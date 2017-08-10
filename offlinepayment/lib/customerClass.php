<?php
include_once 'dbClass.php';
include_once 'configClass.php';
include_once 'constants.php';
include_once 'LogClass.php';
class gdmCustomer {
    public $billerinfo = Array();
    public $nicinfo = Array();
    private $dbConnector;
    private $conf;
    private $log;
    
    function __construct($vNicId, $vBillerId) {
        $this->log = new Logger();
        $this->conf = new configLoader('../config/db.json');
        $this->dbConnector = new dbRequest($this->conf->structure['dbtype'],
                                           $this->conf->structure['dbhost'],
                                           $this->conf->structure['dbport'],
                                           $this->conf->structure['dbname'],
                                           $this->conf->structure['dbuser'],
                                           $this->conf->structure['dbpass']);
        $this->loadProfile($vNicId, $vBillerId);
    }
    
    // PRIVATE FUNCTIONS ******************************************************************
    private function loadProfile($vNicId, $vBillerId){
        $this->dbConnector->setQuery("select id as billerid, name as billername, maxpendingbills, notificationemail "
                ."from t_billers where hubid = $1;", Array($vBillerId));
        $billerdata = $this->dbConnector->execQry();
        if(!empty($billerdata)){
            $this->billerinfo = $billerdata[0];
            $this->dbConnector->setQuery("select * from t_clients where nic = $1 and id_billers = $2 and status in ('P','C') order by id", Array($vNicId, (int)$this->billerinfo['billerid']));
            $nicdata = $this->dbConnector->execQry();
            if(!empty($nicdata)){
                $this->nicinfo['nic'] = $vNicId;
                $this->nicinfo['clientname'] = $nicdata[0]['clientname'];
                $this->nicinfo['maxamount'] = $nicdata[0]['amount'];
                $this->nicinfo['minamount'] = $nicdata[0]['amount'];
                $this->nicinfo['duedate'] = $nicdata[0]['billcutdate'];
            }
        }
    }
    
    //PUBLIC FUNCTIONS ********************************************************************
    public function applyPayment($vAmount){
        try {
            $this->dbConnector->startTransactions();
            $this->dbConnector->setQuery("update t_clients set status = 'C' where nic = $1 and id_billers = $2", Array($this->nicinfo['nic'], (int)$this->billerinfo['billerid']));
            if((float)$vAmount == (float)$this->nicinfo['maxamount']){
                if($this->dbConnector->execQry()){
                    $this->dbConnector->commitTransactions();
                    return true;
                }else {
                    return false;
                }
            }else {
                return null;
            }
        } catch (Exception $e) {
            $this->dbConnector->rollbacTransactions();
            $this->log->writeLog(LOGERROR, $e->getTraceAsString());
        }
    } 
    
    public function rollbackPayment($nada){
        return true;
    }
 //End of the Class   
 }
?>
