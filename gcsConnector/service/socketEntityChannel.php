<?php
include_once '../lib/socketClass.php';
include_once '../lib/configClass.php';
//include_once '../lib/connectorProcessorClass.php';

//$connectorServerConf = new configLoader('config/server.json');
//$connectorServer = new socketProcessor('0.0.0.0', 58888, 'S');
//configLoader::configLoader('../config/server.json');
configLoader::Load('../config/server.json');
exit(1);


$connectorProcessor = new connectorProcessor();
$serverConfigHash = md5_file('../config/server.json');
while ($connectorServerConf->structure['status'] == 'on') {
    $incomingMessage = $connectorServer->receiveMessage();
    $outgoingMessage = $connectorProcessor->process($incomingMessage);
    $connectorServer->returnMessage($outgoingMessage);
    if(md5_file('../config/server.json') != $serverConfigHash){
        $connectorServerConf->reload();
        $serverConfigHash = md5_file('../config/server.json');
    }
    unset($incomingMessage);
    unset($outgoingMessage);
}