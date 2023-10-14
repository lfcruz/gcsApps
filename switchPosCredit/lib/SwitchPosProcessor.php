<?php
include_once 'dbClass.php';
include_once 'constants.php';
include_once 'configClass.php';
include_once 'LogClass.php';

class SwitchposProcessor {
     private $config;
     private $dbConnector;
     private $logger;
     private $threadid;
     
     function __construct($vThreadId) {
          try {
               $this->threadid = $vThreadId;
               $this->config = new configLoader('../config/db.json');
               $this->dbConnector = new dbRequest($this->config->structure['dbtype'], 
                       $this->config->structure['dbhost'], $this->config->structure['dbport'], 
                       $this->config->structure['dbname'], $this->config->structure['dbuser'], 
                       $this->config->structure['dbpass']);
               $this->logger = new Logger();
          } catch (Exception $ex) {
               $this->logger->writeLog(ERROR_LOG, $ex->getMessage(), $ex->getTraceAsString());
               return false;              
          }
          return true;
          
     }
    
    // PRIVATE FUNCTIONS ******************************************************************
    //PUBLIC FUNCTIONS ********************************************************************
     public function getPendingNotifications($vBulkSize){
          try {
               $this->dbConnector->startTransactions();
               $this->dbConnector->setQuery(DTO_GET_BULK.$vBulkSize.")",[]);
               $resultset = $this->dbConnector->execQry();
               if($resultset){
                    foreach ($resultset as $row){
                         $this->dbConnector->setQuery("update accountoperation set status = '".$this->threadid."' where id = ".$row['id'], []);
                         $this->dbConnector->execQry();
                    }
                    $this->dbConnector->commitTransactions();
               }else {
                    $this->dbConnector->rollbacTransactions();
               }
          } catch (Exception $ex) {
               $this->dbConnector->rollbacTransactions();
               $resultset = [];
               $this->logger->writeLog(ERROR_LOG, $ex->getMessage(), $ex->getTraceAsString());
          }
          return json_encode($resultset);
          
     }
     
     public function setCompletedNotification($vId, $vResposeCode, $vReceiptNumber=null){
          try {
               $this->dbConnector->startTransactions();
               $this->dbConnector->setQuery("update accountoperation set status = 'C', lastupdated = current_timestamp, rc = '".$vResposeCode."', receiptnumber = '".$vReceiptNumber."' where id = ".$vId, []);
               $resultset = $this->dbConnector->execQry();
               if($resultset){
                    $this->dbConnector->commitTransactions();
               }else {
                    $this->dbConnector->rollbacTransactions();
                    $resultset = false;
               }
          } catch (Exception $ex) {
               $this->dbConnector->rollbacTransactions();
               $resultset = false;
               $this->logger->writeLog(ERROR_LOG, $ex->getMessage(), $ex->getTraceAsString());
          }
          return json_encode($resultset);
     }
         
}