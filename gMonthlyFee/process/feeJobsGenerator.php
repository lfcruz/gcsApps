<?php
include_once 'lib/constantsClass.php';
include_once 'lib/configClass.php';
include_once 'lib/logClass.php';
include_once 'lib/gcsObjectHandlerClass.php';
$vChannel = $argv[1];
$mfConfig = new gMFConfigure($vChannel);
var_dump($mfConfig->channelGeneralParameters);
var_dump($mfConfig->channelEngine);


echo "************************************************************************\n";
$mfBills = new gMFBills('TPGDR');
var_dump($mfBills->getMFBills($mfConfig->channelGeneralParameters['CollectorSchedulerDays']['param_value']));

//var_dump($mfConfig);
//var_dump($mfBills);



