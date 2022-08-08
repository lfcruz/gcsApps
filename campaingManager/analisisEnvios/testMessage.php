<?php
include_once '../messagesFunctions.php';
$telco = $argv[1];
$number = $argv[2];
$type = $argv[3];
$message = $argv[4];
var_dump(sentMessage($number,$telco,$message,$type));
?>
