<?php
include_once 'dbClass.php';
include_once 'configClass.php';
include_once 'constants.php';
include_once "paymentsClass.php";
include_once 'LogClass.php';
class gdmCustomer {
    public $billerinfo = Array();
    public $nicinfo = Array();
    private $dbLinkCustomer;
    private $conf;
    private $log;
    public $paymentid;
    
    function __construct($vNicId, $vBillerId) {
        $this->log = new Logger();
        $this->conf = new configLoader('../config/db.json');
        $this->dbLinkCustomer = new dbRequest($this->conf->structure['dbtype'],
                                           $this->conf->structure['dbhost'],
                                           $this->conf->structure['dbport'],
                                           $this->conf->structure['dbname'],
                                           $this->conf->structure['dbuser'],
                                           $this->conf->structure['dbpass']);
        $this->loadProfile($vNicId, $vBillerId);
    }
    
    // PRIVATE FUNCTIONS ******************************************************************
    private function loadProfile($vNicId, $vBillerId){
        $this->dbLinkCustomer->setQuery("select id as billerid, name as billername, maxpendingbills, notificationemail "
                ."from t_billers where hubid = $1;", Array($vBillerId));
        $billerdata = $this->dbLinkCustomer->execQry();
        if(!empty($billerdata)){
            $this->billerinfo = $billerdata[0];
            $this->dbLinkCustomer->setQuery("select * from t_clients where nic = $1 and id_billers = $2 and status in ('P','C') order by id desc", Array($vNicId, (int)$this->billerinfo['billerid']));
            $nicdata = $this->dbLinkCustomer->execQry();
            if(!empty($nicdata)){
                $this->nicinfo['nic'] = $vNicId;
                $this->nicinfo['clientname'] = $nicdata[0]['clientname'];
                $this->nicinfo['maxamount'] = $nicdata[0]['amount'];
                $this->nicinfo['minamount'] = $nicdata[0]['amount'];
                $this->nicinfo['duedate'] = $nicdata[0]['billcutdate'];
                $this->nicinfo['status'] = $nicdata[0]['status'];
            }
        }
    }
    
    //PUBLIC FUNCTIONS ********************************************************************
    public function applyPayment($vAmount){
        try {
            if((float)$vAmount == (float)$this->nicinfo['maxamount']){
                $paymentEntity = new gdmPayments();
                $this->dbLinkCustomer->startTransactions();
                $this->dbLinkCustomer->setQuery("update t_clients set status = 'C', amount = $1 where nic = $2 and id_billers = $3 and status = 'P'", Array(0.00, $this->nicinfo['nic'], (int)$this->billerinfo['billerid']));
                if($this->dbLinkCustomer->execQry()){
                    if($paymentEntity->recordPayment($this->nicinfo, $this->billerinfo)){
                        $this->dbLinkCustomer->commitTransactions();
                        $this->paymentid = $paymentEntity->virtualid;
                        return true;
                    }else {
                        $this->dbLinkCustomer->rollbacTransactions();
                    }
                }else {
                    return false;
                }
            }else {
                return null;
            }
        } catch (Exception $e) {
            $this->log->writeLog(LOGERROR, $e->getTraceAsString());
            return false;
        }
    } 
 //End of the Class   
 }
?>
