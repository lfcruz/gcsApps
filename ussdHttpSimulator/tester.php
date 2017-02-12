<?php
include_once 'lib/httpClientClass.php';
include_once 'lib/configLoader.php';
$argTrx = 'PFA';
$msisdn = getMsisdnInfo(1);
$flow = new configLoader('conf/trxMap.json');
$flowTerminate = false;
$menu = Array();
$flowPointer = 0;
$execFlow = "";
$newurl = "";

//Calling Main Menu ---------------------------------------------------------
$ussdClaro = new httpClient($flow->structure['MainMenu'], $msisdn['msisdn']);
$result = $ussdClaro->httpRequest("POST");
error_log("---- New session for MSISDN: ".$msisdn['msisdn']." with TRANSACTION: ".$argTrx.".\n", 3, 'log/ussdGWY.log');
error_log(">>> MainMenu:\n", 3, 'log/ussdGWY.log');

//Transaction Flow ----------------------------------------------------------
while(!$flowTerminate){
    $menu = parseMenu($result);
    error_log(print_r($menu, true)."\n\n", 3, 'log/ussdGWY.log');
    
    
    if(empty($menu)) { exit(1);}
    if(array_key_exists('url', $menu)){
        $flowPointer += 1;
        $execFlow = getNextFlow($flow->structure[$argTrx][$flowPointer]);
        foreach($menu as $urlkey){
            if(key($urlkey) == "url"){
                $newurl .= $urlkey['url'];
            }else {
                if(key($urlkey) <> 'sessionId'){
                    $newurl .= "&".key($urlkey)."=".$execFlow;
                }else {
                    $newurl .= "&".key($urlkey)."=".$urlkey[key($urlkey)];
                }
            }
        }
        $ussdClaro->setURL(str_replace('.1.20', '.1.8', $newurl), $msisdn['msisdn']);
        $newurl = "";
    }else {
        $flowPointer += 1;
        $execFlow = getNextFlow($flow->structure[$argTrx][$flowPointer]);
        echo "\n\n-$execFlow-\n\n";
        //$ussdClaro->setURL(str_replace('.1.20', '.1.8', $menu[$flow->structure[$argTrx][$flowPointer]]));
        $ussdClaro->setURL(str_replace('.1.20', '.1.8', $menu[$execFlow]));
    }
    $result = $ussdClaro->httpRequest("POST");
    if($flow->structure[$argTrx][$flowPointer] == "End") {
        $end = true;
    }
    error_log(">>> ".$flow->structure[$argTrx][$flowPointer]."\n", 3, 'log/ussdGWY.log');
}



function parseMenu($vresult){
    $vmenu = Array();
    $dom = new DOMDocument;
    $dom->loadHTML($vresult);
    foreach ($dom->getElementsByTagName('a') as $node) {
        $vmenu[trim($node->nodeValue)] = $node->getAttribute('href');
    }
    if(empty($vmenu)){
        foreach ($dom->getElementsByTagName('form') as $node) {
            $vmenu['url'] = Array("url" => $node->getAttribute('action'));
        }
        foreach ($dom->getElementsByTagName('input') as $node) {
            $vmenu[$node->getAttribute('name')] = Array($node->getAttribute('name') => $node->getAttribute('value'));
        }
    }
    return $vmenu;
}

function getMsisdnInfo($vRandValue){
    $msisdnList = new configLoader('conf/msisdnInfo.json');
    return $msisdnList->structure[$vRandValue];
}

function getNextFlow($vFlowID){
    global $msisdn;
    $result = "";
    switch ($vFlowID){
        case "Input-Pin":
            $result = $msisdn['Pin'];
            break;
        case "Input-Amount":
            $result = "100";
            break;
        case "Input-TC":
            $result = $msisdn['CreditCard'];
            break;
        case "Input-LO":
            $result = $msisdn['Loan'];
            break;
        case "Input-Funding":
            $result = $msisdn['FundingAcct'];
            break;
        case "Input-FundingTC":
            $result = $msisdn['CreditCard'];
            break;        
        case "Input-Target":
            $result = $msisdn['TargetAcct'];
            break;
        case "Input-Telco":
            $result = $msisdn['Telefonica'];
            break;
        case "Input-Msisdn":
            $result = $msisdn['Rechmsisdn'];
            break;
        case "Input-Facturador":
            $result = $msisdn['Facturador'];
            break;
        case "Input-Nic":
            $result = $msisdn['Contrato'];
            break;
        default:
            $result = $vFlowID;
            break;
    }
    return $result;
}

?>

