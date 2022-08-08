<?php
include_once 'configClass.php';
include_once 'constantsClass.php';
class Logger {
    private $conf;
    
    function __construct() {
        $this->conf = new configLoader('config/logger.json');
    }
    
    public function writeLog($errorType, $customMessage = null, $objException = null){
        error_log($errorType." - ".date('Y-m-d H:i:s')." - ".$customMessage."\n", 3, $this->conf->structure['logfile']);
    }
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

