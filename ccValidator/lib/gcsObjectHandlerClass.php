<?php
include_once 'dbClass.php';
include_once 'configClass.php';
include_once 'constants.php';
include_once 'LogClass.php';
class gcsCreditCards {
    public $ccBinNumber = null;
    private $accountInfo;
    private $dbLinkCustomer;
    private $conf;
    private $log;
    
    function __construct($vCCData) {
        $this->accountInfo = $vCCData;
        $this->log = new Logger();
        $this->conf = new configLoader('../config/db.json');
        $this->dbLinkCustomer = new dbRequest($this->conf->structure['dbtype'],
                                           $this->conf->structure['dbhost'],
                                           $this->conf->structure['dbport'],
                                           $this->conf->structure['dbname'],
                                           $this->conf->structure['dbuser'],
                                           $this->conf->structure['dbpass']);
        return $this->loadProfile();
    }
    
    // PRIVATE FUNCTIONS ******************************************************************
    private function loadProfile(){
        $this->dbLinkCustomer->setQuery('select binnumber from bin_cache where documentid = $1 and telephone = $2 and accountnumber = $3', Array($this->accountInfo['client-id'], $this->accountInfo['telephone'], $this->accountInfo['account-number']));
        $ccData = $this->dbLinkCustomer->execQry();
        if(!empty($ccData)){
            $this->ccBinNumber = $ccData[0]['binnumber'];
        }else {
            $this->queueCCRecordAdd();
        }
        return true;
    }
    
    //PUBLIC FUNCTIONS ********************************************************************
    public function queueCCRecordAdd(){
            $this->dbLinkCustomer->setQuery('insert into update_cache values ($1, $2, $3, $4, $5, $6, $7, $8, DEFAULT)', Array($this->accountInfo['client-id'],
                                                                                                                      $this->accountInfo['client-id-type'],
                                                                                                                      $this->accountInfo['telephone'],
                                                                                                                      $this->accountInfo['account-number'],
                                                                                                                      $this->accountInfo['account-type'],
                                                                                                                      $this->accountInfo['currency'],
                                                                                                                      $this->accountInfo['bank-id'],
                                                                                                                      CACHE_CREATE));
            $this->dbLinkCustomer->execQry();
            return true;
    } 
 //End of the Class   
 }
?>
