<?php
include_once 'configClass.php';
include_once 'dbClass.php';
include_once 'LogClass.php';
include_once 'httpClientClass.php';

class qrMerchants {
    const QRY_GET_MERCHANTS = "select tpagonet_merchant_id as tpg_id,"
                                  ."merchant_id_cardnet as tpg_merchant_id,"
                                  ."merchant_account,"
                                  ."merchant_account_type,"
                                  ."merchant_bank_code,"
                                  ."rnc as merchant_rnc,"
                                  ."merchant_telco "
                                  ."from tpagonet_merchant_m "
                                  ."where is_active = $1 and isqr = $1 and substr(merchant_name, -1) <> '_' limit 10";

    const QRY_GET_MERCHANTS_ACCUMULATIVE = "select monthly.adq_merchant_code as adq_merchant_code,"
                                          ."daily.transactionsCount as dailyTransactions, daily.amountTotal as dailySales,"
                                          ."weekly.transactionsCount as weeklyTransactions, weekly.amountTotal as weeklySales,"
                                          ."monthly.transactionsCount as monthlyTransactions, monthly.amountTotal as monthlySales "
                                          ."from "
                                            ."(select adq_merchant_code, count(1) as transactionsCount, sum(original_debit_amt) as amountTotal from tqrcode_transactions "
                                            ."where transaction_date > to_date(to_char(current_date - integer '30','YYYYMMDD'),'YYYYMMDD') "
                                            ."and transaction_date < to_date(to_char(current_date,'YYYYMMDD'),'YYYYMMDD') "
                                            ."and transaction_status = '00' "
                                            ."group by adq_merchant_code) monthly "
                                          ."full outer join (select adq_merchant_code, count(1) as transactionsCount, sum(original_debit_amt) as amountTotal from tqrcode_transactions "
                                                           ."where transaction_date > to_date(to_char(current_date - integer '7','YYYYMMDD'),'YYYYMMDD') "
                                                           ."and transaction_date < to_date(to_char(current_date,'YYYYMMDD'),'YYYYMMDD') "
                                                           ."and transaction_status = '00' "
                                                           ."group by adq_merchant_code) weekly on (weekly.adq_merchant_code = monthly.adq_merchant_code) "
                                          ."full outer join (select adq_merchant_code, count(1) as transactionsCount, sum(original_debit_amt) as amountTotal from tqrcode_transactions "
                                                           ."where transaction_date > to_date(to_char(current_date - integer '1','YYYYMMDD'),'YYYYMMDD') "
                                                           ."and transaction_date < to_date(to_char(current_date,'YYYYMMDD'),'YYYYMMDD') "
                                                           ."and transaction_status = '00' "
                                                           ."group by adq_merchant_code) daily on (daily.adq_merchant_code = monthly.adq_merchant_code) "
                                          ."where (daily.transactionsCount > $1 "
                                            ."or daily.amountTotal > $2 "
                                            ."or weekly.transactionsCount > $3 "
                                            ."or weekly.amountTotal > $4 "
                                            ."or monthly.transactionsCount > $5 "
                                            ."or monthly.amountTotal > $6 ) "
                                           ."and monthly.adq_merchant_code in ";
    
    const QRY_GET_MERCHANTS_IDS = "select mid " 
                                 ."from terminal "
                                 ."inner join terminal_external_info on (terminalid = id) "
                                 ."where merchant_id in ";
    
    const QRY_DISABLE_MERCHANTS_IDS = "update merchnat set active = false where mid = $1";
    
    const DAILY_TRANSACTIONS_LIMIT = 1;
    const DAILY_SALES_LIMIT = 100;
    
    const WEEKLY_TRANSACTIONS_LIMIT = 25;
    const WEEKLY_SALES_LIMIT = 11200;
    
    const MONTHLY_TRANSACTIONS_LIMIT = 100;
    const MONTHLY_SALES_LIMIT = 50000;


    private $dbConnector = Array();
    private $deoxysConnector;
    private $config = Array();
    private $logging;
    
    
    
    
    
    function __construct() {
        $this->logging = new Logger();
        $this->config['db'] = new configLoader('config/db.json');
        $this->config['app'] = new configLoader('config/application.json');
        $this->dbConnector['tpago'] = new dbRequest($this->config['db']->structure['tpago']['dbtype'], 
                                                    $this->config['db']->structure['tpago']['dbhost'], 
                                                    $this->config['db']->structure['tpago']['dbport'], 
                                                    $this->config['db']->structure['tpago']['dbname'], 
                                                    $this->config['db']->structure['tpago']['dbuser'], 
                                                    $this->config['db']->structure['tpago']['dbpass']);
        
        $this->dbConnector['tpagonet'] = new dbRequest($this->config['db']->structure['tpagonet']['dbtype'], 
                                                       $this->config['db']->structure['tpagonet']['dbhost'], 
                                                       $this->config['db']->structure['tpagonet']['dbport'], 
                                                       $this->config['db']->structure['tpagonet']['dbname'], 
                                                       $this->config['db']->structure['tpagonet']['dbuser'], 
                                                       $this->config['db']->structure['tpagonet']['dbpass']);
        
        $this->dbConnector['vswitch'] = new dbRequest($this->config['db']->structure['vswitch']['dbtype'], 
                                                      $this->config['db']->structure['vswitch']['dbhost'], 
                                                      $this->config['db']->structure['vswitch']['dbport'], 
                                                      $this->config['db']->structure['vswitch']['dbname'], 
                                                      $this->config['db']->structure['vswitch']['dbuser'], 
                                                      $this->config['db']->structure['vswitch']['dbpass']);
        
        $this->dbConnector['hermes'] = new dbRequest($this->config['db']->structure['hermes']['dbtype'], 
                                                      $this->config['db']->structure['hermes']['dbhost'], 
                                                      $this->config['db']->structure['hermes']['dbport'], 
                                                      $this->config['db']->structure['hermes']['dbname'], 
                                                      $this->config['db']->structure['hermes']['dbuser'], 
                                                      $this->config['db']->structure['hermes']['dbpass']);
        
        $this->deoxysConnector = new httpClient();
        return;
    }
    
//========= Methods ===========================================================
    public function process(){
        $this->logging->writeLog(INFO_LOG, "+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+ Starting Limits Validatios +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+");
        $this->blockMerchants($this->getMerchantsToBlock($this->getActiveMerchants()));
        $this->logging->writeLog(INFO_LOG, "+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+ Finished Limits Validatios +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+");
    }
    
    private function getActiveMerchants(){
        $this->logging->writeLog(INFO_LOG, "Getting active merchants.....");
        $merchantList = "'0000'";
        $this->dbConnector['tpago']->setQuery(self::QRY_GET_MERCHANTS, Array('Y'));
        $activeMerchants = $this->dbConnector['tpago']->execQry();
        if(!empty($activeMerchants)){
            foreach ($activeMerchants as $record){
                $merchantList .= ", '".substr($record['tpg_merchant_id'], 4)."'";
            }
            $this->dbConnector['vswitch']->setQuery(self::QRY_GET_MERCHANTS_IDS."(".$merchantList.")", Array());
            $activeidMerchants = $this->dbConnector['vswitch']->execQry();
            if(!empty($activeidMerchants)){
                $merchantList = "'0000'";
                foreach ($activeidMerchants as $id){
                    $merchantList .= ",'".$id['mid']."'";
                }
                $this->logging->writeLog(INFO_LOG, "Active merchnats found.");
                $this->logging->writeLog(DEBUG_LOG, "Active merchnats list: ".$merchantList);
            }else {
                $this->logging->writeLog(WARNG_LOG, "No active merchants IDs to validate.");
                $merchantList = null;
            }
        }else {
            $this->logging->writeLog(WARNG_LOG, "No active merchants to validate.");
            $merchantList = null;
        }
        return $merchantList;
    }
    
    private function getMerchantsToBlock($vActiveMerchants){
        if(!empty($vActiveMerchants)){
            $this->logging->writeLog(INFO_LOG, "Getting merchants over the threshold.....");
            $merchantList = "'00000000000'";
            $merchantIDList = Array();
            $this->dbConnector['hermes']->setQuery(self::QRY_GET_MERCHANTS_ACCUMULATIVE."(".$vActiveMerchants.")", Array(self::DAILY_TRANSACTIONS_LIMIT, self::DAILY_SALES_LIMIT,
                                                                                                              self::WEEKLY_TRANSACTIONS_LIMIT, self::WEEKLY_SALES_LIMIT,
                                                                                                              self::MONTHLY_TRANSACTIONS_LIMIT, self::MONTHLY_SALES_LIMIT));
            $toBlockMerchants = $this->dbConnector['hermes']->execQry();
            if(!empty($toBlockMerchants)){
                foreach ($toBlockMerchants as $record){
                    $merchantList .= ",'".$record['adq_merchant_code']."'";
                    array_push($merchantIDList, $record['adq_merchant_code']);
                }
                $this->logging->writeLog(INFO_LOG, "Merchnats to block found.");
                $this->logging->writeLog(DEBUG_LOG, "Merchnats to block list: ".$merchantList);
            }else {
                $this->logging->writeLog(WARNG_LOG, "No merchants to be blocked.");
                $merchantIDList = null;
            }
        }else {
            $merchantIDList = null;
        }
        return $merchantIDList;
    }
    
    private function blockMerchants($vMerchantsToBlock){
        //$deoxysUrl = 'http://'.$this->config['app']->structure['deoxys_host'].':'.$this->config['app']->structure['deoxys_port'].'/gcs/deoxys/api/merchant/';
        //$deoxysHeader = Array("Content_Type: application/vnd.gcs.merchant+json;version:0.1.0;charset=UTF-8", "X-API-VERSION: 0.1.0");
        var_dump($vMerchantsToBlock);
        exit(1);
        if(!empty($vMerchantsToBlock)){
            foreach ($vMerchantsToBlock as $id){
                //$this->deoxysConnector->setURL($deoxysUrl.$id."/enable");
                //$result = $this->deoxysConnector->httpRequest('PUT', $deoxysHeader);
                //$this->logging->writeLog(INFO_LOG, "Merchant ".$id." has been disabled.");
                
            }
        }else {
            $this->logging->writeLog(WARNG_LOG, "No actions taken.");
        }
        return true;
    }
    
    
}

