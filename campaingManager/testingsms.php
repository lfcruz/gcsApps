<?php
include_once 'addedFunctions.php';

$smsInfo = Array("msisdn" => "8094380771", "telco" => "CLARO", "message" => "Pruebas GCS Broadcast");
$smsHeader = Array("Username: tPago-wsClient", "Password: ab26cbb45c4a778782ba67983f1c7e7b", "Accept: application/json", "Content-Type: application/json");
var_dump($smsInfo);
var_dump(json_encode($smsInfo));
var_dump($smsHeader);
$smsResult = json_decode(do_post_request("http://172.19.7.40:28080/tPago-Notification/api/notifications/sms/v1.0.2/send.do", json_encode($smsInfo), $smsHeader, "POST"), true);
var_dump($smsResult);
