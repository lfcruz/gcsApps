<?php
include_once 'configClass.php';
include_once 'constantsClass.php';
class Logger {
    private $logLevel;
    private $logFile;
    public $logModule;
    
    function __construct($vLoggerConf, $vModule) {
        $this->logFile = $vLoggerConf['file'];
        $this->logModule = $vModule;
        switch ($vLoggerConf['level']) {
            case 'DEBUG':
                $this->logLevel = LOGDEBUG;
                break;
            case 'TRACE':
                $this->logLevel = LOGTRACE;
                break;
            case 'INFO':
                $this->logLevel = LOGINFO;
                break;
            default:
                $this->logLevel = LOGERROR;
                break;
        }
    }
    
    public function writeLog($vErrorType, $vModule, $vCustomMessage, Exception $vObjException = null){
        $hasException = ($vObjException <> null) ? true : false;
        if($vErrorType <= $this->logLevel){
            error_log(date('Y-m-d\TH:i:s')." - ".$vModule." - ".$vErrorType." - ".$vCustomMessage.EOF, 3, $this->logFile);
            if($hasException){
                error_log(date('Y-m-d\TH:i:s')." - ".$vObjException->getFile()." - [".$vObjException->getLine()."] - ".$vObjException->getMessage().EOF.$vObjException->getTraceAsString().EOF, 3, $this->logFile);
            }
        }
    }
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

