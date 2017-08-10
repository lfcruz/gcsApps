<?php
include_once 'configClass.php';
include_once 'constants.php';
class Logger {
    private $conf;
    
    function __construct() {
        $this->conf = new configLoader('../config/logger.json');
    }
    
    public function writeLog($errorType, $customMessage = null, $objException = null){
        error_log($customMessage, 4, $this->conf->structure['logfile']."\n");
    }
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

