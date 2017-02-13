<?php 
include_once 'lib/dbClass.php';
include_once 'lib/configClass.php';
$conf = new configLoader('config/dbProperties.json');
$dktConnector = new dbRequest($conf->structure['dkt']['dbType'],
                                   $conf->structure['dkt']['dbIP'],
                                   $conf->structure['dkt']['dbPort'],
                                   $conf->structure['dkt']['dbName'],
                                   $conf->structure['dkt']['dbUser'],
                                   $conf->structure['dkt']['dbPassword']);
$makoConnector = new dbRequest($conf->structure['mako']['dbType'],
                                   $conf->structure['mako']['dbIP'],
                                   $conf->structure['mako']['dbPort'],
                                   $conf->structure['mako']['dbName'],
                                   $conf->structure['mako']['dbUser'],
                                   $conf->structure['mako']['dbPassword']);

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

function getDKTTerminals($terminalFilter){
    global $dktConnector;
    $query = "select a.id AS agencyid, b.ID as terminalid, a.companyName as name,"
                             ." a.additionalAddressInfo as street, d.description as city, c.isocode_2 as region,"
                             ." c.isocode_number as country "
                       ."from Partners a, DealerTerminalsDef b, Countries c, Cities d "
                       ."where b.dealerID = a.ID and c.ID = a.countryID and d.ID = a.cityID and b.id not in (".$terminalFilter.") "
                       ."order by a.ID";
    $dktConnector->setQuery($query, []);
    $result = $dktConnector->execQry();
    error_log(date('Y-m-d H:i:s')." - Terminals difference calculated........\n", 3, 'log/update.log');
    if(!$result){
        return false;
    }else {
        return $result;
    }
}

function getTransTerminals(){
    global $makoConnector;
    $termString = '  ';
    $reg = [];
    $makoConnector->setQuery('select terminalid from dktterminalrelation', []);
    $getResult = $makoConnector->execQry();
    foreach($getResult as $reg){
        $termString .= $reg['terminalid'].", ";
    }
    //$termString = substr($termString, 0, strlen($termString)-2);
    $termString .= "0";
    error_log(date('Y-m-d H:i:s')." - Actual terminals loaded........\n", 3, 'log/update.log');
    return $termString;
}

function putTransTerminals($data){
    global $makoConnector;
    $reg = [];
    $queryParameters = [];
    error_log(date('Y-m-d H:i:s')." - Updating terminals..", 3, 'log/update.log');
    $query = 'INSERT INTO dktterminalrelation (agencyid, terminalid, name, street, city, region, country) VALUES ($1,$2,$3,$4,$5,$6,$7)'; 
    foreach($data as $reg){
	error_log("Agencia: ".$reg['agencyid']." Terminal: ".$reg['terminalid']." Nombre: ".$reg['name']."\n", 3, "log/loader.log");
        $queryParameters = [$reg['agencyid'], $reg['terminalid'], $reg['name'], $reg['street'], $reg['city'], $reg['region'], $reg['country']];
        $makoConnector->setQuery($query, $queryParameters);
        $putResult = $makoConnector->execQry();
        if(empty($putResult)){
            error_log("*", 3, 'log/update.log');
            error_log("Terminal ".$reg['terminalid']." not registred.\n", 3, "log/loader.log");
        }else {
            error_log('.', 3, 'log/update.log');
        }
    }
    error_log("\n".date('Y-m-d H:i:s')." - Terminal update finished........\n", 3, 'log/update.log');
} 

function refreshdata(){
    $dataMAKO = getTransTerminals();
    $dataDKT = getDKTTerminals($dataMAKO);
    if($dataDKT){
        putTransTerminals($dataDKT);
    }else {
        error_log(date('Y-m-d H:i:s')."No terminals to update.", 3, 'log/update.log');
    }
}

error_log(date('Y-m-d H:i:s')." - Starting terminal refreshing........\n", 3, 'log/update.log');
refreshdata();
$query = "select * from dktterminalrelation where terminalid not in (select terminalid from terminal) order by terminalid ";
$makoConnector->setQuery($query, Array());
$result = $makoConnector->execQry();
if($result) {
    foreach ($result as $vReg) {
        $vMerchantId = null;
        $vTerminalId = null;
        $qInsertMerchant = null;
        $qInsertTerminal = null;
        $qInsertTerminalInfo = null;
        $saveResult = "";
        $saveResutlMsg = "";
        $validMerchant = false;
        $vMerchantId = findMerchant($vReg['agencyid']);
        if(empty($vMerchantId)){
            $vMerchantId = getSequenceId();
            $qInsertMerchant = "insert into merchant (id,subclass,merchantid,name,active,contact,address1,address2, city,state,province,zip,phone,country,mcc,parent,ca_name,ca_street, ca_city,ca_region,ca_postal_code,ca_country) values (".$vMerchantId.",'M','".$vReg['agencyid']."','".$vReg['name']."','Y','',null,null,null,null,null,null,null,null,'6011',null,'".$vReg['name']."','".$vReg['street']."','".$vReg['city']."','".$vReg['region']."','000000','".$vReg['country']."')";
            $makoConnector->setQuery($qInsertMerchant, []);
            $validMerchant = $makoConnector->execQry(); 
        }
        if(!empty($vMerchantId)){
            $vTerminalId = getSequenceId();
            $qInsertTerminal = "insert into terminal (id,terminalid,info,softversion,currentbatch,merchant,profile) values (".$vTerminalId.",'".$vReg['terminalid']."','Terminal001',null,null,".$vMerchantId.",12)";
            $qInsertTerminalInfo = "insert into terminal_external_info (id,mid,tid,interchangeid) values (".$vTerminalId.",'".str_pad($vReg['agencyid'], 15, 0, STR_PAD_LEFT)."','".str_pad($vReg['terminalid'], 8, 0, STR_PAD_LEFT)."','BPD')";
            $makoConnector->setQuery($qInsertTerminal, []);
            if(!empty($makoConnector->execQry())){
                $makoConnector->setQuery($qInsertTerminalInfo, []);
                if(!$makoConnector->execQry()){
                    $makoConnector->setQuery("delete terminal where id = $vTerminalId", []);
                    error_log("Terminal ".$vReg['terminalid']." ROLLBACK applied.\n", 3, 'log/update.log');
                }else {
                    error_log("Terminal ".$vReg['terminalid']." SAVED succesfully.\n", 3, 'log/update.log');
                }
            }else {
                error_log("Terminal ".$vReg['terminalid']." could not be created.\n", 3, 'log/update.log');
            }
        }else{
            error_log("Merchant ".$vReg['agencyid']." for Terminal ".$vReg['terminalid']." was not found or could not be created.\n", 3, 'log/update.log');
        }
    }        
}  
error_log(date('Y-m-d H:i:s')." - Terminal refreshing ended.\n\n", 3, 'log/update.log');
?>
