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
    define ("QRY_CLIENT","SELECT id FROM clients WHERE (clientprimaryip = $1 OR clientsecondaryip = $1) AND clientuserid = $2 AND clientpassword = $3 AND clientstatus = true");
    define ("QRY_CLIENT_NAME","QueryClient");
 // Defining Global Variables --------------------------------------------------
    $configStructure = array(0);
    $dbConnector = null;
    
 // Defining Functions ---------------------------------------------------------   
   function writeLog($logType,$origReference,$logString,$logDest){
        $msgString = date(DATE_RFC822)." ".$logType."[".$origReference."]: ".$logString."\n";
        switch($logDest){
            case TRX_LOG:
                error_log($msgString,3,TRX_LOG_FILE);
                break;
            case APP_LOG:
                error_log($msgString,3,APP_LOG_FILE);
                break;
            default:
                break;
        }
        
        return;
    }

    function loadConfig($logPrint){
        global $configStructure;
        $stringfile = "";
 
        // Configuration file validation ---------------------------------------
        if ($logPrint){
            writeLog(MSG_INFO, REF_CONFIG_LOAD,"Loading configuration file..........",APP_LOG);
        }
        if (file_exists(CONFIG_FILE)){
            $stringfile = file_get_contents(CONFIG_FILE);
        }
        elseif ($logPrint) {
            writeLog(MSG_ERROR, REF_CONFIG_LOAD, "Configuration file was not found (".CONFIG_FILE.").",APP_LOG);
        }
        $configStructure = json_decode($stringfile,true);
        if ($logPrint){
            writeLog(MSG_INFO,REF_CONFIG_LOAD,"Configuration file loaded successfuly.....",APP_LOG);
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
    
    function requestValidation($remAddr, $reqAccept, $reqContentType, $reqUserId, $reqAuth){
        global $dbConnector;
        $validationReturn = false;
        $qryResult = null;
        
        if ($reqAccept === $reqContentType and $reqAccept === "application/vnd.tpago.ccValidator+json"){
            echo ("[$remAddr] - [$reqUserId] - [$reqAuth] \n");
            $qryResult = pg_execute($dbConnector,QRY_CLIENT_NAME,array($remAddr,$reqUserId,$reqAuth));
            if (!$qryResult){
                writeLog(MSG_ERROR, REF_SQL,"Failed to execute client query validation.", APP_LOG);
            }else{
                $validationReturn = true;
            }
        }
        if (!$validationReturn){
            writeLog(MSG_WARNING, REF_SQL,"Invalid request blocked.", APP_LOG);
        }
        return $validationReturn;
    }

?>
