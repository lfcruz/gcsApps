<?php
include_once "gcsObjectHandlerClass.php";  
include_once "constants.php";
include_once "dbClass.php";
include_once "configClass.php";
class coreRequest {
    private $httpRequest;
    private $conf;
    private $dbLinkRequest;
    
    function __construct($vRequest){
        $this->httpRequest = $vRequest;
        $this->conf = new configLoader('../config/db.json');
        $this->dbLinkRequest = new dbRequest($this->conf->structure['dbtype'], $this->conf->structure['dbhost'], $this->conf->structure['dbport'], $this->conf->structure['dbname'], $this->conf->structure['dbuser'], $this->conf->structure['dbpass']);
    }
    
    public function process (){
        $httpResponse = $this->generateResponse(E_INTERNAL);
        switch ($this->httpRequest["method"]){
            case HTTP_POST:
                switch ($this->httpRequest[0]){
                    case "credit-card":
                        $httpResponse = $this->getbin($this->httpRequest['body']);
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
        $this->dbLinkRequest->setQuery('select * from error_codes where error_code = $1', Array($vErrorCode));
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
    
    private function securityValidation(){
        return;
    }
    
    private function getbin($vCCData){
        $ccAccount = new gcsCreditCards($vCCData);
        return (!empty($ccAccount->ccBinNumber))? $this->generateResponse(PROC_OK,Array("account-number" => $ccAccount->ccBinNumber)) : $this->generateResponse(E_CC_ACCOUNT_INVALID);
    }
}
