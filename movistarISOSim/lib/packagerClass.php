<?php
include_once 'configClass.php';
include_once 'constants.php';
include_once 'LogClass.php';
include_once 'movistarGTISOPackager.php';
class isoPackager {
    private $ISOMessage;
    private $jack;
    
    function __construct($vISOMessage) {
        $this->ISOMessage = $vISOMessage;
        $this->jack = new isoPack();
    }
    
    // PRIVATE FUNCTIONS ******************************************************************
    private function unpackMessage(){
        echo "UNPACKET DATA -------------------------------------------------------------------\n";
        $unpackedData = unpack('H*',$this->ISOMessage);
        $message = substr($unpackedData[1], 14);
        $this->jack->addISO($message);
        return $this->jack->getData();
    }
    
    private function packMessage($vData){
        echo "PACKET DATA -------------------------------------------------------------------\n";
        $packResult = pack('H*',"6000000003");
        $this->jack->addMTI('0210');
        foreach ($vData as $bit) {
            switch ($bit){
                case 4:
                    $this->jack->addData($bit, (int) str_replace('.', '', (string) number_format((float) $vData[$bit], 2, '.', '')));
                    break;
                default:
                    $this->jack->addData($bit, $vData[$bit]);
                    break;    
            }
        }
        $vData = $this->jack->getData();
        $packResult .= pack('H*', $this->jack->getMTI());
        $packResult .= pack('H*', $this->jack->getBitmap());
        foreach ($vData as $bit) {
            switch ($bit){
                case 24:
                    $packResult .= pack('n*', $vData[$bit]);
                    break;
                case 38:
                case 39:
                case 41:
                case 42:
                    $packResult .= $vData[$bit];
                    break;
                default:
                    $packResult .= pack('H*', $vData[$bit]);
                    break;    
            }
        }
        $isoLength = strlen($packResult);
        $packResult = pack('n*', $isoLength).$packResult;
        return $packResult;
    }
    
    
    
    // PUBLIC FUNCTIONS ******************************************************************
    public function getUnpacketData(){
        return $this->unpackMessage();
    }
    
    public function setPacketData($vData){
        return $this->packMessage($vData);
    }
 //End of the Class   
 }
