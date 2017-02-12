<?php
class configLoader {
    public $structure;
    private $stringfile;
    private $status;
    
    function __construct($vconfFile){
        if(file_exists($vconfFile)){
            $this->stringfile = file_get_contents($vconfFile);
            $this->structure = json_decode($this->stringfile,true);
            $this->status = true;
        } else {
            $this->structure = array();
            $this->status = false;
        }
        return $this->status;
    }
        /* Configuration file structure
         * BCMdbIp
         * BCMdbPort
         * BCMdbName
         * BCMdbUser
         * BCMdbPassword
         * BCMQueue
         */
}
    ?>
