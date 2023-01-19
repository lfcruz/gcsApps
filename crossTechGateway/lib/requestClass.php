<?php
include_once "securityClass.php";
include_once "customerClass.php";
include_once "paymentsClass.php";   
include_once "constants.php";
include_once "dbClass.php";
include_once "configClass.php";
class coreRequest {
    private $httpRequest;
    private $conf;
    private $dbLinkRequest;
    private $secure;
    
    function __construct($vRequest){
         $this->secure = gSecure::class->validateToken($vRequest['authorization'],$vRequest[0],$vRequest[1]);
         $this->httpRequest = $vRequest;
         $this->conf = new configLoader('../config/'.$this->httpRequest[0].'_application.json');
         $this->dbLinkRequest = new dbRequest($this->conf->structure['dbtype'], 
                                              $this->conf->structure['dbhost'], 
                                              $this->conf->structure['dbport'], 
                                              $this->conf->structure['dbname'], 
                                              $this->conf->structure['dbuser'], 
                                              $this->conf->structure['dbpass']);
    }
    
    public function process (){
        $httpResponse = $this->generateResponse(E_INTERNAL);
        if (!($this->secure == E_AUTHORIZATION)){
             switch ($this->httpRequest[0]){
                  case "security":
                       $httpResponse = $this->handlerSecurity($this->httpRequest);
                       break;
                  case "purchase-code":
                       $httpResponse = $this->handlerPurchaseCode($this->httpRequest);
                       break;
                  default:
                       $httpResponse = $this->generateResponse(E_PROCESS);
                       break;
             }
        }else {
             $httpResponse = $this->generateResponse(E_AUTHORIZATION);
        }
        return $httpResponse;
        
    }
    
    private function generateResponse($vErrorCode, $vPayload = null){
        $this->dbLinkRequest->setQuery("select * from error_codes where error_code = $1", Array($vErrorCode));
        $responseStructure = ["http_rsp_code" => null,"proc_rsp_code" => null,"data" => null];
        $procCode = $this->dbLinkRequest->execQry();
        $responseStructure["proc_rsp_code"] = $procCode[0];
        $responseStructure["data"] = $vPayload;
        switch (substr($vErrorCode, 0, 1)){
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
         $secureFunction = new gSecure();
         switch ($this->httpRequest["method"]){
            case HTTP_POST:
                 switch ($this->httpRequest[1]) {
                    case "get-token":
                         $apiResponse = $this->generateResponse(PROC_OK,$secureFunction->getToken());
                         break;
                    case "set-password":
                         $apiResponse = $this->generateResponse(PROC_OK,$secureFunction->setPassword());
                         break;
                    default:
                         $apiResponse = $this->generateResponse(E_PROCESS);
                         break;
                 }
                 $apiResponse = $this->generateResponse(E_METHOD);
                 break;
            case HTTP_GET:
                 switch ($this->httpRequest[1]) {
                    default:
                         $apiResponse = $this->generateResponse(E_PROCESS);
                         break;
                 }
                 break;
            case HTTP_PUT:
                 switch ($this->httpRequest[1]) {
                    default:
                         $apiResponse = $this->generateResponse(E_PROCESS);
                         break;
                 }
                 break;
            case HTTP_DELETE:
                 switch ($this->httpRequest[1]) {
                    default:
                         $apiResponse = $this->generateResponse(E_PROCESS);
                         break;
                 }
                 break;
            default:
                 return $this->generateResponse(E_METHOD);
                 break;
         }
        return $apiResponse;
    }
}
