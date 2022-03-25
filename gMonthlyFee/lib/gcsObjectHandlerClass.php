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

class promoProfile {
    public $promoList;
    private $dbLinkCustomer;
    private $conf;
    private $log;
    
    function __construct() {
        $this->log = new Logger();
        $this->conf = new configLoader('config/db.json');
        $this->dbLinkCustomer = new dbRequest($this->conf->structure['promodb']['dbtype'],
                                           $this->conf->structure['promodb']['dbhost'],
                                           $this->conf->structure['promodb']['dbport'],
                                           $this->conf->structure['promodb']['dbname'],
                                           $this->conf->structure['promodb']['dbuser'],
                                           $this->conf->structure['promodb']['dbpass']);
     
        return $this->loadList();
    }
    
    // PRIVATE FUNCTIONS ******************************************************************
    private function loadList(){
        $mainQuery = "select document_id, document_type from customer_accounts where bank_code is null";
        //var_dump($mainQuery);
        //var_dump($arraySelector);
        $this->dbLinkCustomer->setQuery($mainQuery, Array());
        $qryData = $this->dbLinkCustomer->execQry();
        if(!empty($qryData)){
            $this->promoList = $qryData;
            return true;
        }else {
            $this->profileData = Array();
            return false;
        }
    }
    
    private function sendMessage($vMessage){
        $sockClient = new socketProcessor('10.225.192.36', 8888, 'C');
        $response = $sockClient->sendMessage($vMessage);
        unset($sockClient);
        return $response;
    }
    
    //PUBLIC FUNCTIONS ********************************************************************
    public function updateAccount($vDocumentId, $vDocumentType, $vAccountNumber, $vAccountType, $vBankCode){
        $this->dbLinkCustomer->setQuery("update customer_accounts set account_number = $1, account_type = $2, bank_code = $3 "
                . "where document_id = $4 and document_type = $5", Array($vAccountNumber, $vAccountType, $vBankCode, $vDocumentId, $vDocumentType));
        $result = $this->dbLinkCustomer->execQry();
        return $result;
    } 
    
    public function sendTransactions($vChunkSize){
        try{
            $vChunkSize = ($vChunkSize === 0) ? (int) strval('100') : $vChunkSize;
            $domRequest = new DOMDocument;
            $domRequest->loadXML(MSG419);
            $buildMessage = simplexml_import_dom($domRequest);
            $this->dbLinkCustomer->setQuery("select ca.document_id as document_id,
                                                    ca.document_type as document_type,
                                                    ca.account_number as account_number,
                                                    ca.account_type as account_type,
                                                    ba.bank_code as bank_code,
                                                    ba.bank_id as bank_id,
                                                    ca.cashback_amount as amount,
                                                    ca.promo_type as promo_type,
                                                    sa.partnerid as partner_id,
                                                    sa.agencyid as agency_id,
                                                    sa.terminalid as terminal_id,
                                                    sa.shiftid as shift_id,
                                                    sa.username as username,
                                                    sa.country as country,
                                                    sa.channel as channel
                                                from customer_accounts ca
                                                inner join banks ba on (ba.bank_code = ca.bank_code)
                                                full outer join sab_agency sa on (sa.active is true)
                                                where ca.account_number is not null and ca.is_sent is not true
                                                limit $1", Array($vChunkSize));
            $data = $this->dbLinkCustomer->execQry();
            if(!empty($data)){
                foreach($data as $record){
                    $vCorrelationId = date('YmdHis').rand(10,99);
                    $vGcsSequenceNo = rand(100000,999999).date('dmYHis');
                    $buildMessage['CORRELATIONID'] = $vCorrelationId;
                    $buildMessage['COUNTRY'] = $record['country'];
                    $buildMessage['CHANNEL'] = $record['channel'];
                    $buildMessage['PARTNERID'] = $record['partner_id'];
                    $buildMessage['AGENCYID'] = $record['agency_id'];
                    $buildMessage['TERMINALID'] = $record['terminal_id'];
                    $buildMessage['SHIFTID'] = $record['shift_id'];
                    $buildMessage['USERNAME'] = $record['username'];
                    $buildMessage->CLIENT['ID'] = $record['document_id'];
                    $buildMessage->CLIENT['TYPE'] = $record['document_type'];
                    $buildMessage->TRANSACTION['DATE'] = date('dmY');
                    $buildMessage->TRANSACTION['TIME'] = date('His');
                    $buildMessage->TRANSACTION['BANKID'] = $record['bank_id'];
                    $buildMessage->TRANSACTION['ACCOUNT-TO-NUMBER'] = $record['account_number'];
                    $buildMessage->TRANSACTION['ACCOUNT-TO-TYPE'] = $record['account_type'];
                    $buildMessage->TRANSACTION['AMOUNT'] = $record['amount'];
                    $buildMessage->TRANSACTION['BANKSESSIONID'] = $vGcsSequenceNo;
                    $domResponse = new DOMDocument;
                    $domResponse->loadXML($this->sendMessage($buildMessage->asXML()));
                    $responseMessage = simplexml_import_dom($domResponse);
                    //var_dump($responseMessage);
                    $vResult = ($responseMessage->TRANSACTION['RESPONSECODE'] == '0000') ? 1 : 0;
                    //var_dump($vResult);
                    $this->dbLinkCustomer->setQuery('update customer_accounts 
                        set is_sent = $1,
                            gcs_sequence_no = $2,
                            bp_sequence_no = $3,
                            response_code = $4,
                            correlation_id = $5
                    where document_id = $6
                      and document_type = $7
                      and account_number = $8
                      and account_type = $9
                      and bank_code = $10
                      and promo_type = $11
                      and is_sent is not true', Array($vResult,
                                                  $vGcsSequenceNo,
                                                  $responseMessage->TRANSACTION['BPSEQUENCENO'],
                                                  $responseMessage->TRANSACTION['RESPONSECODE'],
                                                  $vCorrelationId,
                                                  $record['document_id'],
                                                  $record['document_type'],
                                                  $record['account_number'],
                                                  $record['account_type'],
                                                  $record['bank_code'],
                                                  $record['promo_type']));
                    $this->dbLinkCustomer->execQry();
                    unset($domResponse);
                    unset($responseMessage);
                }
            }
        } catch (Exception $ex) {
            var_dump($ex);
        }   
    }
 //End of the Class   
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
                    $result = ($this->engConfig->structure[$this->channel]['queues']['1']['bot_days'] == 0) ? 
                        ($this->engConfig->structure[$this->channel]['queues']['1']['top_days'] > $this->engConfig->structure[$this->channel]['queues']['1']['bot_days']) ? 
                        ($this->engConfig->structure[$this->channel]['queues']['2']['bot_days'] > $this->engConfig->structure[$this->channel]['queues']['1']['top_days']) ?
                        ($this->engConfig->structure[$this->channel]['queues']['2']['top_days'] > $this->engConfig->structure[$this->channel]['queues']['2']['bot_days']) ?
                        ($this->engConfig->structure[$this->channel]['queues']['3']['bot_days'] > $this->engConfig->structure[$this->channel]['queues']['2']['top_days']) ?
                        ($this->engConfig->structure[$this->channel]['queues']['3']['top_days'] > $this->engConfig->structure[$this->channel]['queues']['3']['bot_days']) ? true : false
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
        //$qryParameters = "select GEN_PARAMETER_NAME, PARAM_DISPLAY_NAME, PARAMETER_VALUE from  GCSGENERIC_PARAMS_M where GEN_PARAMETER_NAME in ($1)";
        $qryParameters = "select GEN_PARAMETER_NAME, PARAM_DISPLAY_NAME, PARAMETER_VALUE from  GCSGENERIC_PARAMS_M where GEN_PARAMETER_NAME in (".$this->appConfig->structure['channels'][$this->channel]['mf_parameters'].")";
        $this->logger->writeLog(LOGINFO, $this->logger->logModule, 'Getting General Parameters......');
        try {
            //$this->dbConn->setQuery($qryParameters, Array($this->appConfig->structure['channels'][$this->channel]['mf_parameters']));
            $this->dbConn->setQuery($qryParameters, Array());
            $this->channelGeneralParameters = $this->dbConn->execQry();
            if(empty($this->channelGeneralParameters)){
                $this->logger->writeLog(LOGERROR, $this->logger->logModule, 'No general parameters found for channel '.$this->channel);
                return false;
            }
        } catch (Exception $gException) {
                $this->logger->writeLog(LOGERROR, $this->logger->logModule, 'There was an exception getting general parameters for channel '.$this->channel);
                $this->logger->writeLog(LOGDEBUG, $this->logger->logModule, 'There was an exception getting general parameters for channel '.$this->channel, $gException);
                return false;
        }
        //$this->logger->writeLog(LOGTRACE, $this->logger->logModule, var_dump(var_dump($this->channelGeneralParameters)));
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
            //$this->logger->writeLog(LOGTRACE, $this->logger->logModule, var_dump($this->channelEngine));
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