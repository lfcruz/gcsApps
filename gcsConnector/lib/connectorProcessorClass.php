<?php
include_once 'lib/httpClientClass.php';
include_once 'lib/configClass.php';
include_once 'lib/dbClass.php';

Class connectorProcessor {
    private $transactionInfo = Array ("bank" => "",
                                      "accountnumber" => "",
                                      "phonenumber" => "",
                                      "transactiontype" => ""); 
    private $coreApiHeaders = Array('Accept: application/json',
                                    'Content-Type: application/json');
    private $coreApiFinantialStructure = Array ("id" => "",
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
    private $coreApiCustomerStructure = Array();
    
    private $connectorConfig;
    private $connectorHttpResource;
    private $connectorDbResource;
    private $resourceUrl;
    private $originalMessage;
    
//Constructor functions --------------------------------------------------------    
    function __construct() {
        $this->connectorConfig = new configLoader('config/connector.json');
        $this->connectorHttpResource = new httpClient();
        $this->connectorDbResource = new dbRequest($this->connectorConfig->structure['dbtype'], $this->connectorConfig->structure['dbhost'], $this->connectorConfig->structure['dbport'], $this->connectorConfig->structure['dbname'], $this->connectorConfig->structure['dbuser'], $this->connectorConfig->structure['dbpass']);
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
        if($this->originalMessage->CLIENT['ID'] == 'RNC') {
            $this->transactionInfo['accountnumber'] = "R".$this->originalMessage->CLIENT['ID'];
        }else {
            $this->transactionInfo['accountnumber'] = $this->connectorConfig->structure['accounttypes'][(string) $this->originalMessage->TRANSACTION['TYPE']].$this->originalMessage->CLIENT['ID'];
        }
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
    
    private function msg111(){
        $this->coreApiFinantialStructure["id"] = (string) $this->originalMessage["CORRELATIONID"];
        $this->coreApiFinantialStructure["operation"] = "DEBIT";
        $this->coreApiFinantialStructure["phone"] = (string) $this->getPhoneNumber();
        $this->coreApiFinantialStructure["amount"] = (string) $this->originalMessage->TRANSACTION["AMOUNT"];
        $this->coreApiFinantialStructure["currency"] = (string) $this->originalMessage->TRANSACTION["CURRENCY"];
        $this->coreApiFinantialStructure["reasonCode"] = (string) $this->originalMessage->TRANSACTION["SUBTRANSACTIONTYPE"];
        
        $this->connectorHttpResource->setURL($this->setURLCoreApi('financial'));
        $coreApiResult = json_decode($this->connectorHttpResource->httpRequest('POST', $this->coreApiHeaders, json_encode($this->coreApiFinantialStructure)),true);
        
        $this->originalMessage["TYPE"]="112";
        if (array_key_exists('error',$coreApiResult)){
            $this->originalMessage->TRANSACTION["RESPONSECODE"] = $coreApiResult["error"]["code"];
        }else {
            $this->originalMessage->TRANSACTION["RESPONSECODE"] = "0000";
        }
        $this->originalMessage->TRANSACTION["BPSEQUENCE"] = str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    }
    
    private function msg300(){
        $tmp["TYPE"]="310";
        if($this->originalMessage->TRANSACTION["ACCOUNT"] == ""){
            $this->originalMessage->TRANSACTION["RESPONSECODE"]="9899";
        }else {
            $tmp->TRANSACTION["RESPONSECODE"]="0000";
            $this->originalMessage->TRANSACTION["NAME"] = "NELSON MANDELA";
            $this->originalMessage->TRANSACTION["TYPE"]="DDA";
            $this->originalMessage->TRANSACTION["CURRENCY"]="DOP";
            $this->originalMessage->TRANSACTION["VALID-THRU"]="";
        }
        $this->originalMessage->TRANSACTION["BPSEQUENCE"] = str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    }
    
    private function msg320(){
        $this->originalMessage["TYPE"]="321";
        $this->originalMessage->TRANSACTION["COMPANYID"]="15";
        $this->originalMessage->TRANSACTION["COMPANYNAME"]="AVON";
        $this->originalMessage->TRANSACTION["VENDORNAME"]="Yoselin De los Santos";
        $this->originalMessage->TRANSACTION["RESPONSECODE"]="0000";
        $this->originalMessage->TRANSACTION["BPSEQUENCE"]= str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
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
    
    private function msg415(){
        $this->originalMessage["TYPE"]="416";
            if($this->originalMessage->TRANSACTION["AMOUNT"]=="350.00"){
                $this->originalMessage->TRANSACTION["RESPONSECODE"]="9850";
            } else {
                $this->originalMessage->TRANSACTION["RESPONSECODE"]="0000"; 
            }
        $this->originalMessage->TRANSACTION["TYPE"]="02";
        $this->originalMessage->TRANSACTION["AUTHNUMBER"]="2147";
        $this->originalMessage->TRANSACTION["BPSEQUENCE"] = str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    }

    private function msg500() {
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

//Mensaje Consulta Balance Vendedor Citi    
    private function msg515(){
        $this->originalMessage["TYPE"]="516";
        $this->originalMessage->TRANSACTION["CURRENCY"]="DOP";
        $this->originalMessage->TRANSACTION["CURRENTBALANCE"]="2479.0000";
        $this->originalMessage->TRANSACTION["RESPONSECODE"]="0000";
        $this->originalMessage->TRANSACTION["BPSEQUENCE"] = str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);;
    }

//Mensaje Consulta Historial Vendedor Citi    
    private function msg517(){
        $this->originalMessage["TYPE"]="518";
        $this->originalMessage->TRANSACTION["CURRENCY"]="DOP";
        $this->originalMessage->TRANSACTION["CURRENTBALANCE"]="";
        $this->originalMessage->TRANSACTION["RESPONSECODE"]="0000";
        $this->originalMessage->TRANSACTION["BPSEQUENCE"] = str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    }
    
    private function msg540(){
        $this->originalMessage["TYPE"]="545";
        switch ($this->originalMessage["BANKID"]) {
            case "BDI":
		$this->originalMessage->TRANSACTION["ACCOUNT"]="5424180279791732";
		$this->originalMessage->TRANSACTION["EXPDATE"]="04/16";
		break;
            case "ADO":
		$this->originalMessage->TRANSACTION["ACCOUNT"]="4761340000000043";
                $this->originalMessage->TRANSACTION["EXPDATE"]="12/17";
                break;
            case "BDP":
                $this->originalMessage->TRANSACTION["ACCOUNT"]="541950001998048";
                //$this->originalMessage->TRANSACTION["ACCOUNT"]="4509750047107304";
                $this->originalMessage->TRANSACTION["EXPDATE"]="08/14";
                //$this->originalMessage->TRANSACTION["ACCOUNT"]="377880301750014";
                //$this->originalMessage->TRANSACTION["EXPDATE"]="05/16";
                break;
            default:
		// Tarjetas Ambiente pruebas FDR
                    // Tarjeta Visa
                        $this->originalMessage->TRANSACTION["ACCOUNT"]="4012000033330026";
                        $this->originalMessage->TRANSACTION["EXPDATE"]="04/16";
                
                    //Tarjeta Mastercard
                        //$this->originalMessage->TRANSACTION["ACCOUNT"]="5424180279791732";
                        //$this->originalMessage->TRANSACTION["EXPDATE"]="10/20";
                
		//Tarjetas Ambiente pruebas Progreso
                    //Tarjeta Amex
                        //$this->originalMessage->TRANSACTION["ACCOUNT"]="377883875670642";
                        //$this->originalMessage->TRANSACTION["ACCOUNT"]="377881913435648";
                        //$this->originalMessage->TRANSACTION["EXPDATE"]="07/20";
		
                //Tarjeta Ambiente prueba Cardnet
                    //Tarjeta Visa
                        //$this->originalMessage->TRANSACTION["ACCOUNT"]="4761340000000043";
                        //$this->originalMessage->TRANSACTION["EXPDATE"]="12/17";
                    break;
        }
        $this->originalMessage->TRANSACTION["RESPONSECODE"]="0000";
        $this->originalMessage->TRANSACTION["BPSEQUENCE"] = str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    }
 
//PIN validation
    private function msg815(){
        $this->originalMessage["TYPE"]="816";
        ////Error condition on security code = 1234 / return 9899 general error
        if($this->originalMessage->TRANSACTION["SECURITYCODE"]=="1234"){
            $this->originalMessage->TRANSACTION["RESPONSECODE"]="9899";
        }else {
            $this->originalMessage->TRANSACTION["RESPONSECODE"]="0000";
        }
        $this->originalMessage->TRANSACTION["BPSEQUENCE"] = str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    }
    
//Customer profile
    private function msg879(){
        $this->connectorHttpResource->setURL($this->setURLCoreApi('view'));
        $this->connectorDbResource->setQuery('select from v_clients_profile where document_number=$1 and document_type=$2 and bank_code=$3', Array($this->originalMessage->CLIENT['ID'], substr($this->originalMessage->CLIENT['TYPE'], 0, 1)), $this->transactionInfo['bank']);
        $gcsConnectorStructure = $this->connectorDbResource->execQry();
        foreach ($gcsConnectorStructure as $accountKey => $accountData) {
            
            $coreApiResult = json_decode($this->connectorHttpResource->httpRequest('GET', $this->coreApiHeaders),true);
            
        }
        
        
        
    
    $tmp = simplexml_import_dom($input);
    //Format response message 
    $tmp["TYPE"]="880";
    $tmp->CLIENT["FULL-NAME"]="JOHN DOE SIMULADO";
    $tmp->CLIENT["ADDRESS"]="26 ESTE, ESQ P SECTOR LA CASTELLANA";
    $tmp->CLIENT["STATUS"]="";
    $tmp->CLIENT["CITY"]="SANTO DOMINGO ESTE SD";
    $tmp->CLIENT["PHONE"]="809-549-5717";
    $tmp->CLIENT["SEGMENT"]="2-Banca Premium";
    $tmp->CLIENT["OFFICER-CODE"]="U15682";
    $tmp->CLIENT["OFFICER-NAME"]="IRIS LUGO";
    $tmp->CLIENT["EMAIL"]="";
    $tmp->TRANSACTION["BANKSESSIONID"]='BDP'.str_pad(rand(0,99999999999999999999), 20, "0", STR_PAD_LEFT);;
    //Error condition on amount = 330 / return 9902
    if($tmp->CLIENT["ID"]=="22500581032" or $tmp->CLIENT["ID"]=="22500581032"){
        $tmp->TRANSACTION["RESPONSECODE"]="9945";
        
        $tmp->TRANSACTION["BPSEQUENCE"]=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
        $tmp->ACCOUNTS->ACCOUNT[0]["BANK"]=$tmp["BANKID"];
        $tmp->ACCOUNTS->ACCOUNT[0]["BANK-NAME"]="Banco GCS";
        $tmp->ACCOUNTS->ACCOUNT[0]["ALIAS"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["NUMBER"]="*****6326";
        $tmp->ACCOUNTS->ACCOUNT[0]["TYPE"]="DDA";
        $tmp->ACCOUNTS->ACCOUNT[0]["CURRENCY"]="DOP";
        $tmp->ACCOUNTS->ACCOUNT[0]["STATUS"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["RELATION"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["BALANCE"]="825.00";
        $tmp->ACCOUNTS->ACCOUNT[0]["AVAILABLE-BALANCE"]="825.00";


    }
    elseif($tmp->CLIENT["ID"]=="22500581263"){
        $tmp->TRANSACTION["RESPONSECODE"]="0000";

        $tmp->TRANSACTION["BPSEQUENCE"]=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
        $tmp->ACCOUNTS->ACCOUNT[0]["BANK"]=$tmp["BANKID"];
        $tmp->ACCOUNTS->ACCOUNT[0]["BANK-NAME"]="Banco GCS";
        $tmp->ACCOUNTS->ACCOUNT[0]["ALIAS"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["NUMBER"]="*****6326";
        $tmp->ACCOUNTS->ACCOUNT[0]["TYPE"]="SAV";
        $tmp->ACCOUNTS->ACCOUNT[0]["CURRENCY"]="DOP";
        $tmp->ACCOUNTS->ACCOUNT[0]["STATUS"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["RELATION"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["BALANCE"]="0.00";
        $tmp->ACCOUNTS->ACCOUNT[0]["AVAILABLE-BALANCE"]="0.00";


    }

elseif($tmp->CLIENT["ID"]=="22500582055"){
        $tmp->TRANSACTION["RESPONSECODE"]="0000";

        $tmp->TRANSACTION["BPSEQUENCE"]=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
        $tmp->ACCOUNTS->ACCOUNT[0]["BANK"]=$tmp["BANKID"];
        $tmp->ACCOUNTS->ACCOUNT[0]["BANK-NAME"]="Banco GCS";
        $tmp->ACCOUNTS->ACCOUNT[0]["ALIAS"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["NUMBER"]="*****6326";
        $tmp->ACCOUNTS->ACCOUNT[0]["TYPE"]="SAV";
        $tmp->ACCOUNTS->ACCOUNT[0]["CURRENCY"]="DOP";
        $tmp->ACCOUNTS->ACCOUNT[0]["STATUS"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["RELATION"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["BALANCE"]="0.00";
        $tmp->ACCOUNTS->ACCOUNT[0]["AVAILABLE-BALANCE"]="0.00";


    }


    else {
    	$tmp->TRANSACTION["RESPONSECODE"]="0000";
    
    	$tmp->TRANSACTION["BPSEQUENCE"]=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    	$tmp->ACCOUNTS->ACCOUNT[0]["BANK"]=$tmp["BANKID"];
    	$tmp->ACCOUNTS->ACCOUNT[0]["BANK-NAME"]="Banco GCS";
    	$tmp->ACCOUNTS->ACCOUNT[0]["ALIAS"]="";
    	$tmp->ACCOUNTS->ACCOUNT[0]["NUMBER"]="*****6326";
    	$tmp->ACCOUNTS->ACCOUNT[0]["TYPE"]="LOAN";
    	$tmp->ACCOUNTS->ACCOUNT[0]["CURRENCY"]="DOP";
    	$tmp->ACCOUNTS->ACCOUNT[0]["STATUS"]="";
    	$tmp->ACCOUNTS->ACCOUNT[0]["RELATION"]="";
    	$tmp->ACCOUNTS->ACCOUNT[0]["BALANCE"]="10811.00";
    	$tmp->ACCOUNTS->ACCOUNT[0]["AVAILABLE-BALANCE"]="0.00";
    
    	$tmp->ACCOUNTS->ACCOUNT[1]["BANK"]=$tmp["BANKID"];
    	$tmp->ACCOUNTS->ACCOUNT[1]["BANK-NAME"]="Banco GCS";
    	$tmp->ACCOUNTS->ACCOUNT[1]["ALIAS"]="";
    	$tmp->ACCOUNTS->ACCOUNT[1]["NUMBER"]="************6501";
    	$tmp->ACCOUNTS->ACCOUNT[1]["TYPE"]="CC";
    	$tmp->ACCOUNTS->ACCOUNT[1]["CURRENCY"]="DOP";
    	$tmp->ACCOUNTS->ACCOUNT[1]["STATUS"]="";
    	$tmp->ACCOUNTS->ACCOUNT[1]["RELATION"]="";
    	$tmp->ACCOUNTS->ACCOUNT[1]["BALANCE"]="16244.54";
    	$tmp->ACCOUNTS->ACCOUNT[1]["AVAILABLE-BALANCE"]="13753.46";
    
	$tmp->ACCOUNTS->ACCOUNT[2]["BANK"]=$tmp["BANKID"];
        $tmp->ACCOUNTS->ACCOUNT[2]["BANK-NAME"]="Banco GCS";
        $tmp->ACCOUNTS->ACCOUNT[2]["ALIAS"]="";
        $tmp->ACCOUNTS->ACCOUNT[2]["NUMBER"]="************5528";
        $tmp->ACCOUNTS->ACCOUNT[2]["TYPE"]="SAV";
        $tmp->ACCOUNTS->ACCOUNT[2]["CURRENCY"]="DOP";
        $tmp->ACCOUNTS->ACCOUNT[2]["STATUS"]="";
        $tmp->ACCOUNTS->ACCOUNT[2]["RELATION"]="";
        $tmp->ACCOUNTS->ACCOUNT[2]["BALANCE"]="16244.54";
        $tmp->ACCOUNTS->ACCOUNT[2]["AVAILABLE-BALANCE"]="13753.46";

    	$tmp->ACCOUNTS->ACCOUNT[3]["BANK"]=$tmp["BANKID"];
    	$tmp->ACCOUNTS->ACCOUNT[3]["BANK-NAME"]="Banco GCS";
    	$tmp->ACCOUNTS->ACCOUNT[3]["ALIAS"]="";
    	$tmp->ACCOUNTS->ACCOUNT[3]["NUMBER"]="*****2487";
    	$tmp->ACCOUNTS->ACCOUNT[3]["TYPE"]="DDA";
    	$tmp->ACCOUNTS->ACCOUNT[3]["CURRENCY"]="DOP";
    	$tmp->ACCOUNTS->ACCOUNT[3]["STATUS"]="";
    	$tmp->ACCOUNTS->ACCOUNT[3]["RELATION"]="";
    	$tmp->ACCOUNTS->ACCOUNT[3]["BALANCE"]="23456.55";
    	$tmp->ACCOUNTS->ACCOUNT[3]["AVAILABLE-BALANCE"]="23456.55";

    	/*$tmp->ACCOUNTS->ACCOUNT[4]["BANK"]=$tmp["BANKID"];
    	$tmp->ACCOUNTS->ACCOUNT[4]["BANK-NAME"]="Banco". $tmp["BANKID"];
    	$tmp->ACCOUNTS->ACCOUNT[4]["ALIAS"]="";
    	$tmp->ACCOUNTS->ACCOUNT[4]["NUMBER"]="*****2590";
    	$tmp->ACCOUNTS->ACCOUNT[4]["TYPE"]="DDA";
    	$tmp->ACCOUNTS->ACCOUNT[4]["CURRENCY"]="DOP";
    	$tmp->ACCOUNTS->ACCOUNT[4]["STATUS"]="";
    	$tmp->ACCOUNTS->ACCOUNT[4]["RELATION"]="";
    	$tmp->ACCOUNTS->ACCOUNT[4]["BALANCE"]="5234.00";
    	$tmp->ACCOUNTS->ACCOUNT[4]["AVAILABLE-BALANCE"]="5034.00";

        $tmp->ACCOUNTS->ACCOUNT[5]["BANK"]=$tmp["BANKID"];
        $tmp->ACCOUNTS->ACCOUNT[5]["BANK-NAME"]="Banco ". $tmp["BANKID"];
        $tmp->ACCOUNTS->ACCOUNT[5]["ALIAS"]="";
        $tmp->ACCOUNTS->ACCOUNT[5]["NUMBER"]="*****7386";
        $tmp->ACCOUNTS->ACCOUNT[5]["TYPE"]="LOAN";
        $tmp->ACCOUNTS->ACCOUNT[5]["CURRENCY"]="DOP";
        $tmp->ACCOUNTS->ACCOUNT[5]["STATUS"]="";
        $tmp->ACCOUNTS->ACCOUNT[5]["RELATION"]="";
        $tmp->ACCOUNTS->ACCOUNT[5]["BALANCE"]="1811.00";
        $tmp->ACCOUNTS->ACCOUNT[5]["AVAILABLE-BALANCE"]="0.00";

        $tmp->ACCOUNTS->ACCOUNT[6]["BANK"]=$tmp["BANKID"];
        $tmp->ACCOUNTS->ACCOUNT[6]["BANK-NAME"]="Banco". $tmp["BANKID"];
        $tmp->ACCOUNTS->ACCOUNT[6]["ALIAS"]="";
        $tmp->ACCOUNTS->ACCOUNT[6]["NUMBER"]="************2801";
        $tmp->ACCOUNTS->ACCOUNT[6]["TYPE"]="CC";
        $tmp->ACCOUNTS->ACCOUNT[6]["CURRENCY"]="DOP";
        $tmp->ACCOUNTS->ACCOUNT[6]["STATUS"]="";
        $tmp->ACCOUNTS->ACCOUNT[6]["RELATION"]="";
        $tmp->ACCOUNTS->ACCOUNT[6]["BALANCE"]="19204.54";
        $tmp->ACCOUNTS->ACCOUNT[6]["AVAILABLE-BALANCE"]="10713.46";*/
	if($tmp["BANKID"]="BDP"){
		$tmp->ACCOUNTS->ACCOUNT[4]["BANK"]=$tmp["BANKID"];
        	$tmp->ACCOUNTS->ACCOUNT[4]["BANK-NAME"]="Banco GCS";
        	$tmp->ACCOUNTS->ACCOUNT[4]["ALIAS"]="";
        	$tmp->ACCOUNTS->ACCOUNT[4]["NUMBER"]="************5678";
        	$tmp->ACCOUNTS->ACCOUNT[4]["TYPE"]="AMEX";
        	$tmp->ACCOUNTS->ACCOUNT[4]["CURRENCY"]="DOP";
        	$tmp->ACCOUNTS->ACCOUNT[4]["STATUS"]="";
        	$tmp->ACCOUNTS->ACCOUNT[4]["RELATION"]="";
        	$tmp->ACCOUNTS->ACCOUNT[4]["BALANCE"]="19204.54";
        	$tmp->ACCOUNTS->ACCOUNT[4]["AVAILABLE-BALANCE"]="10713.46";

	}

    }
    return $tmp;
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