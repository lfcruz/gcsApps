<?php
include_once 'lib/movistarGTISOPackager.php';
include_once 'lib/packagerClass.php';
include_once 'lib/socketServer.php';

$isoServer = new socketProcessor("0.0.0.0", 9000, "S");

function hubRequest($functionType, $partnerRoute = null){
    
}

function prepareData(){
    
}

function processRequest($vRequestData){
    var_dump($vRequestData);
    return true;
}


//Main Function --------------------------------------------------------------
do{
    $isoRequest =  $isoServer->receiveMessage();
    $isoPackager = new isoPackager($isoRequest);
    $isoRequestData = $isoPackager->getUnpacketData();
    $isoResponse = processRequest($isoRequestData);
    $isoServer->returnMessage($isoResponse);
    unset($isoPackager);
    unset($isoRequest);
    unset($isoRequestData);
    unset($isoResponse);
}while(true);
unset($isoServer);
