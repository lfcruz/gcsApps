<?php
Class httpClient {
    private $url;
    private $httpInfo;
    
    function __construct() {
        $this->url = "";
    }
    
    private function parseResponse($httpData){
        $dataMatrix = explode(chr(13), $httpData);
        $is_data = false;
        foreach ($dataMatrix as $key => $value){
            if($value == chr(10)){
                $is_data = true;
            }elseif($is_data) {
                $resultMatrix ['payload'] = str_replace(chr(10), "", trim($value));
            }else {
                switch ($key) {
                    case 0:
                        $resultMatrix['http_code'] = str_replace(chr(10), "", trim($value));
                        break;
                    default:
                        $valueStructure = explode(': ', $value);
                        $resultMatrix['http_headers'][str_replace(chr(10), "", trim($valueStructure[0]))] = str_replace(chr(10), "", trim($valueStructure[1]));
                        break;
                }
            }
        }
        return $resultMatrix;
    }
    
    public function setURL($vurl){
        $this->url = $vurl;
        echo $this->url."\n";
    }
    
    public function httpRequest($vhttpMethod, $vhttpHeaders = null, $vhttpData = null) {
        $urlResponse = null;
        switch ($vhttpMethod) {
            case 'GET':
                $urlParams = array(CURLOPT_CUSTOMREQUEST => $vhttpMethod,
                    CURLOPT_FRESH_CONNECT => true,
                    CURLOPT_FORBID_REUSE => true,
                    CURLOPT_HEADER => true,
                    CURLINFO_HEADER_OUT => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => $vhttpHeaders);
                break;
            case 'POST':
                $urlParams = array(CURLOPT_CUSTOMREQUEST => $vhttpMethod,
                    CURLOPT_FRESH_CONNECT => true,
                    CURLOPT_FORBID_REUSE => true,
                    CURLOPT_HEADER => true,
                    CURLINFO_HEADER_OUT => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => $vhttpHeaders,
                    CURLOPT_POSTFIELDS => $vhttpData);
                break;
            case 'PUT':
                $urlParams = array(CURLOPT_CUSTOMREQUEST => $vhttpMethod,
                    CURLOPT_FRESH_CONNECT => true,
                    CURLOPT_FORBID_REUSE => true,
                    CURLOPT_HEADER => true,
                    CURLINFO_HEADER_OUT => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => $vhttpHeaders);
                break;
            case 'DELETE':
                $urlParams = array(CURLOPT_CUSTOMREQUEST => $vhttpMethod,
                    CURLOPT_FRESH_CONNECT => true,
                    CURLOPT_FORBID_REUSE => true,
                    CURLOPT_HEADER => true,
                    CURLINFO_HEADER_OUT => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => $vhttpHeaders);
                break;
            default:
                break;
        }
        $urlResource = curl_init($this->url);
        if (!$urlResource) {
            $urlResponse = "<HTML><HEAD><TITLE></TITLE></HEAD><BODY></BODY></HTML>";
        }else {
            curl_setopt_array($urlResource, $urlParams);
            $urlResponse = curl_exec($urlResource);
            $this->httpInfo = $this->parseResponse($urlResponse);
            curl_close($urlResource);
        }
        return (!$urlResponse) ? $urlResponse : $this->httpInfo;    
    }
}