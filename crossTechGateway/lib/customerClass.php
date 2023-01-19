<?php
include_once 'dbClass.php';
include_once 'configClass.php';
include_once 'constants.php';
include_once "paymentsClass.php";
include_once "httpClientClass.php";
include_once 'LogClass.php';

class vCashClient {
    /*LAST_STATUS: Array {"status_code":"####", "status_description":"X(50)"}
    */
    public $last_status = Array("status_code" => "", "status_description" => ""); 
    public $cardholder = Array("profile"=>Array(), "mobiles"=>Array(), "balance"=>Array());
    private $baseURL;
    private $httpClient;
    
    function __construct($vHost, $vPort, $isTLS=false, $vUrlBase="/") {
         try {
              $this->baseURL = ($isTLS) ? "https://".$vHost.":".$vPort.$vUrlBase : "http://".$vHost.":".$vPort.$vUrlBase;
              $this->httpClient = new httpClient();
         } catch (Exception $ex) {
              $this->setLastStatus(E_GENERAL, $ex->getMessage());
              return false;
         }
         $this->setLastStatus(PROC_OK, "Client build succesfully.");
         return true;
    }
    
    // PRIVATE FUNCTIONS ******************************************************************
    private function setLastStatus($vCode, $vDescription){
         $this->last_status["status_code"] = $vCode;
         $this->last_status["status_description"] = $vDescription;
         return;
    }
    
    private function rollPayloadStructure($vLayerArray, $vLayerDepth=1, $vMaxDepth=1){
         
    }
    
    private function buildPayload($vMethod, $vDataValues, $vStructureDepth=1){
         switch ($vMethod){
              case "OTPGEN":
                   $finalStructure = json_decode(OTPGEN, true, $vStructureDepth, JSON_OBJECT_AS_ARRAY);
                   break;
              default:
                   break;
         }
         $this->rollPayloadStructure($finalStructure,);
    }
    
    //PUBLIC FUNCTIONS ********************************************************************
    public function getCardholder($vDocumentNumber, $vDocumentType){
        try {
           $this->httpClient->setURL($this->baseURL.'/cardholder/'.$vDocumentType.'/'.$vDocumentNumber);
           $trasient = $this->httpClient->httpRequest(HTTPGET);
           $this->cardholder['balance'] = $transient['balance'];
           unset($trasient['balance']);
           $this->cardholder['profile'] = $transient;
           unset($transient);
           $this->httpClient->setURL($this->baseURL.'/cardholder/'.$vDocumentType.'/'.$vDocumentNumber.'/phones');
           $trasient = $this->httpClient->httpRequest(HTTPGET);
           $this->cardholder['mobiles'] = $transient;
           unset($trasient);
        } catch (Exception $ex) {
            $this->setLastStatus(E_ERROR, $ex->getMessage());
            return false;
        }
        $this->setLastStatus(PROC_OK, 'Cardholder profile loaded.');
        return true;
    }
    
    public function getCashOTP($vMobileNumber, $vAmount){
        try {
           $this->httpClient->setURL($this->baseURL.'/cashout/'.$vMobileNumber);
           $data = $this->buildPayload('OTPGEN');
           $trasient = $this->httpClient->httpRequest(HTTPPOST);
           $this->cardholder['balance'] = $transient['balance'];
           unset($trasient['balance']);
           $this->cardholder['profile'] = $transient;
           unset($transient);
           $this->httpClient->setURL($this->baseURL.'/cardholder/'.$vDocumentType.'/'.$vDocumentNumber.'/phones');
           $trasient = $this->httpClient->httpRequest(HTTPGET);
           $this->cardholder['mobiles'] = $transient;
           unset($trasient);
        } catch (Exception $ex) {
            $this->setLastStatus(E_ERROR, $ex->getMessage());
            return false;
        }
        $this->setLastStatus(PROC_OK, 'Cardholder profile loaded.');
        return true;
    }
    
 //End of the Class   
 }
?>
