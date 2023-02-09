<?php 
session_start();
include_once "../lib/constants.php";
include_once "../lib/requestClass.php";
    $vRQ = explode('/', trim($_SERVER['PATH_INFO'],'/'));
    $vRequest = $vRQ;
    $vRequest['body'] = json_decode(file_get_contents('php://input'),true);
    $vRequest['method'] = $_SERVER['REQUEST_METHOD'];
    $vRequest['authorization'] = (!empty($_SERVER['HTTP_API_TOKEN'])) ? $_SERVER['HTTP_API_TOKEN'] : "0.0.0";
    $request = new coreRequest($vRequest);
    
    if($request){
         $response = $request->process();
    }else {
         $response["https_rsp_code"] = HTTP_ERROR;
         $response["data"] = null;
         $response["proc_rsp_code"] = null;
    }
    header($response["http_rsp_code"]);
    header('Content-Type: application/json');
    $finalResponse['data'] = $response['data'];
    $finalResponse['responsecode'] = $response['proc_rsp_code'];
    echo ((!empty($finalResponse['data'])) ? json_encode($finalResponse['data']) : json_encode($finalResponse['responsecode']));
?>