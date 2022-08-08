<?php
include_once 'lib/movistarGTISOPackager.php';
include_once 'lib/packagerClass.php';
include_once 'lib/socketServer.php';
include_once 'lib/httpClientClass.php';

$isoServer = new socketProcessor("0.0.0.0", 9000, "S");

function validateClaro($vMsisdn){
    $returnResponse = false;
    $httpHeaders = ['Content-Type: application/vnd.tpago.billpayment+json',
                    'Accept: application/vnd.tpago.billpayment+json',
                    'UserId: demo',
                    'Authentication: T3mp0r4ldev'];
    $httpClient = new httpClient();
    $httpClient->setURL('http://10.225.192.199:8080/api/bill-payment/invoice/200TU/'.$vMsisdn);
    $httpResponse = $httpClient->httpRequest('GET', $httpHeaders);
    if(substr($httpResponse['http_code'], -2) == 'OK'){
        $returnResponse = true;
    }
    unset($httpClient);
    unset($httpResponse);
    unset($httpHeaders);
    return $returnResponse;
}

function rechargeClaro($vRequestData){
    echo "Recharging Claro Number............\n";
    $returnResponse = false;
    $httpHeaders = ['Content-Type: application/vnd.tpago.billpayment+json',
                    'Accept: application/vnd.tpago.billpayment+json',
                    'UserId: demo',
                    'Authentication: T3mp0r4ldev'];
    $httpData = ["local-tx-id" => date('YmdHis').$vRequestData[11],
                 "biller" => "200TU",
                 "contract" => $vRequestData[2],
                 "amount" => $vRequestData[4],
                 "currency" => "DOP"];
    $httpClient = new httpClient();
    $httpClient->setURL('http://10.225.192.199:8080/api/bill-payment/payments');
    $httpResponse = $httpClient->httpRequest('POST', $httpHeaders, json_encode($httpData));
    if(substr($httpResponse['http_code'], -7) == 'Created'){
        $httpClient->setURL($httpResponse['http_headers']['Location']);
        $timer = 0;
        do{
            $httpResponse = $httpClient->httpRequest('GET', $httpHeaders);
            $payload = json_decode($httpResponse['payload'],true);
            sleep(1);
            $timer += 5;
        }while($payload['status'] == 'PENDING' and $timer < 100);
        switch ($payload['status']) {
            case 'SUCCESSFUL':
                $vRequestData['38'] = substr($payload['payment-ref-no'], -6);
                $vRequestData['39'] = '00';
                break;
            default:
                $vRequestData['38'] = str_pad($payload['biller-response-code'], 6, '0', STR_PAD_LEFT);
                $vRequestData['39'] = '99';
                break;
        }
    }
    $vRequestData[4] = intval($vRequestData[4]);
    unset($httpClient);
    unset($httpResponse);
    unset($httpHeaders);
    return $vRequestData;
}

function rechargeMovistar($vIsoString){
    $isoClient = new socketProcessor('10.225.192.35', '9000', 'C');
    $response = $isoClient->sendMessage($vIsoString);
    unset($isoClient);
    return $response;
}


//Main Function --------------------------------------------------------------
do{
    $isoRequest =  $isoServer->receiveMessage();
    $isoPackager = new isoPackager($isoRequest);
    $isoRequestData = $isoPackager->getUnpacketData();
    switch (validateClaro($isoRequestData[2])){
        case true:
            $responseData = rechargeClaro($isoRequestData);
            $isoResponse = $isoPackager->setPacketData($responseData);
            break;
        default :
            $isoResponse = rechargeMovistar($isoRequest);
            break;
    }
    $isoServer->returnMessage($isoResponse);
    unset($isoPackager);
    unset($isoRequest);
    unset($isoRequestData);
    unset($isoResponse);
}while(true);
unset($isoServer);
