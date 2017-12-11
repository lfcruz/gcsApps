<?php
include_once 'LogClass.php';
class configLoader {
    private $configFile;
    private $logger;
    
    function __construct($vFiles){
        $this->configFile = $vFiles;
        $this->logger =  new Logger();
        return $this->reload();
    }
    
    public function Load($vFiles) {
        try {
            $stringfile = file_get_contents($vFiles);
            $confFile = json_decode($stringfile,true);
            foreach($confFile as $configKey=>$configData){
                $GLOBALS[$configKey] = $configData;
            }
            $GLOBALS['logger'] = new Logger();
            $GLOBALS['logger']->writeLog(LOGINFO, 'System configuration loaded successfully.');
            return true;
        } catch (Exception $ex) {
            $GLOBALS['logger']->writeLog(LOGERROR, null,[$ex->getCode(),$ex->getFile(),$ex->getLine(),$ex->getMessage(),$ex->getTraceAsString()]);
            return false;
        }
    }
    
    public function reload() {
        try {
            $stringfile = file_get_contents($this->configFile);
            $confFile = json_decode($stringfile,true);
            foreach($confFile as $configKey=>$configData){
                $GLOBALS[$configKey] = $configData;
            }
            
            $this->logger->writeLog(LOGINFO, 'System configuration loaded successfully.');
            return true;
        } catch (Exception $ex) {
            $this->logger->writeLog(LOGERROR, null,[$ex->getCode(),$ex->getFile(),$ex->getLine(),$ex->getMessage(),$ex->getTraceAsString()]);
            return false;
        }
    }
}
