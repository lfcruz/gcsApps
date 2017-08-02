<?php
include_once "customerClass.php";
include_once "paymentsClass.php";
include_once "constants.php";
include_once "dbClass.php";
include_once "configClass.php";
class coreRequest {
    private $httpRequest;
    private $conf;
    
    function __construct($vRequest){
        $this->httpRequest = $vRequest;
        $this->conf = new configLoader('../config/db.json');
    }
    
    public function process (){
        $httpResponse = $this->generateResponse(E_INTERNAL);
        switch ($this->httpRequest["method"]){
            case "POST":
                switch ($this->httpRequest[0]){
                    case "postpayment":
                        $httpResponse = $this->handlerPayment();
                        break;
                    default:
                        $httpResponse = $this->generateResponse(E_PROCESS);
                        break;
                }
                break;
            case "GET":
                switch ($this->httpRequest[0]){
                    case "getnicinfo":
                        $httpResponse = $this->handlerNicInfo();
                        break;
                    case "getpaymentinfo":
                        $httpResponse = $this->handlerPaymentInfo();
                        break;
                    default:
                        $httpResponse = $this->generateResponse(E_PROCESS);
                        break;
                }
                break;
            default:
                $httpResponse = $this->generateResponse(E_METHOD);
                break;
        }
        return $httpResponse;
        
    }
    
    private function generateResponse($vErrorCode, $vPayload = null){
        $dbConnector = new dbRequest($this->conf->structure['dbtype'], $this->conf->structure['dbhost'], $this->conf->structure['dbport'], $this->conf->structure['dbname'], $this->conf->structure['dbuser'], $this->conf->structure['dbpass']);
        $dbConnector->setQuery("select * from error_codes where error_code = $1", Array($vErrorCode));
        $responseStructure = ["http_rsp_code" => null,"proc_rsp_code" => null,"data" => null];
        $responseStructure["proc_rsp_code"] = $dbConnector->execQry();
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
    
    private function handlerPayment(){
        $paymentEntity = new gdmPayments();
        $customer = new gdmCustomer($this->httpRequest[2], $this->httpRequest[1]);
        $data = (empty($customer->billerinfo)) ? $this->generateResponse(E_INVALID_BILLER) :
            $data = (empty($customer->nicinfo)) ? $data = $this->generateResponse(E_INVALID_NIC): Array();
        if(empty($data)){
            $paymentResult = $customer->applyPayment($this->httpRequest[3]);
            var_dump($paymentResult);
            $data = (is_null($paymentResult)) ? $this->generateResponse(E_INVALID_AMOUNT) : 
                $data = (!$paymentResult) ? $this->generateResponse(E_INTERNAL) :             
                    $data = ($paymentEntity->recordPayment($customer->nicinfo, $customer->billerinfo)) ? $this->generateResponse(PROC_OK,Array("transactionid" => $paymentEntity->virtualid)) : 
                        $data = ($customer->rollbackPayment($this->httpRequest[3])) ? $this->generateResponse(E_INTERNAL) : $this->generateResponse(E_PAYMENT_ERROR);
        }
        return $data;
    } //Done.

    private function handlerNicInfo(){ //Done.
        $customer = new gdmCustomer($this->httpRequest[2], $this->httpRequest[1]);
        $data = (empty($customer->billerinfo)) ? $this->generateResponse(E_INVALID_BILLER) :
            $data = (empty($customer->nicinfo)) ? $data = $this->generateResponse(E_INVALID_NIC):
                $data = (($customer->nicinfo['maxamount'] + $customer->nicinfo['minamount']) == 0.00 ) ? $data = $this->generateResponse(W_NO_PENDING_BILLS) : $this->generateResponse(PROC_OK,$customer->nicinfo);
        return $data;
    }
 
}
