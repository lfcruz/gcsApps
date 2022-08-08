<?php
include_once 'lib/httpClientClass.php';
class rechargeClaro {
    private $msisdn;
    
    function __construct($vMsisdn) {
        $this->msisdn = (strlen($vMsisdn) > 9) ? '502'.$vMsisdn : $vMsisdn;
    }
    
    function rechargeMsisdn($vRequestData){
        echo "Recharging Claro Number............\n";
        $returnResponse = false;
        $httpHeaders = ['Content-Type: application/vnd.tpago.billpayment+json',
                    'Accept: application/vnd.tpago.billpayment+json',
                    'UserId: tpagogt',
                    'Authentication: jU4YYe!BYKg@s2NADAVKMgFzwsWXK#4v'];
        $httpData = ["local-tx-id" => date('YmdHis').$vRequestData['localid'],
                 "biller" => "800",
                 "contract" => $this->msisdn,
                 "amount" => strval(((int) $vRequestData['amount'])),
                 "currency" => "DOP"];
        var_dump(httpData);
        $httpClient = new httpClient();
        $httpClient->setURL('http://172.19.7.76:8080/api/bill-payment/payments');
        $httpResponse = $httpClient->httpRequest('POST', $httpHeaders, json_encode($httpData));
        var_dump($httpResponse);
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
                    $vRequestData['amount-tax'] = $vRequestData['amount'];
                    $vRequestData['amount'] = strval((int) $vRequestData['amount-tax'] - ((int) $vRequestData['amount-tax'] * 0.18));
                    $vRequestData['response-message'] = 'Recharge to '.$this->msisdn.' Complete Sucesfully';
                    $vRequestData['transaction-id'] = substr($payload['payment-ref-no'], -6);
                    break;
                default:
                    $vRequestData['amount-tax'] = $vRequestData['amount'];
                    $vRequestData['amount'] = strval((int) $vRequestData['amount-tax'] - ((int) $vRequestData['amount-tax'] * 0.18));
                    $vRequestData['response-message'] = 'Recharge to '.$this->msisdn.' Failed';
                    break;
            }
        }
        unset($httpClient);
        unset($httpResponse);
        unset($httpHeaders);
        return $vRequestData;
    }

}               