<?php
include_once 'constants.php';
class Logger {
    
    public function writeLog($errorLevel, $customMessage = null, $objExcept = null){
    	switch (strtoupper($GLOBALS['log']['level'])){
                case "DEBUG":
    			$print = ($errorLevel < LOGDEBUG) ? true : false;
    			break;
                case "INFO":
    			$print = ($errorLevel < LOGTRACE) ? true : false;
    			break;
                case "ERROR":
    			$print = ($errorLevel < LOGINFO) ? true : false;
    			break;
    		default:
    			$print = false;
    			break;
    	}
        $errLevelString = ($errorLevel == 3) ? LEVEL_DEBUG :
                $errLevelString = ($errorLevel == 2) ? LEVEL_INFO :
                        $errLevelString = ($errorLevel == 1) ? LEVEL_ERROR : LEVEL_DEBUG;
        ($print and is_null($objException)) ? error_log("[".date("Y-m-d H:i:s")."] - ".$errLevelString." - ".$customMessage."\n", 3, $_GLOBAL['logger']['logfile']) : 
            ($print and !is_null($objException)) ? error_log("[".date("Y-m-d H:i:s")."] - ".$errLevelString." - (".$objExcept[0]."/".$objExcept[1]."/".$objExcept[2].") - ".$objExcept[3]."\n".$objExcept[4]."\n", 3, $_GLOBAL['logger']['logfile']."\n") : false;
    }
}