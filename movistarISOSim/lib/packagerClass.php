<?php
include_once 'configClass.php';
include_once 'constants.php';
include_once 'LogClass.php';
class gdmPackager {
    public $recordStructured;
    public $recordString;
    private $packagerid;
    private $parser;
    private $packager;
    private $log;
    
    
    function __construct($vPackagerFile, $vPackagerid = null) {
        $this->log = new Logger();
        try {
            $this->packager = new configLoader($vPackagerFile);
            $this->setPackagerId($vPackagerid);
        } catch (Exception $e) {
            $this->log->writeLog(LOGERROR, $e->getTraceAsString());
        }
    }
    
    // PRIVATE FUNCTIONS ******************************************************************
    
    // PUBLIC FUNCTIONS ******************************************************************
    public function setPackagerId($vPackager){
        try{
            $this->packagerid = $vPackager;
            $this->parser = ($this->packagerid == null) ? $this->packager->structure['df'] : $this->packager->structure[$this->packagerid];
        } catch (Exception $e) {
            $this->log->writeLog(LOGERROR, $e->getTraceAsString());
        }
    }
    
    public function parseRecord($vRecord){
        $this->recordStructured = Array();
        foreach ($this->parser as $vRecordField) {
            $this->recordStructured[$vRecordField['name']] = utf8_encode(trim(substr($vRecord, $vRecordField['position'], $vRecordField['length'])));
        }
    }

    public function createRecord($dataStructure){
	$this->recordString = "";
        foreach ($this->parser as $vRecordField) {
            $this->recordString .= str_pad($dataStructure[$vRecordField['name']], $vRecordField['length'], $vRecordField['paddingCharacter'], strval($vRecordField['paddingDirection']));
        }
        $this->recordString = str_pad((string)strlen($this->recordString), 4, '0', STR_PAD_LEFT).$this->packagerid.$this->recordString;
    }
 //End of the Class   
 }
