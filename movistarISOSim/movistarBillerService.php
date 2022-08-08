<?php
include_once 'lib/socketServer.php';
include_once 'lib/httpClientClass.php';
include_once 'lib/configClass.php';

define('E_INVALID_MESSAGE_LENGHT','');
define('E_INVALID_MESSAGE_TRANSACTION','');


$packetServer = new socketProcessor("0.0.0.0", 8000, "S");
$packetRecordRQ = [];
$packetRecordRS = [];

function paymentClaro($vRequestData, $trxType){
    $returnResponse = false;
    $httpHeaders = ['Content-Type: application/vnd.tpago.billpayment+json',
                    'Accept: application/vnd.tpago.billpayment+json',
                    'UserId: demo',
                    'Authentication: T3mp0r4ldev'];
    
    $httpClient = new httpClient();
    var_dump($vRequestData);
    
    switch ($trxType){
        case '03':
            $httpClient->setURL('http://10.225.192.199:8080/api/bill-payment/invoice/CGT/502'.substr($vRequestData['numerocuenta'], -8));
            $httpResponse = $httpClient->httpRequest('GET', $httpHeaders, null);
            break;
        case '01':
            $httpClient->setURL('http://10.225.192.199:8080/api/bill-payment/payments');
            $httpData = ["local-tx-id" => date('YmdHis').$vRequestData['referencia'],
                 "biller" => "CGT",
                 "contract" => '502'.substr($vRequestData['numerocuenta'], -8),
                 "amount" => $vRequestData['efectivo'],
                 "currency" => "GTQ"];
            $httpResponse = $httpClient->httpRequest('POST', $httpHeaders, json_encode($httpData));
            break;
        default:
            break;
    }
    switch (substr($httpResponse['http_code'], 9, 3)){
        case '200':
            $payload = json_decode($httpResponse['payload'],true);
            $vRequestData['balance'] = floatval($payload['invoice-amount']);
            $vRequestData['status'] = '00';
            $vRequestData['nombre'] = $payload['name'];
            $vRequestData['apellido'] = " ";
            $vRequestData['descripcion'] = 'Consulta Exitosa.';
            break;
            break;
        case '201':
            $httpClient->setURL($httpResponse['http_headers']['Location']);
            $timer = 0;
            do{
                $httpResponse = $httpClient->httpRequest('GET', $httpHeaders);
                $payload = json_decode($httpResponse['payload'],true);
                sleep(1);
                $timer += 5;
            }while($payload['status'] == 'PENDING' and $timer < 100);
            var_dump($payload);
            switch ($payload['status']) {
                case 'SUCCESSFUL':
                    $vRequestData['autorizacion'] = $payload['payment-ref-no'];
                    $vRequestData['saldo'] = '0.00';
                    $vRequestData['nombre'] = ' ';
                    $vRequestData['apellido'] = ' ';
                    $vRequestData['status'] = $payload['biller-response-code'];
                    $vRequestData['descripcion'] = $payload['biller-response-msg'];
                    break;
                default:
                    $vRequestData['autorizacion'] = '0';
                    $vRequestData['saldo'] = '0.00';
                    $vRequestData['nombre'] = ' ';
                    $vRequestData['apellido'] = ' ';
                    $vRequestData['status'] = $payload['biller-response-code'];
                    $vRequestData['descripcion'] = $payload['biller-response-msg'];
                    break;
            }
            break;
        case '500':
            $payload = json_decode($httpResponse['payload'],true);
            $vRequestData['balance'] = '0.00';
            $vRequestData['status'] = $payload['error']['code'];
            $vRequestData['nombre'] = ' ';
            $vRequestData['apellido'] = ' ';
            $vRequestData['descripcion'] = $payload['error']['description'];
            break;
        default:
            break;
    }
    unset($httpClient);
    unset($httpResponse);
    unset($httpHeaders);
    return $vRequestData;
}

function parseRecord($stringLine, $trxType){
    $result = [];
    $parserMap = new configLoader('config/incoming_packager.json');
    foreach ($parserMap->structure[$trxType] as $key){
        $result[$key['name']] = substr($stringLine, $key['position'], $key['length']);
    }
    return $result;
}

function buildRecord($recordStructure, $trxType){
    $result = '';
    $parserMap = new configLoader('config/incoming_packager.json');
    switch ($trxType){
        case '03':
            $trxType = '53';
            $result .= '53';
            break;
        case '01':
            $trxType = '51';
            $result .= '51';
            break;
        default:
            break;
    }
    foreach ($parserMap->structure[$trxType] as $key) {
        $result .= str_pad($recordStructure[$key['name']], $key['length'], $key['paddingCharacter'], (int) $key['paddingDirection']);
    }
    $result = str_pad(strval(strlen($result)), 4, '0', STR_PAD_LEFT).$result;
    var_dump($result);
    return $result;
}


//Main Function --------------------------------------------------------------
do{
    $packetRequest =  $packetServer->receiveMessage();
    $packetLen = substr($packetRequest, 0, 4);
    $packetTrx = substr($packetRequest, 4, 2);
    if ($packetLen == '0078' or $packetLen == '0155'){
        $packetRecordRQ = parseRecord($packetRequest, $packetTrx);
        $packetRecordRS = paymentClaro($packetRecordRQ, $packetTrx);
        $packetResponse = buildRecord($packetRecordRS, $packetTrx);
    }
    $packetServer->returnMessage($packetResponse);
    unset($packetRequest);
    unset($packetLen);
    unset($packetTrx);
    unset($packetRecordRQ);
    unset($packetRecordRS);
    unset($packetResponse);
}while(true);
unset($packetServer);
