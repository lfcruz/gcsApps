<?php
class socketProcessor {
    private $ipAddress;
    private $port;
    private $socket;
    private $client;
    private $socketType;
    
    function __construct($vIP,$vPort,$vType) {
        $status = false;
        $this->ipAddress = $vIP;
        $this->port = $vPort;
        $this->socketType = $vType;
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket!!!!");
        switch ($this->socketType){
            case "C":
                if($this->socket){
                $status = true;
                }
                break;
            case "S":
                if($this->socket and socket_bind($this->socket, $this->ipAddress, $this->port)){
                    socket_listen($this->socket);
                    $status = true;
                }
                break;
            default:
                break;
        }
        return false;
     }
    
    function __destruct() {
        socket_close($this->socket);
    }

    public function receiveMessage(){
        $input = null;
        $remoteIP = null;
        $validXML = new DOMDocument;
        $xmlMessage = null;
        $this->client = socket_accept($this->socket);
        $input = socket_read($this->client, 1024);
        $validXML->loadXML($input);
        if (!$validXML) {
            echo "Error en DOMFile.";
            exit(1);
        }
        $xmlMessage = simplexml_import_dom($validXML);
        return $xmlMessage;
    }
    
    public function returnMessage($vxmlMessage){
        $result = true;
        $result = socket_write($this->client, $vxmlMessage->asXML());
        socket_close($this->client);
        return $result;
    }
    
    public function sendMessage($vxmlMessage){
        $validXML = new DOMDocument;
        socket_connect($this->socket, $this->ipAddress, $this->port) or die("Could not open server!!!!");
        socket_write($this->socket, $vxmlMessage->asXML());
        $xmlString = socket_read($this->socket, 1024);
        $validXML->loadXML($xmlString);
        if(!$validXML){
            
        }
        $xmlMessage = simplexml_import_dom($validXML);
        return $xmlMessage;
    }
}
    
?>