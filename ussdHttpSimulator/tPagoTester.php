<?php 
session_start();
include_once "lib/tPagoTesterClass.php";
    $vRequest = explode('/', trim($_SERVER['PATH_INFO'],'/'));
    $request = new tPagoTester($vRequest[0], $vRequest[1]);
    $response = $request->process();
    header($response["http_rsp_code"]);
    var_dump($response);
?>