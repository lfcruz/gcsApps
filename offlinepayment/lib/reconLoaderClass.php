<?php
include_once '../lib/LogClass.php';
include_once '../lib/constants.php';
include_once '../lib/configClass.php';
include_once '../lib/dbClass.php';

class conciliationReport {
    private $billerInfo;
    private $packager;
    private $conf = [];
    private $dbLinkConsiliation;
    private $log;
    private $fileHandler;
    private $reconData = [];
    
    function __construct($vBillerCode) {
        $this->conf['db'] = new configLoader('../config/db.json');
        $this->conf['log'] = new configLoader('../config/logger.json');
        $this->conf['packager'] = new configLoader('../config/outgoing_packager.json');
        $this->conf['file'] = new configLoader('../config/billers_files.json');
        $this->dbLinkConsiliation = new dbRequest($this->conf['db']->structure['dbtype'],
                                                  $this->conf['db']->structure['dbhost'],
                                                  $this->conf['db']->structure['dbport'],
                                                  $this->conf['db']->structure['dbname'],
                                                  $this->conf['db']->structure['dbuser'],
                                                  $this->conf['db']->structure['dbpass']);
        $this->billerInfo = $this->billerValidation($vBillerCode);
        $this->packager = $this->getPackager();
        $this->log = new Logger();
        
        
    }
    
    private function billerValidation($vBillerCode){
        try {
            $this->dbLinkConsiliation->setQuery('select * from t_billers where hubid = $1 ', [$vBillerCode]);
            $result = $this->dbLinkConsiliation->execQry();
        }catch(Exception $e){
            error_log($e->getCode()." : ".$e->getMessage()." : ".$e->getTraceAsString(), 3, $this->conf['log']->structure['logfile']);
        }
        return $result[0];
    }
    
    private function getPackager(){
        return $this->conf['packager']->structure[$this->billerInfo['oformat']];
    }
    
    private function openLiquidationRecords(){
        $this->dbLinkConsiliation->setQuery("update t_postedpayments set onliquidation = true where status = 'S' and reported = false", []);
        $this->dbLinkConsiliation->execQry();
    }
    
    private function getLiquidationSummary(){
        $this->dbLinkConsiliation->setQuery($this->packager['header']['query'], [$this->billerInfo['id']]);
        return $this->dbLinkConsiliation->execQry();
    }
    
    private function getLiquidationDetails(){
        $this->dbLinkConsiliation->setQuery($this->packager['body']['query'], [$this->billerInfo['id']]);
        return $this->dbLinkConsiliation->execQry();
    }
    
    private function formatFileField($record, $field){
        if($field['name'] == 'filler'){
            return str_pad("", (int) $field['length'], $field['padding'], (int) $field['orientation']);
        }else {
            return str_pad($record[$field['name']], (int) $field['length'], $field['padding'], (int) $field['orientation']);
        }
    }
    
    private function createLiquidationFile(){
        $conciliationName = "../".$this->conf['file']->structure[$this->billerInfo['externalid']]['outputFileDirectory'].$this->billerInfo['externalid']."_reporte_pagos_".date('Ymd').".txt";
        $this->fileHandler = fopen($conciliationName, 'w');
        foreach ($this->reconData['header'] as $record){
            $stringLine = $this->packager['header']['id'];
            foreach ($this->packager['header']['structure'] as $field){
                $stringLine .= $this->formatFileField($record, $field);
            }
            $stringLine .= "\n";
            fwrite($this->fileHandler, $stringLine);
        }
        foreach ($this->reconData['body'] as $record){
            $stringLine = $this->packager['body']['id'];
            foreach ($this->packager['body']['structure'] as $field){
                $stringLine .= $this->formatFileField($record, $field);
            }
            $stringLine .= "\n";
            fwrite($this->fileHandler, $stringLine);
        }
        fclose($this->fileHandler);
    }
    
    private function closeLiquidationRecords(){
        $this->dbLinkConsiliation->setQuery("update t_postedpayments set reported = true, reported_date = current_timestamp, onliquidation = false where id_biller = $1 and onliquidation = true and reported = false and reported_date is null", [$this->billerInfo['id']]);
        $this->dbLinkConsiliation->execQry();
    }
    
    public function process(){
        try {
            $this->dbLinkConsiliation->startTransactions();
            echo "OPEN LIQUIDATION................\n";
            $this->openLiquidationRecords();
            echo "GET HEADER DATA................\n";
            $this->reconData['header'] = $this->getLiquidationSummary();
            echo "GET BODY DATA................\n";
            $this->reconData['body'] = $this->getLiquidationDetails();
            var_dump($this->reconData);
            echo "CREATE FILE................\n";
            $this->createLiquidationFile();
            echo "CLOSE LIQUIDATION................\n";
            $this->closeLiquidationRecords();
            $this->dbLinkConsiliation->commitTransactions();
        } catch (Exception $ex) {
             $this->dbLinkConsiliation->rollbacTransactions();
             $this->log->writeLog(LOGERROR, $e->getTraceAsString());
             return false;
        }
        return true;
    }
}