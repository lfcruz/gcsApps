<?php
include_once 'dbClass.php';
include_once 'configClass.php';
include_once 'constantsClass.php';
include_once 'logClass.php';
include_once 'socketServer.php';

class tpagoProfile {
    public $profileFound = false;
    public $profileData;
    private $documentId;
    private $documentType;
    private $msisdn;
    private $bankCode;
    private $accountNumber;
    private $dbLinkCustomer;
    private $conf;
    private $log;
    
    function __construct($vDocumentId, $vDocumentType, $vPrimary, $vMsisdn = null, $vBankCode = null, $vAccountNumber = null) {
        $this->documentId = $vDocumentId;
        $this->documentType = $vDocumentType;
        $this->msisdn = $vMsisdn;
        $this->bankCode = $vBankCode;
        $this->accountNumber = $vAccountNumber;
        $this->log = new Logger();
        $this->conf = new configLoader('config/db.json');
        $this->dbLinkCustomer = new dbRequest($this->conf->structure['tpagodb']['dbtype'],
                                           $this->conf->structure['tpagodb']['dbhost'],
                                           $this->conf->structure['tpagodb']['dbport'],
                                           $this->conf->structure['tpagodb']['dbname'],
                                           $this->conf->structure['tpagodb']['dbuser'],
                                           $this->conf->structure['tpagodb']['dbpass']);
        if(!$vPrimary){
            if($this->msisdn != null){
                $stringSelector = ' and pre.msisdn = $3';
                Array($this->documentId, $this->documentType, $this->msisdn);
                if($this->bankCode != null) {
                    $stringSelector .= ' and pre.partner_code = $4';
                    Array($this->documentId, $this->documentType, $this->msisdn, $this->bankCode);
                    if($this->accountNumber != null) {
                        $stringSelector = ' and fund.account_number = $5';
                        Array($this->documentId, $this->documentType, $this->msisdn, $this->bankCode, $this->accountNumber);
                    }
                }
            }else {
            $stringSelector = '';
            $arraySelector = Array($this->documentId, $this->documentType);
            }
        }else {
            $stringSelector = ' and fund.priority = $3';
            $arraySelector = Array($this->documentId, $this->documentType, '1');
        }
     
    return $this->loadProfile($stringSelector, $arraySelector);
    }
    
    // PRIVATE FUNCTIONS ******************************************************************
    private function loadProfile($stringSelector, $arraySelector){
        $mainQuery = "select acct.id as document_id,
                                                acct.id_type as document_type,
                                                acct.gcs_account_id as gcs_account_id,
                                                acct.creation_date as creation_date,
                                                acct.billing_cycleid as billing_cycle,
                                                pre.msisdn as msisdn,
                                                pre.partner_code as bank_code,
                                                pre.telco_code as telco_code,
                                                pre.isprimary as primary_bank,
                                                pre.activation_date as activation_date,
                                                fund.priority as account_priority,
                                                fund.default_account as purchase_default,
                                                fund.account_number as account_number,
                                                fund.account_type as account_type,
                                                fund.currency as account_currency,
                                                fund.aliass as account_alias
                                        from r_gcscustomer_account_m acct
                                        inner join pre_gcscustomer_enrollment_m pre on (pre.gcs_account_id =  acct.gcs_account_id)
                                        inner join r_gcscustomer_funding_acct_mp fund on (fund.gcs_account_id = acct.gcs_account_id and fund.msisdn =  pre.msisdn and fund.partner_code = pre.partner_code)
                                        where acct.status = 'A' and pre.status = 'A' and fund.status = 'A' and fund.account_type not in ('SAV','DDA')  
                                          and acct.id = $1
                                          and acct.id_type = $2".$stringSelector.' order by document_id';
        //and pre.partner_code not in (35, 5, 38, 49)
        //var_dump($mainQuery);
        //var_dump($arraySelector);
        $this->dbLinkCustomer->setQuery($mainQuery, $arraySelector);
        $qryData = $this->dbLinkCustomer->execQry();
        if(!empty($qryData)){
            $this->profileFound = true;
            $this->profileData = $qryData;
        }else {
            $this->profileData = Array();
        }
        return $this->profileFound;
    }
    
    //PUBLIC FUNCTIONS ********************************************************************
    public function showProfile(){
        var_dump($this->profileData);
    } 
    
 //End of the Class   
 }
 
class gMFBills {
     
 /*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  ATTRIBUTES DECLARATION
 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
     private $logger;
     private $appConfig;
     private $dbConfig;
     private $dbConn;
     private $channel;
     public $billsList;

     
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 CONSTRUCTORS
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
     function __construct($vChannel) {
         $this->channel = $vChannel;
        try {
            $this->appConfig = new configLoader('config/application.json');
            $this->dbConfig = new configLoader('config/db.json');
            $this->logger = new Logger($this->appConfig->structure['logger'], 'gMFBills');
            $this->dbConn = new dbRequest($this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['db_profile']]['dbtype'], 
                                                      $this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['db_profile']]['dbhost'], 
                                                      $this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['db_profile']]['dbport'], 
                                                      $this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['db_profile']]['dbname'], 
                                                      $this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['db_profile']]['dbuser'], 
                                                      $this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['db_profile']]['dbpass']);
            $response = ($this->appConfig->structure['active']) ? $this->isChannelActive() : false;
        }catch (Exception $gException){
            $this->logger->writeLog(LOGERROR, $this->logger->logModule, $this->logger->logModule.' fail on constructor');
            $this->logger->writeLog(LOGDEBUG, $this->logger->logModule, $this->logger->logModule.' fail on constructor:',$gException);
            $response = false;
        }
        return $response;
     }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 PRIVATE FUNCTIONS
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    private function isChannelActive(){
        $this->logger->writeLog(LOGTRACE, $this->logger->logModule, 'Channel '.$this->channel.' active status is: '.(($this->appConfig->structure['channels'][$this->channel]['active']) ? 'true' : 'false'));
        return $this->appConfig->structure['channels'][$this->channel]['active'];
    }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 PUBLIC FUNCTIONS
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function getMFBills($vMaxAge, $vMinAge = null){
        try {
            if($vMinAge == null){
                $query = QRY_GET_MF_BASE.QRY_FILTER_DEFAULT.$vMaxAge.QRY_INTERVAL_DAY.QRY_FILTER_ACTIVE_ACCOUNTS;
            }else {
                $query = QRY_GET_MF_BASE.QRY_FILTER_ENGINE_AGEING_TOP.$vMaxAge.QRY_INTERVAL_DAY.QRY_FILTER_ENGINE_AGEING_BOT.$vMinAge.QRY_INTERVAL_DAY.QRY_FILTER_ACTIVE_ACCOUNTS;
            }
            $this->logger->writeLog(LOGINFO, $this->logger->logModule, 'Getting monthly bills for channel '.$this->channel);
            $this->dbConn->setQuery($query, Array());
            $vBillsList = $this->dbConn->execQry();
        } catch (Exception $gException) {
            $this->logger->writeLog(LOGERROR, $this->logger->logModule, 'There was an exception getting monthly bills.');
            $this->logger->writeLog(LOGDEBUG, $this->logger->logModule, 'There was an exception getting monthly bills.', $gException);
        }
        $this->billsList = $vBillsList;
        return $vBillsList;
    }
    
    public function setMFCharge($vMFBillId, $vMFGcsSequence){
        try {
            $this->dbConn->setQuery(QRY_UPDATE_BILL_CHARGE, Array($vMFGcsSequence, $vMFBillId));
            $response = $this->dbConn->execQry();
        } catch (Exception $gException) {
            $this->logger->writeLog(LOGERROR, $this->logger->logModule, 'There was an exception updating monthly bills charge.');
            $this->logger->writeLog(LOGDEBUG, $this->logger->logModule, 'There was an exception updating monthly bills charge.', $gException);
        }
        return $response;
    }
    
 }
 
class gMFConfigure {

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 ATTRIBUTES DECLARATION
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    private $logger;
    private $appConfig;
    private $dbConfig;
    private $engConfig;
    private $dbConn;
    private $channel;
    public $channelGeneralParameters;
    public $channelEngine;

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 CONSTRUCTORS
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    function __construct(string $vChannel) {
        $this->channel = $vChannel;
        try {
            $this->appConfig = new configLoader('config/application.json');
            $this->engConfig = new configLoader('config/engines.json');
            $this->dbConfig = new configLoader('config/db.json');
            $this->logger = new Logger($this->appConfig->structure['logger'], 'gMFConfigure');
            $this->dbConn = new dbRequest($this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['db_profile']]['dbtype'], 
                                                      $this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['db_profile']]['dbhost'], 
                                                      $this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['db_profile']]['dbport'], 
                                                      $this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['db_profile']]['dbname'], 
                                                      $this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['db_profile']]['dbuser'], 
                                                      $this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['db_profile']]['dbpass']);
            $response = ($this->appConfig->structure['active']) ? 
                    ($this->isChannelActive()) ? 
                    ($this->getGeneralParameters()) ? $this->getEngineParameters() : false 
                : false
                :false;
        }catch (Exception $gException){
            $this->logger->writeLog(LOGERROR, $this->logger->logModule, $this->logger->logModule.' fail on constructor');
            $this->logger->writeLog(LOGDEBUG, $this->logger->logModule, $this->logger->logModule.' fail on constructor:',$gException);
            $response = false;
        }
        return $response;
    }
    
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 PRIVATE FUNCTIONS
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    private function isChannelActive(){
        $this->logger->writeLog(LOGTRACE, $this->logger->logModule, 'Channel '.$this->channel.' active status is: '.(($this->appConfig->structure['channels'][$this->channel]['active']) ? 'true' : 'false'));
        return $this->appConfig->structure['channels'][$this->channel]['active'];
    }
    
    private function isEngineActive($vEngine){
        $this->logger->writeLog(LOGTRACE, $this->logger->logModule, 'Engine '.$vEngine.' active status is: '.(($this->engConfig->structure[$this->channel][$vEngine]['active']) ? 'true' : 'false'));
        return $this->engConfig->structure[$this->channel][$vEngine]['active'];
    }
    
    private function validateEngineConfiguration($vEngineActivesCount){
        $result = false;
        if($vEngineActivesCount == 1){
            switch ($this->channelEngine['engineName']){
                case ENGINE_DEFAULT:
                    $result = true;
                    break;
                case ENGINE_AGEING:
                    $result = (array_key_exists('queues', $this->engConfig->structure[$this->channel])) ?
                        (array_key_exists('1', $this->engConfig->structure[$this->channel]['queues'])) ?
                        (array_key_exists('bot_days', $this->engConfig->structure[$this->channel]['queues']['1'])) ?
                        (array_key_exists('top_days', $this->engConfig->structure[$this->channel]['queues']['1'])) ? true : false
                        : false
                    : false
                    : false;
                    $result = ($this->engConfig->structure[$this->channel][ENGINE_AGEING]['queues']['1']['bot_days'] == 0) ? 
                        ($this->engConfig->structure[$this->channel][ENGINE_AGEING]['queues']['1']['top_days'] > $this->engConfig->structure[$this->channel][ENGINE_AGEING]['queues']['1']['bot_days']) ? 
                        ($this->engConfig->structure[$this->channel][ENGINE_AGEING]['queues']['2']['bot_days'] > $this->engConfig->structure[$this->channel][ENGINE_AGEING]['queues']['1']['top_days']) ?
                        ($this->engConfig->structure[$this->channel][ENGINE_AGEING]['queues']['2']['top_days'] > $this->engConfig->structure[$this->channel][ENGINE_AGEING]['queues']['2']['bot_days']) ?
                        ($this->engConfig->structure[$this->channel][ENGINE_AGEING]['queues']['3']['bot_days'] > $this->engConfig->structure[$this->channel][ENGINE_AGEING]['queues']['2']['top_days']) ?
                        ($this->engConfig->structure[$this->channel][ENGINE_AGEING]['queues']['3']['top_days'] > $this->engConfig->structure[$this->channel][ENGINE_AGEING]['queues']['3']['bot_days']) ? true : false
                        : false
                    : false
                    : false
                    : false
                    : false;
                    break;
                case ENGINE_BANKS:
                    $result = (array_key_exists('bank_codes', $this->engConfig->structure[$this->channel][ENGINE_BANKS]) and is_string($this->engConfig->structure[$this->channel][ENGINE_BANKS]['bank_codes'])) ? true : false;
                    break;
                default:
                    $this->logger->writeLog(LOGERROR, $this->logger->logModule, 'Invalid engine '.$this->engConfig->structure['engineName'].' on channel '.$this->channel);
                    break;
            }
        }elseif($vEngineActivesCount > 1) {
            $this->logger->writeLog(LOGERROR, $this->logger->logModule, 'Configuration error, there are more than one ('.$vEngineActivesCount.') engine active for channel '.$this->channel);
            $this->engConfig = Array();
        }else{
            $this->logger->writeLog(LOGERROR, $this->logger->logModule, 'Configuration error, there is no engine active for channel '.$this->channel);
            $this->engConfig = Array();
        }
        return $result;
    }
    
    private function getGeneralParameters(){
        $this->logger->writeLog(LOGINFO, $this->logger->logModule, 'Getting General Parameters......');
        try {
            $this->dbConn->setQuery(QRY_GET_MF_PARAMS."(".$this->appConfig->structure['channels'][$this->channel]['mf_parameters'].")", Array());
            $responseParameters = $this->dbConn->execQry();
            if(empty($responseParameters)){
                $this->logger->writeLog(LOGERROR, $this->logger->logModule, 'No general parameters found for channel '.$this->channel);
                return false;
            }
            $jsonParameters = '{';
            foreach($responseParameters as $parameter){
                if($jsonParameters == '{'){
                    $jsonParameters .= '"'.$parameter['gen_parameter_name'].'":{"display_name":"'.$parameter['param_display_name'].'","param_value":"'.$parameter['parameter_value'].'"}';
                }else {
                    $jsonParameters .= ',"'.$parameter['gen_parameter_name'].'":{"display_name":"'.$parameter['param_display_name'].'","param_value":"'.$parameter['parameter_value'].'"}';
                }
            }      
            $jsonParameters .= ',"pools":'.json_encode($this->appConfig->structure['channels'][$this->channel]['pool']).',"by_subscribers":'.(int)$this->appConfig->structure['channels'][$this->channel]['by_subscribers'].',"bulk_size":'.$this->appConfig->structure['channels'][$this->channel]['bulk_size'].'}';
        } catch (Exception $gException) {
                $this->logger->writeLog(LOGERROR, $this->logger->logModule, 'There was an exception getting general parameters for channel '.$this->channel);
                $this->logger->writeLog(LOGDEBUG, $this->logger->logModule, 'There was an exception getting general parameters for channel '.$this->channel, $gException);
                return false;
        }
        $this->channelGeneralParameters = json_decode($jsonParameters, true);
        $this->logger->writeLog(LOGTRACE, $this->logger->logModule, json_encode($this->channelGeneralParameters));
        return true;
    }
    
    private function getEngineParameters(){
        $this->channelEngine = Array();
        $engineCounter = 0;
        $this->logger->writeLog(LOGINFO, $this->logger->logModule, 'Getting channel paramerters......');
        foreach($this->engConfig->structure[$this->channel] as $engKey => $engKeyValues){
            if($this->isEngineActive($engKey)){
                $engineCounter += 1;
                $this->channelEngine = Array("engineName"=> $engKey, "engineConf" => $engKeyValues);
            }
        }
        if($this->validateEngineConfiguration($engineCounter)){
            $this->logger->writeLog(LOGTRACE, $this->logger->logModule, json_encode($this->channelEngine));
            return true;
        }else {
        return false;
        }
    }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 PUBLIC FUNCTIONS
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function process(){
        
        
        return true;
    }
    
    
}

class gMFPools {
 /*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  ATTRIBUTES DECLARATION
 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
     private $logger;
     private $appConfig;
     private $dbConfig;
     private $dbConn;
     private $channel;
     private $pool;
     private $job_type;

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 CONSTRUCTORS
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
     function __construct($vChannel, $vJobType, $vPool) {
        $this->channel = $vChannel;
        $this->pool = $vPool;
        $this->job_type = $vJobType;
        try {
            $this->appConfig = new configLoader('config/application.json');
            $this->dbConfig = new configLoader('config/db.json');
            $this->logger = new Logger($this->appConfig->structure['logger'], 'gMFPools');
            $this->dbConn = new dbRequest($this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['pool']['db_profile']]['dbtype'], 
                                                      $this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['pool']['db_profile']]['dbhost'], 
                                                      $this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['pool']['db_profile']]['dbport'], 
                                                      $this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['pool']['db_profile']]['dbname'], 
                                                      $this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['pool']['db_profile']]['dbuser'], 
                                                      $this->dbConfig->structure[$this->appConfig->structure['channels'][$this->channel]['pool']['db_profile']]['dbpass']);
            $response = ($this->appConfig->structure['active']) ? $this->isChannelActive() : false;
        }catch (Exception $gException){
            $this->logger->writeLog(LOGERROR, $this->logger->logModule, $this->logger->logModule.' fail on constructor');
            $this->logger->writeLog(LOGDEBUG, $this->logger->logModule, $this->logger->logModule.' fail on constructor:',$gException);
            $response = false;
        }
        return $response;
     }


/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 PRIVATE FUNCTIONS
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
     
     
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 PUBLIC FUNCTIONS
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
     public function getTasksChunk(){
         try {
             $this->dbConn->setQuery(QRY_GET_POOL_BULK.$this->pool.QRY_JUNCTION_AND.QRY_FILTER_JOBS_CHANNEL.$this->channel.QRY_JUNCTION_AND.QRY_FILTER_JOBS_TYPE.$this->job_type.QRY_LIMIT.$this->appConfig->structure['channels'][$this->channel]['bulk_size'], Array());
             $response = $this->dbConn->execQry();
             if(empty($response)){
                 $response = null;
             }
         } catch (Exception $gException) {
            $this->logger->writeLog(LOGERROR, $this->logger->logModule, $this->logger->logModule.' fail getting pool '.$this->pool.' for channel '.$this->channel.' and job type '.$this->job_type.'.');
            $this->logger->writeLog(LOGDEBUG, $this->logger->logModule, $this->logger->logModule.' fail getting pool '.$this->pool.' for channel '.$this->channel.' and job type '.$this->job_type.'.',$gException);
            $response = false;
         }
         return $response;
     }
     
     public function setTaskStatus($vTaskId, $vTaskStatus){
         
     }
     
     public function cleanTasks($vWipe = false){
         
     }
     
     public function putTask($vRegister){
         
     }
     
     public function putTasks($vRegisters){
         
     }

}


