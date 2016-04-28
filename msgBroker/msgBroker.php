<?php
include_once "socketServer.php";
$server = new socketProcessor("localhost", 58888, "S");
$client = new socketProcessor("172.19.1.35", 8888, "C");

while(true){
    $server->returnMessage($client->sendMessage($server->receiveMessage()));
}
?>