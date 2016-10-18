<?php
include_once 'dbClass.php';
include_once 'configClass.php';
$conf = new configLoader('../config/dbProperties.json');
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

function getDKTTerminals($terminalFilter){
    global $dktConnector;
    $query = "select a.id AS agencyid, b.ID as terminalid, a.companyName as name,"
                             ." a.additionalAddressInfo as street, d.description as city, c.isocode_2 as region,"
                             ." c.isocode_number as country "
                       ."from Partners a, DealerTerminalsDef b, Countries c, Cities d "
                       ."where b.dealerID = a.ID and c.ID = a.countryID and d.ID = a.cityID and b.id not in (".$terminalFilter.")"
                       ."order by a.ID";
    $dktConnector->setQuery($query, []);
    $result = $dktConnector->execQry();
    if(!$result){
        return false;
    }else {
        return $result;
    }
}

function getMAKOTerminals(){
    global $makoConnector;
    $termString = '';
    $reg = [];
    $makoConnector->setQuery('select terminalid from dktterminalrelation', []);
    $getResult = $makoConnector->execQry();
    foreach($getResult as $reg){
        $termString .= $reg['terminalid'].", ";
    }
    $termString = substr($termString, 0, strlen($termString)-2);
    return $termString;
}

function putMAKOTerminals($data){
    global $makoConnector;
    $reg = [];
    $queryParameters = [];
    $query = 'INSERT INTO dktterminalrelation (agencyid, terminalid, name, street, city, region, country) VALUES ($1,$2,$3,$4,$5,$6,$7)'; 
    foreach($data as $reg){
        $queryParameters = [$reg['agencyid'], $reg['terminalid'], $reg['name'], $reg['street'], $reg['city'], $reg['region'], $reg['country']];
        $makoConnector->setQuery($query, $queryParameters);
        $putResult = $makoConnector->execQry();
        if(empty($putResult)){
            error_log("Terminal ".$reg['terminalid']." not registred.\n", 3, "../log/loader.log");
        }
    }
}

function refreshdata(){
    $dataMAKO = getMAKOTerminals();
    $dataDKT = getDKTTerminals($dataMAKO);
    if($dataDKT){
        putMAKOTerminals($dataDKT);
        header("Location: ../searchTerminals.php");
    }else {
        header("Location: lib/errorLoader.php");
    }
}

