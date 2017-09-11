<?php 
session_start();
include_once "../lib/requestClass.php";
    $vRQ = explode('/', trim($_SERVER['PATH_INFO'],'/'));
    $vRequest = $vRQ;
    $vRequest['body'] = json_decode(file_get_contents('php://input'),true);
    $vRequest['method'] = $_SERVER['REQUEST_METHOD'];
    $vRequest['userid'] = null;
    $vRequest['password'] = null;
    $request = new coreRequest($vRequest);
    $response = $request->process();
    header($response["http_rsp_code"]);
    header('Content-Type: application/json');
    $finalResponse['data'] = $response['data'];
    $finalResponse['responsecode'] = $response['proc_rsp_code'];
    echo json_encode($finalResponse);
?>