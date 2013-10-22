<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// Global Variables ------------------------------------------------------------
$mwHeader = array('Accept: application/json',
                  'Content-Type: application/json',
                  'UserId: demo',
                  'Authentication: X253G4TRJYS4DSO12ZRV');

$debcreStructure = array ("id" => "",
                          "operation" => "",
                          "phone" => "",
                          "amount" => "",
                          "currency" => "",
                          "reasonCode" => "",
                          "options" => array ("" => ""),
                          "origin" => array ("id" => "356232",
                                             "name" => "Pulperia Pololo",
                                             "city" => "San Salvador",
                                             "country" => "SV"));

// Functions definitions ------------------------------------------------------
function do_post_request($url, $data, $optional_headers,$requestType)
{
  $urlResponse = null;
  switch ($requestType) {
    case 'GET':
        $urlParams = array(CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $optional_headers);
            break;
    case 'POST':
        $urlParams = array(CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $optional_headers,
            CURLOPT_POSTFIELDS => $data);
            break;
    case 'PUT':
        $urlParams = array(CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $optional_headers);
            break;
    case 'DELETE':
        $urlParams = array(CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $optional_headers);
            break;
    default:
        break;
  }
  $urlResource = curl_init($url);
  if (!$urlResource) {
    echo("Problem stablishing resource.\n");
  }
  else {
      curl_setopt_array($urlResource, $urlParams);
      $urlResponse = curl_exec($urlResource);
    if ($urlResponse === null) {
        echo("Problem reading data from URL.\n");
    }
    curl_close($urlResource);
  }
  return $urlResponse;
}

function msg100($msg)
{
    global $debcreStructure,$mwHeader;
    $docType = "";

    $result = simplexml_import_dom($msg);

    if ($msg->CLIENT["TYPE"] == "CEDULA"){
        $docType = "CDO";
    }
    else {
        $docType = "PDO";
    }
    $debcreStructure["id"] = (string) $msg["CORRELATIONID"];
    $debcreStructure["operation"] = "DEBIT";
    $debcreStructure["phone"] = (string) $msg->CLIENT["TELEPHONE"];
    $debcreStructure["amount"] = (string) $msg->TRANSACTION["AMOUNT"];
    $debcreStructure["currency"] = (string) $msg->TRANSACTION["CURRENCY"];
    $debcreStructure["reasonCode"] = (string) $msg->TRANSACTION["SUBTRANSACTIONTYPE"];

    $url = "http://172.19.3.39:6080/cardholder/$docType/".$msg->CLIENT["ID"]."/financial";
    $requestResult = do_post_request($url, json_encode($debcreStructure), $mwHeader, 'POST');
    $jsonResult = json_decode($requestResult,true);

    $result["TYPE"] = "110";
    if (array_key_exists('error',$jsonResult)){
        $result->TRANSACTION["RESPONSECODE"] = $jsonResult["error"]["code"];
    }
    else {
        $result->TRANSACTION["RESPONSECODE"] = "0000";
    }
    $result->TRANSACTION["BPSEQUENCE"] = str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);

    return $result;

}

function msg400($msg)
{
    global $debcreStructure,$mwHeader;
    $docType = "";

    $result = simplexml_import_dom($msg);

    if ($msg->CLIENT['TYPE'] == 'CEDULA'){
        $docType = 'CDO';
    }
    else {
        $docType = 'PDO';
    }

    $debcreStructure['id'] = (string) $msg['CORRELATIONID'];
    $debcreStructure['operation'] = "CREDIT";
    $debcreStructure['phone'] = (string) $msg->CLIENT['TELEPHONE'];
    $debcreStructure['amount'] = (string) $msg->TRANSACTION['AMOUNT'];
    $debcreStructure['currency'] = (string) $msg->TRANSACTION['CURRENCY'];
    $debcreStructure['reasonCode'] = (string) $msg->TRANSACTION['SUBTRANSACTIONTYPE'];


    $url = "http://172.19.3.39:6080/cardholder/$docType/".$msg->CLIENT['ID']."/financial";
    $requestResult = do_post_request($url, json_encode($debcreStructure), $mwHeader, 'POST');
    $jsonResult = json_decode($requestResult,true);

    $result['TYPE'] = '410';
    if (array_key_exists('error',$jsonResult)){
        $result->TRANSACTION['RESPONSECODE'] = $jsonResult['error']['code'];
    }
    else {
        $result->TRANSACTION['RESPONSECODE'] = '0000';
    }
    $result->TRANSACTION['BPSEQUENCE'] = str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);

    return $result;

}

function msg500($msg)
{
    global $mwHeader;
    $docType = "";

    $result = simplexml_import_dom($msg);

    if ($msg->CLIENT["TYPE"] == 'CEDULA'){
        $docType = 'CDO';
    }
    else {
        $docType = 'PDO';
    }

    $url = "http://172.19.3.39:6080/cardholder/$docType/".$msg->CLIENT["ID"];
    $requestResult = do_post_request($url, null, $mwHeader, 'GET');
    $jsonResult = json_decode($requestResult,true);

    $result["TYPE"] = "510";
    if (array_key_exists('error',$jsonResult)){
        $result->TRANSACTION["RESPONSECODE"] = $jsonResult["error"]["code"];
    }
    else {
        $result->TRANSACTION["RESPONSECODE"] = "0000";
        $result->TRANSACTION["AMOUNT"] = $jsonResult['balance']['available'];
        $result->TRANSACTION["CURRENTBALANCE"] = $jsonResult['balance']['available'];
        $result->TRANSACTION["DUEPAYMENT"] = "0.00";
        $result->TRANSACTION["PAYOFFAMOUNT"] = "";
        $result->TRANSACTION["MINPAYMENT"] = "0.00";
        $result->TRANSACTION["DUEDATE"] = "";
    }
    $result->TRANSACTION["BPSEQUENCE"] = str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);

    return $result;

}

function msg815($input){

    $tmp = simplexml_import_dom($input);
    //Format response message
    $tmp["TYPE"]="816";
    ////Error condition on security code = 1234 / return 9899 general error
    if($tmp->TRANSACTION["SECURITYCODE"]=="1234"){
        $tmp->TRANSACTION["RESPONSECODE"]="9899";
    }else {
    $tmp->TRANSACTION["RESPONSECODE"]="0000";
    }
    $bpsequence=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    $tmp->TRANSACTION["BPSEQUENCE"]=$bpsequence;
    return $tmp;

}

function msg920($input){

    $tmp = simplexml_import_dom($input);
    //Format response message
    $tmp["TYPE"]="925";
    ////Error condition on security code = 1234 / return 9899 general error
    $tmp->TRANSACTION["RESPONSECODE"]="0000";
    $bpsequence=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    $tmp->TRANSACTION["BPSEQUENCE"]=$bpsequence;
    return $tmp;

}

function msg300($input){

    $tmp = simplexml_import_dom($input);

    //Format response message
    $tmp["TYPE"]="310";
    $tmp->TRANSACTION["NAME"]="RHONNY ESTEVEZ";
    $tmp->TRANSACTION["TYPE"]="SAV";
    $tmp->TRANSACTION["CURRENCY"]="DOP";
    $tmp->TRANSACTION["VALID-THRU"]="";
    if($tmp->TRANSACTION["ACCOUNT"]==""){
        $tmp->TRANSACTION["RESPONSECODE"]="9899";
    }else {
    $tmp->TRANSACTION["RESPONSECODE"]="0000";
    }
    $bpsequence=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    $tmp->TRANSACTION["BPSEQUENCE"]=$bpsequence;
    return $tmp;

}


?>