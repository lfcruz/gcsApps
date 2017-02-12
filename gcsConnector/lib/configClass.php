<?php
class configLoader {
    public $structure;
    public $statusMessage;
    private $stringfile;
    private $status;
    private $filename;
    
    function __construct($confFile){
        $this->filename = $confFile;
        $this->reload();
        return $this->status;
    }
    
    public function reload() {
        if(file_exists($this->filename)){
            try {
                $this->stringfile = file_get_contents($this->filename);
                $this->structure = json_decode($this->stringfile,true);
                $this->status = true;
                $this->statusMessage = 'OK.';
            } catch (Exception $ex) {
                $this->status = false;
                $this->statusMessage = $ex->getMessage();
            }
        } else {
            $this->statusMessage = 'File do not exist.';
            $this->structure = array();
            $this->status = false;
        }
    }
}
