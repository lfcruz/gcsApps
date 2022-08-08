<?php
require_once 'Net/SMPP/Client.php';
Class smppClient {
    private $smsc;
    private $host;
    private $port;
    private $systemid;
    private $password;
    private $systemtype;
    private $STon;
    private $SNpi;
    private $SAddr;
    private $TTon;
    private $TNpi;
    
    function __construct($vHost, $vPort, $vSystemid, $vPassword, $vSystemType, $vSTon, $vSNpi, $vSAddr, $vTTon, $vTNpi) {
        $this->host = $vHost;
        $this->port = $vPort;
        $this->systemid = $vSystemid;
        $this->password = $vPassword;
        $this->systemtype = $vSystemType;
        $this->STon = $vSTon;
        $this->SNpi = $vSNpi;
        $this->SAddr = $vSAddr;
        $this->TTon = $vTTon;
        $this->TNpi = $vTNpi;
    }
    
    function __destruct() {
        $this->smsc->sendPDU(Net_SMPP::PDU('unbind'));
        $this->smsc->readPDU();
        $this->smsc->disconnect();
    }
    
    public function smppBind(){
        $this->smsc = new Net_SMPP_Client($this->host, $this->port);
        $this->smsc->connect();
        $respond = $this->smsc->bind(['system_id'=>$this->systemid,
                                      'password'=>$this->password,
                                      'system_type'=>$this->systemtype]);
        return !$respond->isError();
    }
    
    public function smppUnbind(){
        $this->smsc->sendPDU(Net_SMPP::PDU('unbind'));
        $this->smsc->readPDU();
        $this->smsc->disconnect();
    }
    
    public function sendSM($destAddress, $vMessage){
        $smText = mb_convert_encoding($vMessage, "ISO-8859-1", "auto");
        $smPackage =& Net_SMPP::PDU('submit_sm', ['source_addr'=> 'TPAGO',
                                                  'source_addr_ton'=> 5,
                                                  'dest_addr_ton'=> 1,
                                                  'destination_addr' => $destAddress,
                                                  'data_coding'=> 3,
                                                  'short_message'=> $smText]);
        $this->smsc->sendPDU($smPackage);
        $response =& $this->smsc->readPDU();
        return $response->statusDesc();
    }
}