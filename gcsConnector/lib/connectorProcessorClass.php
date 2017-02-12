<?php
include_once 'lib/httpClientClass.php';
include_once 'lib/configClass.php';

Class connectorProcessor {
    private $transactionInfo = Array ("bank" => "",
                                      "accountnumber" => "",
                                      "phonenumber" => "",
                                      "transactiontype" => ""); 
    private $coreApiHeaders = array('Accept: application/json',
                                    'Content-Type: application/json');
    private $coreApiFinantialStructure = array ("id" => "",
                                                "operation" => "",
                                                "phone" => "",
                                                "amount" => "",
                                                "currency" => "",
                                                "reasonCode" => "",
                                                "options" => array ("" => ""),
                                                "origin" => array ("id" => "12345678",
                                                                  "name" => "SYSTEM",
                                                                  "city" => "SYSTEM",
                                                                  "country" => "DO"));
    private $connectorConfig;
    private $connectorHttpResource;
    private $resourceUrl;
    private $originalMessage;
    function __construct() {
        $this->connectorConfig = new configLoader('config/connector.json');
        $this->connectorHttpResource = new httpClient();
        $this->resourceUrl = "http://".$this->connectorConfig->structure['coreip'].":".$this->connectorConfig->structure['coreport'];
    }
    
//Private functions ------------------------------------------------------------
    private function parseIncomingMessage($vMessage) {
        $domMessage = new DOMDocument;
        $domMessage->loadXML($vMessage);
        if(!$domMessage){
            return false;
        }else {
            return simplexml_import_dom($domMessage);
        }
    }
    
    private function setURLCoreApi($vFunction) {
        $resultUrl = $this->resourceUrl;
        switch ($vFunction) {
            case "financial":
                $resultUrl .= "/cardholder/".$this->transactionInfo['bank']."/".$this->transactionInfo['accountnumber']."/financial";
                break;
            case "phones":
                $resultUrl .= "/cardholder/".$this->transactionInfo['bank']."/".$this->transactionInfo['accountnumber']."/phones";
                break;
            case "view":
                $resultUrl .= "/cardholder/".$this->transactionInfo['bank']."/".$this->transactionInfo['accountnumber'];
                break;
            default:
                break;
        }
        return $resultUrl;
    }
    
    private function getTransactionInfo(){
        $this->transactionInfo['bank'] = $this->connectorConfig->structure['bankcodes'][(string) $this->originalMessage['BANKID']];
        $this->transactionInfo['accountnumber'] = $this->connectorConfig->structure['accounttypes'][(string) $this->originalMessage->TRANSACTION['TYPE']].$this->originalMessage->CLIENT['ID'];        
    }
    
    private function getPhoneNumber() {
        $this->connectorHttpResource->setURL($this->setURLCoreApi('phones'));
        $result = json_decode($this->connectorHttpResource->httpRequest('GET', $this->coreApiHeaders),true);
        return $result[0];
    }
    
    private function msg100() {
        $this->coreApiFinantialStructure["id"] = (string) $this->originalMessage["CORRELATIONID"];
        $this->coreApiFinantialStructure["operation"] = "DEBIT";
        $this->coreApiFinantialStructure["phone"] = (string) $this->getPhoneNumber();
        $this->coreApiFinantialStructure["amount"] = (string) $this->originalMessage->TRANSACTION["AMOUNT"];
        $this->coreApiFinantialStructure["currency"] = (string) $this->originalMessage->TRANSACTION["CURRENCY"];
        $this->coreApiFinantialStructure["reasonCode"] = (string) $this->originalMessage->TRANSACTION["SUBTRANSACTIONTYPE"];
        
        $this->connectorHttpResource->setURL($this->setURLCoreApi('financial'));
        $coreApiResult = json_decode($this->connectorHttpResource->httpRequest('POST', $this->coreApiHeaders, json_encode($this->coreApiFinantialStructure)),true);

        $this->originalMessage["TYPE"] = "110";
        if (array_key_exists('error',$coreApiResult)){
            $this->originalMessage->TRANSACTION["RESPONSECODE"] = $coreApiResult["error"]["code"];
        }else {
            $this->originalMessage->TRANSACTION["RESPONSECODE"] = "0000";
        }
        $this->originalMessage->TRANSACTION["BPSEQUENCE"] = str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    }
    
    private function msg400() {
        $this->coreApiFinantialStructure['id'] = (string) $this->originalMessage['CORRELATIONID'];
        $this->coreApiFinantialStructure['operation'] = "CREDIT";
        $this->coreApiFinantialStructure['phone'] = (string) $this->getPhoneNumber();
        $this->coreApiFinantialStructure['amount'] = (string) $this->originalMessage->TRANSACTION['AMOUNT'];
        $this->coreApiFinantialStructure['currency'] = (string) $this->originalMessage->TRANSACTION['CURRENCY'];
        $this->coreApiFinantialStructure['reasonCode'] = (string) $this->originalMessage->TRANSACTION['SUBTRANSACTIONTYPE'];

        $this->connectorHttpResource->setURL($this->setURLCoreApi('financial'));
        $coreApiResult = json_decode($this->connectorHttpResource->httpRequest('POST', $this->coreApiHeaders, json_encode($this->coreApiFinantialStructure)),true);

        $this->originalMessage['TYPE'] = '410';
        if (array_key_exists('error',$coreApiResult)){
            $this->originalMessage->TRANSACTION['RESPONSECODE'] = $coreApiResult['error']['code'];
        }else {
            $this->originalMessage->TRANSACTION['RESPONSECODE'] = '0000';
        }
        $this->originalMessage->TRANSACTION['BPSEQUENCE'] = str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    }
    
    function msg500() {
        $this->connectorHttpResource->setURL($this->setURLCoreApi('view'));
        $coreApiResult = json_decode($this->connectorHttpResource->httpRequest('GET', $this->coreApiHeaders),true);
        
        $this->originalMessage["TYPE"] = "510";
        if (array_key_exists('error',$coreApiResult)){
            $this->originalMessage->TRANSACTION["RESPONSECODE"] = $coreApiResult["error"]["code"];
        }else {
            $this->originalMessage->TRANSACTION["RESPONSECODE"] = "0000";
            $this->originalMessage->TRANSACTION["AMOUNT"] = $coreApiResult['balance']['available'];
            $this->originalMessage->TRANSACTION["CURRENTBALANCE"] = $coreApiResult['balance']['available'];
            $this->originalMessage->TRANSACTION["DUEPAYMENT"] = $coreApiResult['balance']['available']*15/100;
            $this->originalMessage->TRANSACTION["PAYOFFAMOUNT"] = $coreApiResult['balance']['available'];
            $this->originalMessage->TRANSACTION["MINPAYMENT"] = $coreApiResult['balance']['available']*3/100;;
            $this->originalMessage->TRANSACTION["DUEDATE"] = "";
        }
        $this->originalMessage->TRANSACTION["BPSEQUENCE"] = str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    }
    
    
//Public functions -------------------------------------------------------------
    public function process($vMessage) {
        $this->originalMessage = $this->parseIncomingMessage($vMessage);
        $this->getTransactionInfo();
        switch ($this->originalMessage['TYPE']) {
            case '100':
                $this->msg100();
                break;
            case '400':
                $this->msg400();
                break;
            case '500':
                $this->msg500();
                break;
            case '600':
                break;
            case '700':
                break;
            case '800':
                break;
        }
        return $this->originalMessage->asXML();
    }
    
}

/* Functions definitions ------------------------------------------------------






function msg815($input){

    $tmp = simplexml_import_dom($input);
    //Format response message
    $tmp["TYPE"]="816";
    ////Error condition on security code = 1234 / return 9899 general error
    if($tmp->TRANSACTION["SECURITYCODE"]=="1234"){
        $tmp->TRANSACTION["RESPONSECODE"]="9899";
    }else {
    $tmp->TRANSACTION["RESPONSECODE"]="0000";
    }
    $bpsequence=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    $tmp->TRANSACTION["BPSEQUENCE"]=$bpsequence;
    return $tmp;

}

function msg920($input){

    $tmp = simplexml_import_dom($input);
    //Format response message
    $tmp["TYPE"]="925";
    ////Error condition on security code = 1234 / return 9899 general error
    $tmp->TRANSACTION["RESPONSECODE"]="0000";
    $bpsequence=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    $tmp->TRANSACTION["BPSEQUENCE"]=$bpsequence;
    return $tmp;

}

function msg300($input){

    $tmp = simplexml_import_dom($input);

    //Format response message
    $tmp["TYPE"]="310";
    $tmp->TRANSACTION["NAME"]="RHONNY ESTEVEZ";
    $tmp->TRANSACTION["TYPE"]="SAV";
    $tmp->TRANSACTION["CURRENCY"]="DOP";
    $tmp->TRANSACTION["VALID-THRU"]="";
    if($tmp->TRANSACTION["ACCOUNT"]==""){
        $tmp->TRANSACTION["RESPONSECODE"]="9899";
    }else {
    $tmp->TRANSACTION["RESPONSECODE"]="0000";
    }
    $bpsequence=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    $tmp->TRANSACTION["BPSEQUENCE"]=$bpsequence;
    return $tmp;

}*/