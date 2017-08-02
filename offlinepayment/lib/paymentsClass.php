<?php
include_once 'dbClass.php';
include_once 'configClass.php';
include_once 'constants.php';
class gdmPayments {
    public $paymentinfo = Array();
    public $virtualid;
    private $dbConnector;
    private $conf;
    
    function __construct($vPaymentID = null) {
        $this->conf = new configLoader('../config/db.json');
        $this->dbConnector = new dbRequest($this->conf->structure['dbtype'],
                                           $this->conf->structure['dbhost'],
                                           $this->conf->structure['dbport'],
                                           $this->conf->structure['dbname'],
                                           $this->conf->structure['dbuser'],
                                           $this->conf->structure['dbpass']);
        $this->virtualid  = (is_null($vPaymentID)) ? $this->getNextPaymentId() : $this->loadProfile($vPaymentID);
        return $this->virtualid;
    }
    
    // PRIVATE FUNCTIONS ******************************************************************
    private function loadProfile($vPaymentID){
        $this->dbConnector->setQuery("select * from t_postedpayments where id = $1", Array($vPaymentID));
        $vPaymentRecord = $this->dbConnector->execQry();
        $this->paymentinfo = (!empty($vPaymentRecord[0])) ? $vPaymentRecord[0] : false;
        return (!empty($this->paymentinfo));
    }
    
    private function getNextPaymentId(){
        $this->dbConnector->setQuery("select nextval('seq_postedpayments')", Array());
        $vPaymentID = $this->dbConnector->execQry();
        return (!empty($vPaymentID)) ? $vPaymentID[0]['nextval'] : false;
        
    }
    
    //PUBLIC FUNCTIONS ********************************************************************
    public function recordPayment($vCustomerInfo, $vBillerInfo){
        $this->dbConnector->setQuery("insert into t_postedpayments (id,nic,id_biller,amount,postdate,applydate,status) "
                ."values ($1,$2,$3,$4,default,default,$5)", Array((int)$this->virtualid, $vCustomerInfo['nic'],(int)$vBillerInfo['billerid'], (float)$vCustomerInfo['maxamount'], "C"));
        return ($this->dbConnector->execQry()) ? true : false;
    }
    
 //End of the Class   
 }
?>
