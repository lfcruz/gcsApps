<?php
include_once 'configClass.php';
include_once 'constants.php';
include_once 'LogClass.php';
include_once 'movistarGTISOPackager.php';
class isoPackager {
    private $ISOMessage;
    
    function __construct($vISOMessage) {
        $this->ISOMessage = $vISOMessage;
    }
    
    // PRIVATE FUNCTIONS ******************************************************************
    private function unPackMessage(){
        echo "-------------------------------------------------------------------\n";
        $unpackedData = unpack('H*',$this->ISOMessage);
        $message = substr($unpackedData[1], 14);
        $jack = new isoPack();
        $jack->addISO($message);
            
    /*$packResult = "";
    $jack->addMTI('0210');
    $jack->addData(2, substr($message,36,8));
    $jack->addData(3, substr($message,44,6));
    $jack->addData(4, (int) substr($message,50,12));
    $jack->addData(11, substr($message,62,6));
    $jack->addData(12, substr($message,68,6));
    $jack->addData(13, substr($message,74,4));
    $jack->addData(24, '119');
    //$jack->addData(24, (int) substr($message,78,4));
    $jack->addData(38, (string) rand(100000,99999));
    $jack->addData(39, '00');
    $jack->addData(41, pack('H*', substr($message,82,16)));
    $jack->addData(42, pack('H*', substr($message,98,30)));    
    $data = $jack->getData();
    var_dump($data);
    $packResult .= pack('H*', $jack->getMTI());
    $packResult .= pack('H*', $jack->getBitmap());
    $packResult .= pack('H*', $data[2]);
    $packResult .= pack('H*', $data[3]);
    $packResult .= pack('H*', $data[4]);
    $packResult .= pack('H*', $data[11]);
    $packResult .= pack('H*', $data[12]);
    $packResult .= pack('H*', $data[13]);
    $packResult .= pack('n*', $data[24]);
    $packResult .= $data[38];
    $packResult .= $data[39];
    $packResult .= $data[41];
    $packResult .= $data[42];
    $packResult = pack('H*', "6000000003").$packResult;
    $isoLength = strlen($packResult);
    $packResult = pack('n*', $isoLength).$packResult;
    unset($jack);
    var_dump(unpack('H*', $packResult));*/
    return true;
    }
    // PUBLIC FUNCTIONS ******************************************************************
    public function process(){
        return ($this->unPackMessage()) ? true : false;
    }

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
