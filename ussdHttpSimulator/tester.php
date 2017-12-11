<?php
include_once 'lib/httpClientClass.php';

$trxList = ["PTC", "PLO", "AFA", "PFA", "TPR", "CCT", "CTC", "CLO", "RPR", "RRP"];
$ussdTrigger = new httpClient();

//Transaction Flow ----------------------------------------------------------
foreach($trxList as $trxId){
    $ussdTrigger->setURL('http://localhost/tPagoTester/tPagoTester.php/'.$trxId."/8094380771");
}