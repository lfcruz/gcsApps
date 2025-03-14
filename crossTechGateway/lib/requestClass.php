<?php
include_once "securityClass.php";
include_once "purchasecodesClass.php";
include_once "bankagencyClass.php";
include_once "constants.php";
//include_once "dbClass.php";
include_once "configClass.php";
class coreRequest {
    private $httpRequest;
    private $error_table;
    private $secure;
    private $code;
    private $sab;
    
    function __construct($vRequest){
         try {
              $this->secure = new gSecure($vRequest['authorization']);
              $this->httpRequest = $vRequest;
              $this->error_table = new configLoader('../config/api_application.json');
              
              
              //TODO: get token info by method for any module on gateway
              //$tempData = json_decode(base64_decode(urldecode($this->secure->tokenInfo['body'])), true);
              //$this->code = new gCodes($tempData['sub'], $tempData['uid']);
              //$this->sab = new gBankAgency($tempData['sub'], $tempData['uid']);
              //unset($tempData);
              
              
         } catch (Exception $vException){
              echo $vException->getTraceAsString();
              return false;
         }
    }
    
## PRIVATE FUNCTIONS -----------------------------------------------------------
    private function generateResponse($vApiResponse){
        $responseStructure = ["http_rsp_code" => null,"proc_rsp_code" => null,"data" => null];
        $responseStructure["proc_rsp_code"] = $this->error_table->structure['error_codes'][$this->httpRequest[0]][$vApiResponse['error_code']];
        $responseStructure["data"] = $vApiResponse['payload'];
        switch (substr($responseStructure["proc_rsp_code"]["api_code"], 0, 1)){
            case "0":
                $responseStructure["http_rsp_code"] = HTTP_OK;
                break;
            case "9":
                $responseStructure["http_rsp_code"] = HTTP_INVALID;
                break;
            case "8":
                $responseStructure["http_rsp_code"] = HTTP_UNAUTHORIZED;
                break;
            default:
                $responseStructure["http_rsp_code"] = HTTP_ERROR;
                break;
        }
        return $responseStructure;
    }
    
    private function handlerPurchaseCode(){
         $vTokenInfo = $this->secure->tokenInfo['data'];
         $this->code = new gCodes($vTokenInfo['sub'], $vTokenInfo['uid']);
         switch ($this->httpRequest["method"]){
            case HTTP_POST:
                 switch ($this->httpRequest[1]) {
                    case "get-code":
                         $apiResponse = $this->generateResponse($this->code->getCode($this->httpRequest));
                         break;
                    case "set-password":
                         $apiResponse = $this->generateResponse($this->secure->setPassword($this->httpRequest));
                         break;
                    default:
                         $apiResponse = $this->generateResponse(Array("error_code"=>E_PROCESS, "payload"=>""));
                         break;
                 }
                 break;
            case HTTP_GET:
                 switch ($this->httpRequest[1]) {
                    default:
                         $apiResponse = $this->generateResponse(Array("error_code"=>E_PROCESS, "payload"=>""));
                         break;
                 }
                 break;
            case HTTP_PUT:
                 switch ($this->httpRequest[1]) {
                    default:
                         $apiResponse = $this->generateResponse(Array("error_code"=>E_PROCESS, "payload"=>""));
                         break;
                 }
                 break;
            case HTTP_DELETE:
                 switch ($this->httpRequest[1]) {
                    default:
                         $apiResponse = $this->generateResponse(Array("error_code"=>E_PROCESS, "payload"=>""));
                         break;
                 }
                 break;
            default:
                 return $this->generateResponse(Array("error_code"=>E_METHOD, "payload"=>""));
                 break;
         }
        return $apiResponse;

         
         
         
         
        $customer = new gdmCustomer($this->httpRequest[2], $this->httpRequest[1]);
        $data = (empty($customer->billerinfo)) ? $this->generateResponse(E_INVALID_BILLER) :
            $data = (empty($customer->nicinfo)) ? $data = $this->generateResponse(E_INVALID_NIC): null;
        if(is_null($data)){
            if($customer->nicinfo['status'] == 'P'){
                $paymentResult = $customer->applyPayment($this->httpRequest[3]);
                $data = (is_null($paymentResult)) ? $this->generateResponse(E_INVALID_AMOUNT) : 
                    $data = (!$paymentResult) ? $this->generateResponse(E_PAYMENT_ERROR) : $this->generateResponse(PROC_OK,Array("transactionid" => $customer->paymentid));
            }else {
                $data = $this->generateResponse(W_NO_PENDING_BILLS);
            }
        }
        return $data;
    }
    
    private function handlerBankAgency(){
         $vTokenInfo = $this->secure->tokenInfo['data'];
         $this->sab = new gBankAgency($vTokenInfo['sub'], $vTokenInfo['uid']);
         switch ($this->httpRequest["method"]){
            case HTTP_POST:
                 switch ($this->httpRequest[1]) {
                    case "xml":
                         $apiResponse = $this->generateResponse($this->sab->xml($this->httpRequest));
                         break;
                    default:
                         $apiResponse = $this->generateResponse(Array("error_code"=>E_PROCESS, "payload"=>""));
                         break;
                 }
                 break;
            case HTTP_GET:
                 switch ($this->httpRequest[1]) {
                    default:
                         $apiResponse = $this->generateResponse(Array("error_code"=>E_PROCESS, "payload"=>""));
                         break;
                 }
                 break;
            case HTTP_PUT:
                 switch ($this->httpRequest[1]) {
                    default:
                         $apiResponse = $this->generateResponse(Array("error_code"=>E_PROCESS, "payload"=>""));
                         break;
                 }
                 break;
            case HTTP_DELETE:
                 switch ($this->httpRequest[1]) {
                    default:
                         $apiResponse = $this->generateResponse(Array("error_code"=>E_PROCESS, "payload"=>""));
                         break;
                 }
                 break;
            default:
                 return $this->generateResponse(Array("error_code"=>E_METHOD, "payload"=>""));
                 break;
         }
        return $apiResponse;

         
         
         
         
        $customer = new gdmCustomer($this->httpRequest[2], $this->httpRequest[1]);
        $data = (empty($customer->billerinfo)) ? $this->generateResponse(E_INVALID_BILLER) :
            $data = (empty($customer->nicinfo)) ? $data = $this->generateResponse(E_INVALID_NIC): null;
        if(is_null($data)){
            if($customer->nicinfo['status'] == 'P'){
                $paymentResult = $customer->applyPayment($this->httpRequest[3]);
                $data = (is_null($paymentResult)) ? $this->generateResponse(E_INVALID_AMOUNT) : 
                    $data = (!$paymentResult) ? $this->generateResponse(E_PAYMENT_ERROR) : $this->generateResponse(PROC_OK,Array("transactionid" => $customer->paymentid));
            }else {
                $data = $this->generateResponse(W_NO_PENDING_BILLS);
            }
        }
        return $data;
    }

    private function handlerSecurity(){
         
         switch ($this->httpRequest["method"]){
            case HTTP_POST:
                 switch ($this->httpRequest[1]) {
                    case "get-token":
                         $apiResponse = $this->generateResponse($this->secure->getToken($this->httpRequest));
                         break;
                    case "set-password":
                         $apiResponse = $this->generateResponse($this->secure->setPassword($this->httpRequest));
                         break;
                    default:
                         $apiResponse = $this->generateResponse(Array("error_code"=>E_PROCESS, "payload"=>""));
                         break;
                 }
                 break;
            case HTTP_GET:
                 switch ($this->httpRequest[1]) {
                    case "get-jwk":
                         $apiResponse = $this->generateResponse($this->secure->getJWK());
                         break;
                    default:
                         $apiResponse = $this->generateResponse(Array("error_code"=>E_PROCESS, "payload"=>""));
                         break;
                 }
                 break;
            case HTTP_PUT:
                 switch ($this->httpRequest[1]) {
                    default:
                         $apiResponse = $this->generateResponse(Array("error_code"=>E_PROCESS, "payload"=>""));
                         break;
                 }
                 break;
            case HTTP_DELETE:
                 switch ($this->httpRequest[1]) {
                    default:
                         $apiResponse = $this->generateResponse(Array("error_code"=>E_PROCESS, "payload"=>""));
                         break;
                 }
                 break;
            default:
                 return $this->generateResponse(Array("error_code"=>E_METHOD, "payload"=>""));
                 break;
         }
        return $apiResponse;
    }
    
    
    
## PUBLIC FUNCTIONS ------------------------------------------------------------
    public function process (){
        $httpResponse = $this->generateResponse(Array("error_code"=>E_GENERAL, "payload"=>""));
        if ($this->secure->valid or ($this->httpRequest[0] == 'security' and ($this->httpRequest[1] == 'get-token' or $this->httpRequest[1] == 'get-jwk'))){
             switch ($this->httpRequest[0]){
                  case "security":
                       $httpResponse = $this->handlerSecurity($this->httpRequest);
                       break;
                  case "purchase-code":
                       $httpResponse = $this->handlerPurchaseCode($this->httpRequest);
                       break;
                  case "sab":
                       $httpResponse = $this->handlerBankAgency($this->httpRequest);
                  default:
                       $httpResponse = $this->generateResponse(Array("error_code"=>E_PROCESS, "payload"=>""));
                       break;
             }
        }else {
             $httpResponse = $this->generateResponse(Array("error_code"=>E_AUTHORIZATION, "payload"=>""));
        }
        return $httpResponse;
        
    }
    
}
