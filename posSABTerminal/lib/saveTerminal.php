<?php
include_once 'dbClass.php';
include_once 'configClass.php';
$conf = new configLoader('../config/dbProperties.json');
$makoConnector = new dbRequest($conf->structure['mako']['dbType'],
                                   $conf->structure['mako']['dbIP'],
                                   $conf->structure['mako']['dbPort'],
                                   $conf->structure['mako']['dbName'],
                                   $conf->structure['mako']['dbUser'],
                                   $conf->structure['mako']['dbPassword']);
$vMerchantId = null;
$vTerminalId = null;
$qInsertMerchant = null;
$qInsertTerminal = null;
$qInsertTerminalInfo = null;
$sabInfo = $_POST;
$saveResult = "";
$saveResutlMsg = "";
$validMerchant = false;

//Functions ====================================================================
function findMerchant($mid){
    global $makoConnector, $vMerchantId;
    $qResult = [];
    $makoConnector->setQuery("select id from merchant where merchantid = $1", [$mid]);
    $qResult = $makoConnector->execQry();
    if($qResult){
        return $qResult[0]['id'];
    }else {
        return false;
    }
}

function getSequenceId(){
    global $makoConnector;
    $qResult = [];
    $makoConnector->setQuery("select nextval('hibernate_sequence') as midseq", []);
    $qResult = $makoConnector->execQry();
    return $qResult[0]['midseq'];
}

//Main procedure ===============================================================
$vMerchantId = findMerchant($sabInfo['mid']);
if(empty($vMerchantId)){
    $vMerchantId = getSequenceId();
    $qInsertMerchant = "insert into merchant (id,subclass,merchantid,name,active,contact,address1,address2, city,state,province,zip,phone,country,mcc,parent,ca_name,ca_street, ca_city,ca_region,ca_postal_code,ca_country) values (".$vMerchantId.",'M','".$sabInfo['mid']."','".$sabInfo['name']."','Y','',null,null,null,null,null,null,'".$sabInfo['phone']."',null,'6011',null,'".$sabInfo['name']."','".$sabInfo['street']."','".$sabInfo['city']."','".$sabInfo['region']."','000000','".$sabInfo['country']."')";
    $makoConnector->setQuery($qInsertMerchant, []);
    $validMerchant = $makoConnector->execQry(); 
}
$vTerminalId = getSequenceId();
$qInsertTerminal = "insert into terminal (id,terminalid,info,softversion,currentbatch,merchant,profile) values (".$vTerminalId.",'".$sabInfo['tid']."','Terminal001',null,null,".$vMerchantId.",12)";
$qInsertTerminalInfo = "insert into terminal_external_info (id,mid,tid,interchangeid) values (".$vTerminalId.",'".str_pad($sabInfo['mid'], 15, 0, STR_PAD_LEFT)."','".str_pad($sabInfo['tid'], 8, 0, STR_PAD_LEFT)."','BPD')";
if(!empty($vMerchantId)){
    $makoConnector->setQuery($qInsertTerminal, []);
    if(!empty($makoConnector->execQry())){
        $makoConnector->setQuery($qInsertTerminalInfo, []);
        if(!$makoConnector->execQry()){
            $makoConnector->setQuery("delete terminal where id = $vTerminalId", []);
            $saveResult = "HTTP/1.1 304 Not Modified.";
            $saveResutlMsg = error_get_last();
            error_log($saveResult, 3, $qInsertTerminalInfo);
        }
    }else {
        $saveResult = "HTTP/1.1 200 OK.";
    }
}else{
    $saveResult = "HTTP/1.1 304 Not Modified.";
    $saveResutlMsg = error_get_last();
    error_log($saveResult, 3, $qInsertTerminalInfo);
}
header($saveResult);
?>