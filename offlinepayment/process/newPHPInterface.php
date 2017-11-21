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
    
    function __construct($vBillerCode) {
        $this->conf['db'] = new configLoader('../config/db.json');
        $this->conf['log'] = new configLoader('../config/logger.json');
        $this->dbLinkConsiliation = new dbRequest($this->conf['db']->structure['dbtype'],
                                                  $this->conf['db']->structure['dbhost'],
                                                  $this->conf['db']->structure['dbport'],
                                                  $this->conf['db']->structure['dbname'],
                                                  $this->conf['db']->structure['dbuser'],
                                                  $this->conf['db']->structure['dbpass']);
        $this->billerInfo = $this->billerValidation($vBillerCode);
        $this->packager = $this->getPackager();
        
        
    }
    
    private function billerValidation($vBillerCode){
        try {
            $this->dbLinkConsiliation->setQuery('select * from t_billers where hubid = $1 ', [$vBillerCode]);
            $result = $this->dbLinkConsiliation->execQry();
        }catch(Exception $e){
            error_log($e->getCode()." : ".$e->getMessage()." : ".$e->getTraceAsString(), 3, $this->conf['log']['logfile']);
        }
        return $result[0];
    }
    
    private function getPackager(){
        $this->packager = new configLoader('../config/outgoing_packager.json');
        return $this->packager[$this->billerInfo['oformat']];
    }
    
    public function process(){
        $conciliationName = $this->billerInfo['externalid']."_reporte_pagos_".date('Ymd').".txt";
    }
}
