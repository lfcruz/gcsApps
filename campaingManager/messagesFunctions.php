<?php
include_once 'addedFunctions.php';

function callussd($baseurl,$number,$telco,$texto){
$ch = curl_init();
$number = trim($number);
switch($telco){
        case 2:
	curl_setopt($ch, CURLOPT_URL, $baseurl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('MSISDN: 1'.$number.'','Connection: TE','TE: trailers'));
        $response = curl_exec ($ch);
        break;
        case 8:
	curl_setopt($ch, CURLOPT_URL, $baseurl."?msisdn=1".$number."&text=".$texto);
        /*$post='<?xml version="1.0"?><methodCall><methodName>push.ussdbyMsisdn</methodName><params><param><value><string>+1'.$number.'</string></value></param><param><value><struct><member><name>message</name><value><string>'.$texto.'</string></value></member></struct></value></param></params></methodCall>';
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: text/xml'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);*/
        $response = curl_exec ($ch);
        break;
        case 10:
	curl_setopt($ch, CURLOPT_URL, $baseurl);
        $post='<?xml version=\'1.0\' encoding=\'UTF-8\'?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Body><ns1:Pr_externo_soap..ussd_push xmlns:ns1="urn:VivaWS"><origen>*150#</origen><destino>'.$number.'</destino><texto>'.$texto.'</texto><dialogo>0</dialogo></ns1:Pr_externo_soap..ussd_push></soapenv:Body></soapenv:Envelope>';

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset=UTF-8','SOAPAction: "urn:VivaWS#ussd_push"'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($ch, CURLOPT_USERPWD, 'TPAGO:TP@G0v1v@!');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $response = curl_exec ($ch);
        break;
}
curl_close($ch);
return $response;
}


function geturl($number,$telco,$message){
    switch($telco){
        case 2:
        $port='808'.substr($number,-1);
#	$baseurl="http://172.18.47.2:8080/fcgi-bin/wIQpush?dest=1".$number."&src=101&data=url&text=http%3a%2f%2f172.19.1.112%3a".$port."%2fbroadcastCampaings%2fcampaingManager%2fclaroPush%2message.html?texto=".$message."&sessiontype=finish";
        $baseurl="http://localhost:59001/fcgi-bin/wIQpush?dest=1".$number."&src=101&data=url&text=http%3a%2f%2f172.19.1.112%3a".$port."%2fbroadcastCampaings%2fcampaingManager%2fclaroPush%2fmessage.html?texto=".$message."&sessiontype=finish";
        break;
        case 8:
        //$baseurl="http://172.16.120.175:5678/cgi-bin/cellcube_push";
	$baseurl="http://172.17.30.51:16997/index.html";
        break;
        case 10:
        $baseurl="http://192.168.100.60/supreme/index.php/pr_externo_soap/index/wsdl";
        break;
    }
return $baseurl;
}

function insertSMS($number, $telco, $message){
        switch($telco){
            case 2:
                $telcoString = '02';
                $smsUrl = "http://172.19.7.111:8051/api/send-sms";
                break;
            case 8:
                $telcoString = '08';
                $smsUrl = "http://172.19.7.111:8051/api/send-sms";
                break;
            case 10:
                $telcoString = '10';
                $smsUrl = "http://172.19.7.111:8051/api/send-sms";
                break;
            case 58:
                $telcoString = '58';
                $smsUrl = "http://172.23.7.50:8051/api/send-sms";
                break;
        }
	$number = trim($number);
        $smsInfo = Array("phone" => $number, "partner-code" => $telcoString, "body" => $message);
	$smsHeader = Array("Accept: application/json", "Content-Type: application/json; charset=utf-8");
        $smsResult = json_decode(do_post_request("http://172.19.7.111:8051/api/send-sms", json_encode($smsInfo), $smsHeader, "POST"), true);
        return true;
}


function sentMessage($number, $telco, $message, $type){
    switch ($type){
        case 2:
	    insertSMS($number, $telco, $message);
            break;
        case 1:
            $baseurl=geturl($number,$telco,$message);
            callussd($baseurl,$number,$telco,$message);
            break;
        default :
            break;
    }
    return true;
}
?>
