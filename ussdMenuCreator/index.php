<?php
include_once 'lib/requestClass.php';
//var_dump($_SERVER['HTTP_MSISDN']);
//exit;

$vMsisdnList = ['18094380771'=>true,'18295995979'=>true,'18296456060'=>true,'18097966555'=>true,'18297556166'=>true,'18299627700'=>true];
$vMsisdn = $_SERVER['HTTP_MSISDN'];
$vSessionid = $_GET['sessionid'];
$vMenuKey = $_GET['menuKey'];

if (array_key_exists($vMsisdn, $vMsisdnList)){ //and $vMsisdnList['$vMsisdn']){
	$requestHandler = new ussdRequest($vMsisdn);

	if(!isset($_GET['sessionid'])){
   		$vSessionid = $requestHandler->createSession($vMsisdn);
   		$requestHandler->getMenu($vSessionid);
	}else {
    		$requestHandler->getMenu($vSessionid, $vMenuKey);
	}
}else{
    echo "<HTML><HEAD><TITLE></TITLE></HEAD><BODY>Este movil no esta habilitado para el servicio.</BODY></HTML>";
}
