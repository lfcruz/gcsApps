<?php
// Global Variables
    $mwHeader = array('Accept: application/json',
                      'Content-Type: application/json');


// Send Request ----------------------------------------------------------------
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
            CURLOPT_POSTFIELDS => $data,
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

function newOTP($phone,$amount)
{
    global $mwHeader;
    $url = "http://172.19.1.20:9898/services/withdrawal/$phone";
    $requestResult = do_post_request($url, json_encode(array("amount" => $amount)), $mwHeader, 'POST');
    $jsonResult = json_decode($requestResult,true);
    return $jsonResult;
}

function qryOTP($phone)
{
    global $mwHeader;
    $url = "http://172.19.1.20:9898/services/withdrawal/$phone";
    $requestResult = do_post_request($url, null, $mwHeader, 'GET');
    $jsonResult = json_decode($requestResult,true);
    return $jsonResult;
}

function cnlOTP($phone,$ref)
{
    global $mwHeader;
    $url = "http://172.19.1.20:9898/services/withdrawal/$phone/$ref";
    $requestResult = do_post_request($url, null, $mwHeader, 'DELETE');
    $jsonResult = json_decode($requestResult,true);
    return $jsonResult;
}

?>