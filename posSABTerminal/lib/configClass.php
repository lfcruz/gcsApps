<?php
class configLoader {
    public $structure;
    private $stringfile;
    private $status;
    
    function __construct($confFile){
        if(file_exists($confFile)){
            try {
                $this->stringfile = file_get_contents($confFile);
                $this->structure = json_decode($this->stringfile,true);
                $this->status = true;
            } catch (Exception $ex) {
                error_log(date('Y-m-d')." - ".$ex->getMessage()." : [".$ex->getCode()."][".$ex->getFile()."][".$ex->getLine()."]", 3, '../log/loader.log');
            }
        } else {
            $putError = error_get_last();
            $message = date('Y-m-d')." - Configuration file ". $confFile." no existe. : [".$putError['type']."][".$putError['file']."][".$error['line']."]";
            error_log($message, 3, '../log/loader.log');
            $this->structure = array();
            $this->status = false;
        }
        return $this->status;
    }
}
?>
