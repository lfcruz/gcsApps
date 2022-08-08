<?php

echo "Starting Scotiabank migration file 800............\n";
sleep(30);
define("E_TCP","Empty response or timeout received from socket.");
define("E_RESPONSECODE","An error code was received from processor.");
$handler860 = fopen($argv[1], 'r');
$handler800 = fopen($argv[2], 'r');
$data860 = null;
$data800 = null;
$dataResponse = null;
$tcpResult = null;

while (($data860 = fgets($handler860)) !== FALSE) {
    try {
        $data800 = fgets($handler800);
        
        echo "++++++++++++++++++++++++++++++++++++++++++++++++++++\n\n\n";
        echo "Outgoing --> ".$data860."\n";
        echo "++++++++++++++++++++++++++++++++++++++++++++++++++++\n";
        $domOut = new DOMDocument;
        $domOut->loadXML($data860);
        $messageOut = simplexml_import_dom($domOut);
        
        $handlerSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_connect($handlerSocket, '10.225.192.128', 13008);
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
        
        echo "++++++++++++++++++++++++++++++++++++++++++++++++++++\n\n\n";
        echo "Outgoing --> ".$data800."\n";
        echo "++++++++++++++++++++++++++++++++++++++++++++++++++++\n";
        $domOut = new DOMDocument;
        $domOut->loadXML($data800);
        $messageOut = simplexml_import_dom($domOut);
        
        $handlerSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_connect($handlerSocket, '10.225.192.128', 13008);
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
fclose($handler860);
fclose($handler800);
