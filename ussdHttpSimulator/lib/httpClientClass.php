<?php
Class httpClient {
    private $url;
    private $msisdn;
    
    function __construct($vurl =  null,$vmsisdn = null) {
        $this->url = $vurl;
        $this->msisdn = $vmsisdn;
    }
    
    public function setURL($vurl){
        $this->url = $vurl;
    }
    
    public function httpRequest($vhttpMethod) {
        $urlResponse = null;
        $urlParams = array(CURLOPT_CUSTOMREQUEST => $vhttpMethod,
            CURLOPT_FRESH_CONNECT => false,
            CURLOPT_FORBID_REUSE => false,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => Array('MSISDN: '.$this->msisdn));
        $urlResource = curl_init($this->url);
        if (!$urlResource) {
            $urlResponse = "<HTML><HEAD><TITLE></TITLE></HEAD><BODY></BODY></HTML>";
        }else {
            curl_setopt_array($urlResource, $urlParams);
            $urlResponse = curl_exec($urlResource);
            curl_close($urlResource);
        }
        return $urlResponse;    
    }
}