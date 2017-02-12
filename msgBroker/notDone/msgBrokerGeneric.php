<?php
include_once "socketServer.php";
$server = new socketProcessor("localhost", 58888, "S");
$msgError = '<MESSAGE TYPE="ERR" ><TRANSACTION RESPONSECODE="9999" DESCRIPTION="Invalid Message" /></MESSAGE>';
$validXML = new DOMDocument;
while (true) {
    $vIncomingMessage = $server->receiveMessage();
    $client = new socketProcessor("localhost", 58887, "C");
    $vOutgoingMessage = $client->sendMessage($vIncomingMessage);
    $server->returnMessage($vOutgoingMessage);
    unset($client);
}
?>