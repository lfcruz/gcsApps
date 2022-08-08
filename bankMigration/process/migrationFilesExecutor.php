<?php
echo "Starting Scotiabank migration file migrationFile-".$argv[1]."............\n";
sleep(30);
define("E_TCP","Empty response or timeout received from socket.");
define("E_RESPONSECODE","An error code was received from processor.");
$handlerMigrator = fopen('migrationFile-'.$argv[1], 'r');
$dataMigrator = null;
$dataResponse = null;
$tcpResult = null;

while (($dataMigrator = fgets($handlerMigrator)) !== FALSE) {
    try {
        echo "++++++++++++++++++++++++++++++++++++++++++++++++++++\n\n\n";
        echo "Outgoing --> ".$dataMigrator."\n";
        echo "++++++++++++++++++++++++++++++++++++++++++++++++++++\n";
        $domOut = new DOMDocument;
        $domOut->loadXML($dataMigrator);
        $messageOut = simplexml_import_dom($domOut);
        
        $handlerSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_connect($handlerSocket, '10.225.193.110', 8888);
        $tcpResult = socket_write($handlerSocket, substr($messageOut->asXML(),22,strlen($messageOut->asXML())), strlen($messageOut->asXML()));
        $dataResponse = socket_read($handlerSocket, 2048);
        if(!$dataResponse){ 
            throw new Exception(E_TCP);
        }
        
        echo "++++++++++++++++++++++++++++++++++++++++++++++++++++\n";
        echo "Incoming <-- ".$dataResponse."\n";
        echo "++++++++++++++++++++++++++++++++++++++++++++++++++++\n";
        $domIn = new DOMDocument;
        $domIn->loadXML($dataResponse);
        $messageIn = simplexml_import_dom($domIn);
        if($messageIn->TRANSACTION["RESPONSECODE"] <> '0000'){
            throw new Exception(E_RESPONSECODE);
        }
        
        unset($domOut);
        unset($messageOut);
        unset($domIn);
        unset($messageIn);
        unset($handlerSocket);
        
    }catch(Exception $e) {
        echo "----------------------------------------------------\n";
        echo "Error Code: ".$e->getCode()."\n";
        echo "On: ".$e->getFile()." - [".$e->getLine()."]\n";
        echo "Error Message: ".$e->getMessage()."\n";
        echo "Stack Trace: ".$e->getTraceAsString()."\n";
        echo "----------------------------------------------------\n\n\n";
    }
   
}
fclose($handlerMigrator);
