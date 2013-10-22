<?php

/*
 *
 *
 */

//import library with all messages
require_once 'vMsg.php';
//Set running time and timezone
set_time_limit (0);
date_default_timezone_set("America/La_Paz");

//==========================================================
//Prepare Logging file for Simulator
include('log4php/Logger.php');

// Tell log4php to use our configuration file.
Logger::configure('log4php/config.xml');

// Fetch a logger, it will inherit settings from the root logger
$log = Logger::getLogger('myLogger');

// Start logging
$log->info("Super REP Bank Simulator ".date('d-m-Y h:i:s.u')."\nStatus: UP and Waiting for Messages \n");
//=========================================================




//echo "Super REP Bank Simulator \nStatus: UP and Waiting for Messages \n";

//==========================================================
//Set address to listen and port
$address = '172.19.3.35';
$port = 8777;

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
// Bind the socket to an address/port
if(socket_bind($sock, $address, $port)){
}else $log->error("Cannot bind address, port already use?");
// Start listening for connections
socket_listen($sock);
//============================================================

while(true){


$client = socket_accept($sock);
// Read the input from the client &#8211; 1024 bytes
$input = socket_read($client, 1024);

$log->info("=================== Socket Open =================== ".date('d-m-Y h:i:s.u')."\n");

$log->info("<- Receive -> \n".$input."\n");

$dom = new DOMDocument;
$dom->loadXML($input);
if (!$dom) {
    $log->error('Error while parsing the message');
    //exit;
}
$tmp = simplexml_import_dom($dom);


//echo $tmp["TYPE"];

switch($tmp["TYPE"]){

    case "500":
        $tmp2=msg500($tmp);
        break;
    case "100":
        $tmp2=msg100($tmp);
        break;
    case "400":
        $tmp2=msg400($tmp);
        break;
    case "815":
        $tmp2=msg815($tmp);
        break;
    case "920":
        $tmp2=msg920($tmp);
        break;
    case "300":
        $tmp2=msg300($tmp);
        break;

    default:
        $log->warn("Message not created !!!\n");
    break;

}

if($tmp2){
$log->info("<- Send -> \n".$tmp2->asXML());

socket_write($client, $tmp2->asXML());
// Close the client (child) socket
}
socket_close($client);
// Close the master sockets
//socket_close($sock);

$log->info("=================== Socket Close ================== ".date('d-m-Y h:i:s.u')."\n");
//sleep(1);
$tmp2="";
$tmp="";
$input="";
}

socket_close($sock);


?>