<?php
class socketProcessor {
    private $ipAddress;
    private $port;
    private $socket;
    private $client;
    private $socketType;
    
    function __construct($vIP , $vPort, $vType) {
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
        return $status;
     }
    
    /*function __destruct() {
        socket_set_block($this->socket);
        socket_set_option($this->socket, SOL_SOCKET, SO_LINGER, Array("l_onoff" => 1,"l_linger" => 0));
        socket_close($this->socket);
    }*/

    public function receiveMessage(){
        $this->client = socket_accept($this->socket);
        $input = socket_read($this->client, 2048);
        return $input;
    }
    
    public function returnMessage($vMessage){
        $result = socket_write($this->client, $vMessage);
        socket_close($this->client);
        return $result;
    }
    
    public function sendMessage($vMessage){
        socket_connect($this->socket, $this->ipAddress, $this->port) or die("Could not open server!!!!");
        socket_write($this->socket, $vMessage);
        $vResponse = socket_read($this->socket, 2048);
        socket_close($this->socket);
        return $vResponse;
    }
}