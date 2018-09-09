<?php
include_once 'configClass.php';
include_once 'constants.php';
class Logger {
    private $conf;
    
    function __construct() {
        $this->conf = new configLoader('../config/logger.json');
        switch ($this->conf->structure['level']){
            case "WARN":
                $this->conf->structure['level'] = 0;
                break;
            case "ERROR":
                $this->conf->structure['level'] = 1;
                break;
            case "INFO":
                $this->conf->structure['level'] = 2;
                break;
            case "DEBUG":
                $this->conf->structure['level'] = 3;
                break;
            default: // NO LOG
                $this->conf->structure['level'] = -1;
                break;
        }
    }
    
    public function writeLog($errorType, $customMessage = null, $objException = null){
        if ($errorType <= $this->conf->structure['level']){ 
            error_log($customMessage, 3, $this->conf->structure['logfile']."\n");
        }
    }
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

