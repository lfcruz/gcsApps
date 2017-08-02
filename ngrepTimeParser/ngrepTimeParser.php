<?php
include_once 'lib/configClass.php';
include_once 'lib/dbClass.php';


function storeRecord($dbRecord){
    global $dbConnector;
    $dbConnector->setQuery('insert into ngrepTimeRecords (id,datetime,srcip,srcport,dstip,dstport,payload) values (default,$1,$2,$3,$4,$5,$6)', $dbRecord);
    try{
        $dbConnector->execQry();
    } catch (Exception $ex) {
        echo "Exception[".$ex->getCode()."] at ".$ex->getFile()."[line - ".$ex->getLine()."] : ".$ex->getMessage();
    }    
}

function processRecord($currentHeader, $strPayload){
    $recordHeader = explode(' ', $currentHeader);
    $srcAddr = explode(':', $recordHeader[3]);
    $dstAddr = explode(':', $recordHeader[5]);
    
    if($recordHeader[6] = '[AP]'){
        $dbRecord[0] = $recordHeader[1]." ".substr($recordHeader[2], 0, strlen($recordHeader[2])-7);
        $dbRecord[1] = $srcAddr[0];
        $dbRecord[2] = $srcAddr[1];
        $dbRecord[3] = $dstAddr[0];
        $dbRecord[4] = $dstAddr[1];
        $dbRecord[5] = $strPayload;
        storeRecord($dbRecord);
    }
}

function processFile($currentHeader){
    global $fileHandler;
    $strLine = "";
    $strPayload = "";
    
    do{
        $strLine = fgets($fileHandler);
        $strPayload .= (substr($strLine, 0, 1) <> 'T') ? $strLine : "";
    }while(substr($strLine, 0, 1) <> 'T' and !feof($fileHandler));
    
    processRecord($currentHeader,$strPayload);
    
    return $strLine;
}


$inputFile = $argv[1];
$nextHeader = "";
try{
    $config = new configLoader('config/config.json');
    $dbConnector = new dbRequest($config->structure['dbtype'], $config->structure['dbhost'], $config->structure['dbport'], $config->structure['dbname'], $config->structure['dbuser'], $config->structure['dbpass']);    
} catch (Exception $ex) {
    echo "Exception[".$ex->getCode()."] at ".$ex->getFile()."[line - ".$ex->getLine()."] : ".$ex->getMessage();
    exit(1);
}
if(file_exists($inputFile)){
    $fileHandler = fopen($inputFile, 'r');
    do{
        $nextHeader = processFile($nextHeader);       
    }while(!feof($fileHandler));
}else {
    die('File do not exist.');
}