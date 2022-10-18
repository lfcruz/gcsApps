<?php
include_once 'lib/constantsClass.php';
include_once 'lib/configClass.php';
include_once 'lib/logClass.php';
include_once 'lib/gcsObjectHandlerClass.php';
$vChannel = $argv[1];

//Main process 
if ($mfConfig = new gMFConfigure($vChannel)){
     $mfBills = new gMFBills('TPGDR');
     $mfBills->getMFBills($mfConfig->channelGeneralParameters['CollectorSchedulerDays']['param_value']);
}



