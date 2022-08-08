<?php
include_once 'dbClass.php';
include_once 'configClass.php';
include_once 'constants.php';
include_once 'LogClass.php';
class gdmPayments {
    public $paymentinfo = Array();
    public $virtualid;
    private $dbLinkPayment;
    private $conf;
    private $log;
    
    function __construct($vPaymentID = null) {
        $this->log = new Logger();
        $this->conf = new configLoader('../config/db.json');
        $this->dbLinkPayment = new dbRequest($this->conf->structure['dbtype'],
                                           $this->conf->structure['dbhost'],
                                           $this->conf->structure['dbport'],
                                           $this->conf->structure['dbname'],
                                           $this->conf->structure['dbuser'],
                                           $this->conf->structure['dbpass']);
        $this->virtualid  = (is_null($vPaymentID)) ? $this->getNextPaymentId() : $this->loadProfile($vPaymentID);
    }
    
    // PRIVATE FUNCTIONS ******************************************************************
    private function loadProfile($vPaymentID){
        $this->dbLinkPayment->setQuery("select a.id as paymentid, a.postdate as paymentdate, a.nic, a.amount as amount, b.status_description as status," 
                                      ."c.name as billername, c.hubid as billercode from t_postedpayments a, status_code b, t_billers c "
                                      ."where c.id = a.id_biller and b.status_code = a.status and a.id = $1", Array($vPaymentID));
        $vPaymentRecord = $this->dbLinkPayment->execQry();
        $this->paymentinfo = (!empty($vPaymentRecord[0])) ? $vPaymentRecord[0] : Array();
        return (!empty($this->paymentinfo));
    }
    
    private function getNextPaymentId(){
        try {
            $this->dbLinkPayment->setQuery("select nextval('seq_postedpayments')", Array());
            $vPaymentID = $this->dbLinkPayment->execQry();
            if(!empty($vPaymentID)){
                return $vPaymentID[0]['nextval'];
            }else {
                return false;
            }
        } catch (Exception $e){
            $this->log->writeLog(LOGERROR, $e->getTraceAsString());
            return false;
        }
        
    }
    
    //PUBLIC FUNCTIONS ********************************************************************
    public function recordPayment($vCustomerInfo, $vBillerInfo){
        try {
            $this->dbLinkPayment->setQuery("insert into t_postedpayments (id,nic,id_biller,amount,postdate,applydate,status) "
                ."values ($1,$2,$3,$4,default,default,$5)", Array((int)$this->virtualid, $vCustomerInfo['nic'],(int)$vBillerInfo['billerid'], (float)$vCustomerInfo['maxamount'], "S"));
            if($this->dbLinkPayment->execQry()){
                return true;
            }else {
                return false;
            }
        } catch (Exception $e){
            $this->log->writeLog(LOGERROR, $e->getTraceAsString());
            return false;
        }
    }
    
 //End of the Class   
 }
?>
