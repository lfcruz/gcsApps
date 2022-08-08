<?php
include_once 'lib/customerClass.php';
echo "Start Reactivation Process time ===> ".date('Y-m-d H:i:s')."\n";
$processor = new enrollment();
var_dump($processor->doReactivation());
echo "Stop Reactivation Process time ===> ".date('Y-m-d H:i:s')."\n";
