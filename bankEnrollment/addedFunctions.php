<?php
// Global Variables
    $mwHeader = array('Accept: application/json',
                      'Content-Type: application/json',
                      'UserId: demo',
                      'Authentication: X253G4TRJYS4DSO12ZRV');


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
// Cardholder functions --------------------------------------------------------
function validateCardholder($enviroment,$docType,$docNumber) 
{
    global $mwHeader;
    $url = "http://$enviroment:6080/cardholder/$docType/$docNumber/phones";
    $requestResult = do_post_request($url,null,$mwHeader,'GET');
    $jsonResult = json_decode($requestResult,true);
    return $jsonResult;
}

function createCardholder($enviroment,$cardholderStructure)
{
    global $mwHeader;
    $url = "http://$enviroment:6080/cardholders";
    $requestResult = do_post_request($url, json_encode($cardholderStructure), $mwHeader, 'POST');
    $jsonResult = json_decode($requestResult,true);
    return $jsonResult;
}

function attachPhone($enviroment,$docType,$docNumber,$phoneNumber)
{
    global $mwHeader;
    $url = "http://$enviroment:6080/cardholder/$docType/$docNumber/phones/$phoneNumber";
    $requestResult = do_post_request($url, null, $mwHeader, 'PUT');
    $jsonResult = json_decode($requestResult,true);
    return $jsonResult;
}

function detachPhone($enviroment,$docType,$docNumber,$phoneNumber)
{
    global $mwHeader;
    $url = "http://$enviroment:6080/cardholder/$docType/$docNumber/phones/$phoneNumber";
    $requestResult = do_post_request($url, null, $mwHeader, 'DELETE');
    $jsonResult = json_decode($requestResult,true);
    return $jsonResult;
}

function vcashEnrollment($enviroment,$cardholderStructure)
{
    $result = '0000';
    $phoneList = validateCardholder($enviroment, $cardholderStructure["idType"], $cardholderStructure["id"]);
    if (array_key_exists('error', $phoneList)){
        if (array_key_exists('error',  createCardholder($enviroment, $cardholderStructure))){
            $result = '9901';
        }
    }
    elseif (in_array($cardholderStructure["telephone"], $phoneList)) {
            $result = '9900';
        } 
    else {
        if (array_key_exists('error',  attachPhone($enviroment, $cardholderStructure["idType"], $cardholderStructure["id"], $cardholderStructure["telephone"]))) {
            $result = '9902';
        }        
    } 
    return $result;
}

function tpagoEnrollment($enviroment,$msg)
{
    //create a socket to send message to core
    $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_connect($sock, $enviroment, '8888');
    $sent = socket_write($sock, $msg->asXML(), strlen($msg->asXML()));

    //read response message from core
    $input = socket_read($sock, 1024);
    $dom = new DOMDocument;
    $dom->loadXML($input);
    if (!$dom) {
        $result = '9903';
    }
    else{
        $response860 = simplexml_import_dom($dom);
        $result = $response860->TRANSACTION["RESPONSECODE"];
    }
    socket_close($sock);
    return $result;
}

function accountsAttach($enviroment,$msg)
{
    //send message 800 to core
    $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_connect($sock, $enviroment, '8888');
    $sent = socket_write($sock, $msg->asXML(), strlen($msg->asXML()));

    //read response from core
    $input = socket_read($sock, 1024);
    $dom = new DOMDocument;
    $dom->loadXML($input);
    if (!$dom) {
        $result = '9904';
    }
    else {
        $response800 = simplexml_import_dom($dom);
        $result = $response800->TRANSACTION["RESPONSECODE"];
    }
    return $result;

}

function buildMessages($msgType,$structureData)
{
    //Example messages for enrollment
    $msg860 = '<MESSAGE TYPE="860" BANKID="102" CORRELATIONID="2012071212545622321UL65d"><CLIENT ID="00111054938" TYPE="Cedula" TELEPHONE="8292147747" TELCOID="200" BPSEQUENCE="111644" /><TRANSACTION DATE="12072012" TIME="125456" /></MESSAGE>';
    $msg800 = '<MESSAGE TYPE="800" BANKID="102" CORRELATIONID="2012071212553631598UL9ee"><CLIENT ID="00111054938" TYPE="Cedula" TELEPHONE="8292147747" TELCOID="200" BPSEQUENCE="111645" /><PRODUCTS><PRODUCT ID="01" ACCOUNT="*****3635" TYPE="DDA" CURRENCY="DOP" ALIAS="BP_DDA" /></PRODUCTS><TRANSACTION DATE="12072012" TIME="125536" /></MESSAGE>';
    switch ($msgType) {
        case '860':
            $dom = new DOMDocument;
            $dom->loadXML($msg860);
            if (!$dom) {
                echo "Error while parsing the message: $msgType";
                exit;
            }
            //complete mesg 860
            $result = simplexml_import_dom($dom);
            $result["BANKID"]=$structureData["bank"];
            $result->CLIENT["ID"]=$structureData["document"];
            if($structureData["docType"] == "CEDULA"){
                $result->CLIENT["TYPE"]="CEDULA";
            }
            else{ 
                $result->CLIENT["TYPE"]="PASAPORTE";
            }
            $result->CLIENT["TELEPHONE"]=$structureData["msisdn"];
            $result->CLIENT["TELCOID"]=$structureData["telco"];
            $result->CLIENT["BPSEQUENCE"]=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
            break;
        
        case '800':
            $dom = new DOMDocument;
            $dom->loadXML($msg800);
            if (!$dom) {
                echo "Error while parsing the message: $msgType";
                exit;
            }
            // complete msg 800
            $result = simplexml_import_dom($dom);
            $result["BANKID"] = $structureData["bank"];
            $result->CLIENT["ID"] = $structureData["document"];
            if($structureData["docType"] == "CEDULA"){
                $result->CLIENT["TYPE"] = "CEDULA";
            }
            else{
                $result->CLIENT["TYPE"] = "PASAPORTE";
            }
            $result->CLIENT["TELEPHONE"] = $structureData["msisdn"];
            $result->CLIENT["TELCOID"] = $structureData["telco"];
            $result->CLIENT["BPSEQUENCE"]=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
            $result->PRODUCTS->PRODUCT[0]["ID"] = '01';
            $result->PRODUCTS->PRODUCT[0]["ACCOUNT"] = $structureData["document"];
            $result->PRODUCTS->PRODUCT[0]["TYPE"] = $structureData["accountType"];
            $result->PRODUCTS->PRODUCT[0]["CURRENCY"] = "DOP";
            $result->PRODUCTS->PRODUCT[0]["ALIAS"] = $structureData["alias"];
            break;
    }
    return $result;
}


?>