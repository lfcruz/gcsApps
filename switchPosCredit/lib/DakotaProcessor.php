<?php
include_once 'LogClass.php';
include_once 'socketClass.php';
include_once 'xmlMessagesTemplates.php';

class DakotaProcessor {
    private $logger;
    private $dktHandler;
    private $dktBalance;
    private $host;
    private $port;
   
    function __construct($vHost, $vPort) {
         try {
              $this->logger = new Logger();
              $this->host = $vHost;
              $this->port = $vPort;
              #$this->dktHandler = new socketProcessor($vHost, $vPort, G_SOCKET_CLIENT);
              #$this->dktBalance = new socketProcessor($vHost, $vPort, G_SOCKET_CLIENT);
         } catch (Exception $ex) {
              $this->logger->writeLog(ERROR_LOG, $ex->getMessage(), $ex->getTraceAsString());
              return false;
         }
        
         return true;
    }
    
    // PRIVATE FUNCTIONS ******************************************************************
    //PUBLIC FUNCTIONS ********************************************************************
    public function CashOutNotification($vJsonStructure){
         try {
              $this->dktHandler = new socketProcessor($this->host, $this->port, G_SOCKET_CLIENT);
              $vMessageInfo = json_decode($vJsonStructure, true);
              $this->logger->writeLog(INFO_LOG, "GCSSEQUENCE [".$vMessageInfo['MESSAGE']['CLIENT']['GCSSEQUENCE']."] - Initiating CashOut Notificacion.");
              
              $documentObject = new DOMDocument;
              $documentObject->loadXML(DKT_115);
              $messageOutDOM = simplexml_import_dom($documentObject);              
              
              $messageOutDOM['BANKID'] = $vMessageInfo['MESSAGE']['BANKID'];
              $messageOutDOM['CORRELATIONID'] = date('Ymdhis').rand(100000,999999);
              $messageOutDOM['PARTNERID'] = $vMessageInfo['MESSAGE']['PARTNERID'];
              $messageOutDOM['AGENCYID'] = $vMessageInfo['MESSAGE']['AGENCYID'];
              $messageOutDOM['TERMINALID'] = $vMessageInfo['MESSAGE']['TERMINALID'];
              $messageOutDOM['SHIFTID'] = $vMessageInfo['MESSAGE']['SHIFTID'];
              $messageOutDOM['USERNAME'] = $vMessageInfo['MESSAGE']['USERNAME'];
              $messageOutDOM->CLIENT['GCSSEQUENCE'] = $vMessageInfo['MESSAGE']['CLIENT']['GCSSEQUENCE'];
              $messageOutDOM->TRANSACTION['DATE'] = date('dmY');
              $messageOutDOM->TRANSACTION['TIME'] = date('His');
              $messageOutDOM->TRANSACTION['ACCOUNT'] = $vMessageInfo['MESSAGE']['TRANSACTION']['ACCOUNT'];
              $messageOutDOM->TRANSACTION['AMOUNT'] = $vMessageInfo['MESSAGE']['TRANSACTION']['AMOUNT'];
              $messageOutDOM->TRANSACTION['AUTH-CODE'] = $vMessageInfo['MESSAGE']['TRANSACTION']['AUTH-CODE'];
              
              $xmlResponse = $this->dktHandler->sendMessage(trim(preg_replace('/\s\s+/', ' ', $messageOutDOM->asXML())));
              
              $documentObject->loadXML($xmlResponse);
              $messageInDOM = simplexml_import_dom($documentObject);
              
              $vMessageInfo['MESSAGE']['TRANSACTION']['GCSSEQUENCE'] = $messageInDOM->TRANSACTION['GCSSEQUENCE'];
              $vMessageInfo['MESSAGE']['TRANSACTION']['RECEIPT-NUMBER'] = $messageInDOM->TRANSACTION['RECEIPT-NUMBER'];
              $vMessageInfo['MESSAGE']['TRANSACTION']['BPSEQUENCE'] = $messageInDOM->TRANSACTION['BPSEQUENCE'];
              $vMessageInfo['MESSAGE']['TRANSACTION']['COMMISSION-AMOUNT'] = $messageInDOM->TRANSACTION['COMMISSION-AMOUNT'];
              $vMessageInfo['MESSAGE']['TRANSACTION']['RESPONSECODE'] = $messageInDOM->TRANSACTION['RESPONSECODE'];
              
         } catch (Exception $ex) {
              $this->logger->writeLog(ERROR_LOG, $ex->getMessage(), $ex->getTraceAsString());
         }
         unset($this->dktHandler);
         return json_encode($vMessageInfo);
    }
    
    public function CashOutReversal($vJsonStructure){
         try {
              $this->dktHandler = new socketProcessor($this->host, $this->port, G_SOCKET_CLIENT);
              $vMessageInfo = json_decode($vJsonStructure, true);
              $this->logger->writeLog(INFO_LOG, "GCSSEQUENCE [".$vMessageInfo['MESSAGE']['CLIENT']['GCSSEQUENCE']."] - Initiating CashOut Reversal.");
              
              $documentObject = new DOMDocument;
              $documentObject->loadXML(DKT_400);
              $messageOutDOM = simplexml_import_dom($documentObject);              
              
              $messageOutDOM['BANKID'] = $vMessageInfo['MESSAGE']['BANKID'];
              $messageOutDOM['CORRELATIONID'] = date('Ymdhis').rand(100000,999999);
              $messageOutDOM['PARTNERID'] = $vMessageInfo['MESSAGE']['PARTNERID'];
              $messageOutDOM['AGENCYID'] = $vMessageInfo['MESSAGE']['AGENCYID'];
              $messageOutDOM['TERMINALID'] = $vMessageInfo['MESSAGE']['TERMINALID'];
              $messageOutDOM['SHIFTID'] = $vMessageInfo['MESSAGE']['SHIFTID'];
              $messageOutDOM['USERNAME'] = $vMessageInfo['MESSAGE']['USERNAME'];
              $messageOutDOM->CLIENT['GCSSEQUENCE'] = $vMessageInfo['MESSAGE']['CLIENT']['GCSSEQUENCE'];
              $messageOutDOM->TRANSACTION['DATE'] = date('dmY');
              $messageOutDOM->TRANSACTION['TIME'] = date('His');
              $messageOutDOM->TRANSACTION['ACCOUNT'] = $vMessageInfo['MESSAGE']['TRANSACTION']['ACCOUNT'];
              $messageOutDOM->TRANSACTION['AMOUNT'] = $vMessageInfo['MESSAGE']['TRANSACTION']['AMOUNT'];
              
              $xmlResponse = $this->dktHandler->sendMessage(trim(preg_replace('/\s\s+/', ' ', $messageOutDOM->asXML())));
              
              $documentObject->loadXML($xmlResponse);
              $messageInDOM = simplexml_import_dom($documentObject);
              
              $vMessageInfo['MESSAGE']['TRANSACTION']['BPSEQUENCE'] = $messageInDOM->TRANSACTION['BPSEQUENCE'];
              $vMessageInfo['MESSAGE']['TRANSACTION']['RECEIPT-NUMBER'] = "00000000";
              $vMessageInfo['MESSAGE']['TRANSACTION']['RESPONSECODE'] = $messageInDOM->TRANSACTION['RESPONSECODE'];
              
         } catch (Exception $ex) {
              $this->logger->writeLog(ERROR_LOG, $ex->getMessage(), $ex->getTraceAsString());
         }
         unset($this->dktHandler);
         return json_encode($vMessageInfo);
    }
         
}