<?php

function sendRequest($url) {
 $urlResponse = null;
 $urlParams = array(CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_FRESH_CONNECT => false,
            CURLOPT_FORBID_REUSE => false,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_RETURNTRANSFER => true);
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
function getMenu($ussdStructure){
    $url = "http://172.19.1.238:8080/mann-ussd-http-1.0.1/servlet/HttpController?";
    //$url = "http://localhost/ussdHttpSimulator/coreSimulator.php?";
    $url = $url."TransactionId=".strval($ussdStructure[transactionId]);
    $url = $url."&dialogid=".strval($ussdStructure[dialogId]);
    $url = $url."&number=".$ussdStructure[number];
    $url = $url."&text=".$ussdStructure[text];
    $url = $url."&status=".$ussdStructure[status];
    return str_replace("\n",'<br/>',  sendRequest($url));
}
?>