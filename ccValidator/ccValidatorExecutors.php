<?php
// Defining Constants ------------------------------------------------------
    define ("CONFIG_FILE","conf/ccVConf.json");
    define ("APP_LOG_FILE","logs/ccValidator.log");
    define ("TRX_LOG_FILE","logs/ccTransactions.log");
    define ("MSG_ERROR","ERROR");
    define ("MSG_WARNING","WARNING");
    define ("MSG_INFO","INFO");
    define ("REF_CONFIG_LOAD","LoadConfig");
    define ("REF_CONNECTIONS","StablishConnection");
    define ("REF_SQL","GettingDBRecord");
    define ("TRX_LOG","TrxLog");
    define ("APP_LOG","AppLog");
    define ("QRY_CLIENT","SELECT id FROM gateclients WHERE (clientprimaryip = $1 OR clientsecondaryip = $1) AND clientuserid = $2 AND clientpassword = $3 AND clientstatus = true");
    define ("QRY_CLIENT_NAME","QueryClient");
 // Defining Global Variables --------------------------------------------------
    $configStructure = array(0);
    $dbConnector = null;
    
 // Defining Functions ---------------------------------------------------------   
    function writeLog($logType,$origReference,$logString,$logDest){
        global $configStructure;
        $msgString = date(DATE_RFC822)." ".$logType."[".$origReference."]: ".$logString."\n";
        switch($logDest){
            case TRX_LOG:
                error_log($msgString,3,$configStructure['homeDirectory'].TRX_LOG_FILE);
                break;
            case APP_LOG:
                error_log($msgString,3,$configStructure['homeDirectory'].APP_LOG_FILE);
                break;
            default:
                break;
        }
        
        return true;
    }

    function loadConfig($logPrint){
        global $configStructure;
        $stringfile = "";
        if (file_exists(CONFIG_FILE)){
            $stringfile = file_get_contents(CONFIG_FILE);
            $configStructure = json_decode($stringfile,true);
        }
        elseif ($logPrint) {
            writeLog(MSG_ERROR, REF_CONFIG_LOAD, "Configuration file was not found (".CONFIG_FILE.").",APP_LOG);
        }
        
    }
    
    function openDB($state){
        global $configStructure,$dbConnector;
        
        switch ($state){
            case "On":
                // Connecting to Database --------------------------------------
                $connectorString = "host=".$configStructure["dbHostname"].
                                   " port=".$configStructure["dbPort"].
                                   " dbname=".$configStructure["dbName"].
                                   " user=".$configStructure["dbUser"].
                                   " password=".$configStructure["dbPassword"];
                writeLog(MSG_INFO, REF_CONNECTIONS,"Connecting to database.......... ".$connectorString,APP_LOG);
                $dbConnector = pg_connect($connectorString);
                if (!$dbConnector){
                    writeLog(MSG_ERROR, REF_CONNECTIONS,"An error has occurred connecting to database ".$configStructure["dbName"],APP_LOG);
                }
                else {
                    writeLog(MSG_INFO,REF_CONNECTIONS,"Database connection stablished successfuly.",APP_LOG);
                    pg_prepare($dbConnector,QRY_CLIENT_NAME,QRY_CLIENT);
                }
                break;
            case "Off":
                pg_close($dbConnector);
                writeLog(MSG_INFO,REF_CONNECTIONS,"Database connection closed successfuly.",APP_LOG);
                break;
            default:
                break;
        }
    }
    
    function verifyParameters($requestStructure){
        $paramResult = false;
        if(array_key_exists('bank',$requestStructure)){
            if(array_key_exists('docId',$requestStructure)){
                if(array_key_exists('docNumber',$requestStructure)){
                    if(array_key_exists('ccNumber',$requestStructure)){
                        if(array_key_exists('ccExp',$requestStructure)){
                            if(!empty($requestStructure["bank"]) and !empty($requestStructure["docId"]) and !empty($requestStructure["docNumber"]) and !empty($requestStructure["ccNumber"]) and !empty($requestStructure["ccExp"])){
                                $paramResult = true;
                            }
                        }
                    }
                }
            }
        }
        return $paramResult;
    }
    
    function requestValidation($remAddr, $reqAccept, $reqContentType, $reqUserId, $reqAuth){
        global $dbConnector;
        $validationReturn = false;
        $qryResult = null;
        
        if ($reqAccept === $reqContentType and $reqAccept === "application/json"){
            $qryResult = pg_execute($dbConnector,QRY_CLIENT_NAME,array($remAddr,$reqUserId,$reqAuth));
            $qryData = pg_fetch_row($qryResult);
            if (!$qryResult){
                writeLog(MSG_ERROR, REF_SQL, "Failed to execute client query validation.", APP_LOG);
            }
            elseif ($qryData[0] === null){
                writeLog(MSG_WARNING, REF_SQL, "Invalid request blocked => [$remAddr] - [$reqUserId] - [$reqAuth]", APP_LOG);
            }
            else{
                writeLog(MSG_INFO, REF_SQL, "Valid session registred => [$remAddr] - [$reqUserId] - [$reqAuth]", APP_LOG);
                $validationReturn = true;
            }
        }
        return $validationReturn;
    }
    
    function buildMessages($msgType,$structureData){
    //Example messages for enrollment
    $msg860 = '<MESSAGE TYPE="860" BANKID="102" CORRELATIONID="2012071212545622321UL65d"><CLIENT ID="00111054938" TYPE="Cedula" TELEPHONE="8292147747" TELCOID="200" BPSEQUENCE="111644" /><TRANSACTION DATE="12072012" TIME="125456" /></MESSAGE>';
    $msg800 = '<MESSAGE TYPE="800" BANKID="102" CORRELATIONID="2012071212553631598UL9ee"><CLIENT ID="00111054938" TYPE="Cedula" TELEPHONE="8292147747" TELCOID="200" BPSEQUENCE="111644" /><PRODUCTS><PRODUCT ID="01" ACCOUNT="*****3635" TYPE="DDA" CURRENCY="DOP" ALIAS="BP_DDA" /></PRODUCTS><TRANSACTION DATE="12072012" TIME="125536" /></MESSAGE>';
    $msg950 = '<MESSAGE TYPE="950" BANKID="102" CORRELATIONID="2012071212553631598UL9ee"><CLIENT ID="00111054938" TYPE="Cedula" TELEPHONE="8292147747" BPSEQUENCE="111644" /><TRANSACTION DATE="12072012" TIME="125456" /></MESSAGE>';
    $msg940 = '<MESSAGE TYPE="940" BANKID="102" CORRELATIONID="2012071212545622321UL65d"><CLIENT ID="00111054938" TYPE="Cedula" TELEPHONE="8292147747" BPSEQUENCE="111644" /><TRANSACTION DATE="12072012" TIME="125456" /></MESSAGE>';
    $msg540 = '<MESSAGE TYPE="540" BANKID="102" CORRELATIONID="2012071212545622321UL65d"><CLIENT ID="00111054938" TYPE="Cedula" GCSSEQUENCE="123456" TELEPHONE="8292147747" TOKEN="000000000000000000000000" PINVERIFICATION="0" PINCAPTUREFLAG="0" /><TRANSACTION ACCOUNT="************5424" TYPE="CC" CURRENCY="DOP" DATE="12072012" TIME="121212"/></MESSAGE>';
    switch ($msgType) {
        case '860':
            $dom = new DOMDocument;
            $dom->loadXML($msg860);
            if (!$dom) {
                echo "Error while parsing the message: $msgType";
                exit;
            }
            //complete mesg 860
            $result = simplexml_import_dom($dom);
            $result["BANKID"]=$structureData["bank"];
            $result->CLIENT["ID"]=$structureData["document"];
            if($structureData["docType"] == "CEDULA"){
                $result->CLIENT["TYPE"]="CEDULA";
            }
            else{ 
                $result->CLIENT["TYPE"]="PASAPORTE";
            }
            $result->CLIENT["TELEPHONE"]=$structureData["msisdn"];
            $result->CLIENT["TELCOID"]=$structureData["telco"];
            $result->CLIENT["BPSEQUENCE"]=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
            break;
        
        case '800':
            $dom = new DOMDocument;
            $dom->loadXML($msg800);
            if (!$dom) {
                echo "Error while parsing the message: $msgType";
                exit;
            }
            // complete msg 800
            $result = simplexml_import_dom($dom);
            $result["BANKID"] = $structureData["bank"];
            $result->CLIENT["ID"] = $structureData["document"];
            if($structureData["docType"] == "CEDULA"){
                $result->CLIENT["TYPE"] = "CEDULA";
            }
            else{
                $result->CLIENT["TYPE"] = "PASAPORTE";
            }
            $result->CLIENT["TELEPHONE"] = $structureData["msisdn"];
            $result->CLIENT["TELCOID"] = $structureData["telco"];
            $result->CLIENT["BPSEQUENCE"]=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
            $result->PRODUCTS->PRODUCT[0]["ID"] = '01';
            $result->PRODUCTS->PRODUCT[0]["ACCOUNT"] = $structureData["document"];
            $result->PRODUCTS->PRODUCT[0]["TYPE"] = $structureData["accountType"];
            $result->PRODUCTS->PRODUCT[0]["CURRENCY"] = "DOP";
            $result->PRODUCTS->PRODUCT[0]["ALIAS"] = $structureData["alias"];
            break;

        case '950':
            $dom = new DOMDocument;
            $dom->loadXML($msg950);
            if (!$dom) {
                echo "Error while parsing the message: $msgType";
                exit;
            }
            //complete mesg 950
            $result = simplexml_import_dom($dom);
            $result["BANKID"]=$structureData["bank"];
            $result->CLIENT["ID"]=$structureData["document"];
            if($structureData["docType"] == "CEDULA"){
                $result->CLIENT["TYPE"]="CEDULA";
            }
            else{ 
                $result->CLIENT["TYPE"]="PASAPORTE";
            }
            $result->CLIENT["TELEPHONE"]=$structureData["msisdn"];
            $result->CLIENT["BPSEQUENCE"]=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
            break;
        
        case '940':
            $dom = new DOMDocument;
            $dom->loadXML($msg940);
            if (!$dom) {
                echo "Error while parsing the message: $msgType";
                exit;
            }
            //complete mesg 940
            $result = simplexml_import_dom($dom);
            $result["BANKID"]=$structureData["bank"];
            $result->CLIENT["ID"]=$structureData["document"];
            if($structureData["docType"] == "CEDULA"){
                $result->CLIENT["TYPE"]="CEDULA";
            }
            else{ 
                $result->CLIENT["TYPE"]="PASAPORTE";
            }
            $result->CLIENT["TELEPHONE"]=$structureData["msisdn"];
            $result->CLIENT["BPSEQUENCE"]=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
            break;

        case '540':
            $dom = new DOMDocument;
            $dom->loadXML($msg540);
            if (!$dom) {
                echo "Error while parsing the message: $msgType";
                exit;
            }
            //complete mesg 540
            $result = simplexml_import_dom($dom);
            $result["BANKID"]=$structureData["bank"];
            $result->CLIENT["ID"]=$structureData["docNumber"];
            if($structureData["docId"] == "CEDULA"){
                $result->CLIENT["TYPE"]="CEDULA";
            }
            else{ 
                $result->CLIENT["TYPE"]="PASAPORTE";
            }
            $result->CLIENT["TELEPHONE"]='8091234567';
            $result->CLIENT["GCSSEQUENCE"]=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
            break;
    }
    return $result;
}

    function sentToSocket($enviroment,$port,$msg){
  //create a socket to send message to core
    $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_connect($sock, $enviroment, $port);
    $sent = socket_write($sock, $msg->asXML(), strlen($msg->asXML()));

    //read response message from core
    $input = socket_read($sock, 1024);
    $dom = new DOMDocument;
    $dom->loadXML($input);
    if (!$dom) {
        $result = '9903';
    }
    else{
        
        $result = simplexml_import_dom($dom);
    }
    socket_close($sock);
    return $result;
}

    function verifyCard($requestStructure){
        $verifyResult = false;
        $response = sentToSocket('localhost', '59001', buildMessages('540', $requestStructure));
        if ($response->TRANSACTION["EXPDATE"] == $requestStructure["ccExp"] and $response->TRANSACTION["RESPONSECODE"] == '0000'){
            $verifyResult = true;
        }
        return $verifyResult;
    }

?>
