<?php

Class tPagoTester {
    private $trxmap;
    private $msisdn;
    private $measureddata;
    
    function __construct($vtrxid) {
        $this->msisdn = $this->getRandomMSISDN();
        $this->trxmap = $this->getTransactionMap($vtrxid);
    }
    
    // Privates Functions ----------------------------------------------
    private function getTransactionMap($vtrxid){
        
    }
    
    private function getRandomMSISDN(){
        
    }
    
    // Public Fucntions ------------------------------------------------
}
?>