<?php
include_once 'dbClass.php';
include_once 'configClass.php';
include_once 'constants.php';
include_once 'socketClass.php';
include_once 'LogClass.php';
include_once 'httpClientClass.php';
class enrollment {
    private $dbLinkCustomer;
    private $dbLinkConnector;
    private $conf;
    private $log;
    private $enrollmentid;
    private $clientid;
    private $socketClient;
    
    function __construct() {
        $this->enrollmentid = null;
        $this->log = new Logger();
        $this->conf = new configLoader('conf/db.json');
        //$this->socketClient = new socketProcessor('10.225.192.50', 8887, 'C');
        $this->dbLinkCustomer = new dbRequest($this->conf->structure['customer']['dbtype'],
                                           $this->conf->structure['customer']['dbhost'],
                                           $this->conf->structure['customer']['dbport'],
                                           $this->conf->structure['customer']['dbname'],
                                           $this->conf->structure['customer']['dbuser'],
                                           $this->conf->structure['customer']['dbpass']);
        
        $this->dbLinkConnector = new dbRequest($this->conf->structure['connector']['dbtype'],
                                           $this->conf->structure['connector']['dbhost'],
                                           $this->conf->structure['connector']['dbport'],
                                           $this->conf->structure['connector']['dbname'],
                                           $this->conf->structure['connector']['dbuser'],
                                           $this->conf->structure['connector']['dbpass']);
        
        $this->addEnrollmentFile();
    }
    
    // PRIVATE FUNCTIONS ******************************************************************
    private function addEnrollmentFile(){
       $this->dbLinkCustomer->setQuery('insert into t_enrollment_master (enrollment_id, clients_count, account_count, success_count, failed_count) values (DEFAULT, 0,0,0,0)', []);
       if($this->dbLinkCustomer->execQry()){
           $this->dbLinkCustomer->setQuery('select max(enrollment_id) from t_enrollment_master', []);
           $result = $this->dbLinkCustomer->execQry();
           $this->enrollmentid = $result[0]['max'];
       }
       return $this->enrollmentid;
    }
    
    private function clientExist($vRecord){
        $this->dbLinkCustomer->setQuery('select client_id from t_client where document_id = $1 and phone_number = $2', $vRecord);
        $result = $this->dbLinkCustomer->execQry();
        $this->clientid = $result[0]['client_id'];
        return (!empty($result)) ? true : false;
    }
    
    private function addClient($vRecord){
        $this->dbLinkCustomer->setQuery('insert into t_client (client_id, document_id, document_type, phone_number, telco_id, name, last_name, enrollment_activation_code) '
                . 'values (DEFAULT, $1, $2, $3, $4, $5, $6, $7)', 
                $vRecord);
        if($this->dbLinkCustomer->execQry()){
            $this->dbLinkCustomer->setQuery('select max(client_id) from t_client', []);
            $result = $this->dbLinkCustomer->execQry();
            $this->clientid = $result[0]['max'];
        }
        return ($result) ? true : false;
    }
    
    private function addAccount($vRecord){
        $this->dbLinkCustomer->setQuery('insert into '
                . 't_enrollment_detail '
                . '(enrollment_id, client_id, account_number, account_type, account_alias, account_expire_date) '
                . 'values ($1, $2, $3, $4, $5, $6)', 
                $vRecord);
        return $this->dbLinkCustomer->execQry();
    }
    
    private function getCurrentEnrollmentId(){
        return $this->enrollmentid;
    }
    
    private function getEnrollmentClients(){
        $this->dbLinkCustomer->setQuery('select cl.* '
                . 'from t_client cl '
                . 'where cl.client_id in (select client_id from t_enrollment_detail edtl where edtl.enrollment_id = $1 '
                . 'group by edtl.client_id '
                . 'order by edtl.client_id)', [$this->enrollmentid]);
        return $this->dbLinkCustomer->execQry();
    }
    
    private function getEnrollmentClientAccounts($vClientid){
        $this->dbLinkCustomer->setQuery('select edtl.* '
                . 'from t_client cl '
                . 'inner join t_enrollment_detail edtl on edtl.client_id = cl.client_id and edtl.enrollment_id = $1 '
                . 'where cl.client_id = $2', [$this->enrollmentid, $vClientid]);
        return $this->dbLinkCustomer->execQry();
    }
    
    private function setEnrollmentResult($msgType, $client_id, $response_code){
        switch ($msgType) {
            case '860':
                $this->dbLinkCustomer->setQuery('update t_client set phone_validation_result = $1 where client_id = $2', [$response_code, $client_id]);
                break;
            case '800':
                $this->dbLinkCustomer->setQuery('update t_client set enrollment_result = $1 where client_id = $2', [$response_code, $client_id]);
                break;
            default:
                break;
        }
        return $this->dbLinkCustomer->execQry();
    }
    
    private function getSecurityCode($client_id){
        $this->dbLinkCustomer->setQuery('select enrollment_activation_code from t_client where client_id = $1', [$client_id]);
        $activation_code = $this->dbLinkCustomer->execQry();
        $salt = random_bytes(24);
        $activation_code_hash = hash_pbkdf2('sha1', $activation_code[0]['enrollment_activation_code'], $salt, 64000, 18);
        $hash_code = 'sha1:64000:18:'.base64_encode($salt).':'.base64_encode($activation_code_hash);
        return $hash_code;
    }
   
    private function getNextTelephoneId(){
        $this->dbLinkConnector->setQuery("select nextval('telephone_seq')", []);
        return $this->dbLinkConnector->execQry();
    }
    
    private function saveClientInfo($clientData){
        $result = false;
        $telcoVal = (strtoupper($clientData['telco_id']) == 'CLARO') ? TELCO_CLARO : TELCO_MOVISTAR;
        $this->dbLinkConnector->setQuery('insert into client (document_id, document_type, last_name, name) values ($1, $2, $3, $4)', [$clientData['document_id'],
            $clientData['document_type'],
            $clientData['last_name'],
            $clientData['name']]);
        if($this->dbLinkConnector->execQry()) {
            $security_code = $this->getSecurityCode($clientData['client_id']);
            $connector_telephone_id = $this->getNextTelephoneId();
            $this->dbLinkConnector->setQuery('insert into telephone (id, security_code, status, telephone_number, client_id, telco_id, security_code_creation_date, security_code_validated_date) '
                    . 'values ($1, $2, $3, $4, $5, $6, current_timestamp, $7)', [$connector_telephone_id[0]['nextval'], 
                        $security_code, 
                        'PA', 
                        $clientData['phone_number'], 
                        $clientData['document_id'],
                        $telcoVal,
                        null]);
            $result = $this->dbLinkConnector->execQry();
        }
        return $result;
    }
    
    private function getTelephoneId($phone_number, $document_id){
        $this->dbLinkConnector->setQuery('select id from telephone where telephone_number = $1 and client_id = $2', [$phone_number, $document_id]);
        return $this->dbLinkConnector->execQry();
    }
    
    private function saveAccountsInfo($accountData, $clientData){
        $telephone_id = $this->getTelephoneId($clientData['phone_number'], $clientData['document_id']);
        foreach ($accountData as $record => $data) {
            $this->dbLinkConnector->setQuery("insert into product (id, account_alias, account_number, account_type, currency, masked_account_number, telephone_id, expiration_date, status) "
                    . "values (nextval('product_seq'), $1, $2, $3, $4, $5, $6, $7, $8)", [$data['account_alias'],
                        $data['account_number'],
                        $data['account_type'],
                        'GTQ',
                        str_pad(substr($data['account_number'], -4), 8, '*', STR_PAD_LEFT),
                        $telephone_id[0]['id'],
                        null,
                        'PA']);
            $result = $this->dbLinkConnector->execQry();
        }
        return $result;
    }
    
    private function sentSocketMessage($vRoute, $vMsg){
        $routes = ["CORE" => ["HOST" => "10.225.192.50",
                              "PORT" => "8887"],
                   "CONECTOR" => ["HOST" => "10.100.150.80",
                                  "PORT" => "7078"]
                  ];
        $socketClient = new socketProcessor($routes[$vRoute]["HOST"], $route[$vRoute]["PORT"], TCP_CLIENT);
        $vMsg = str_replace(chr(10), '', $vMsg).chr(10);
        $response = $socketClient->sendMessage($vMsg);
        unset($socketClient);
        $dom = new DOMDocument;
        $dom->loadXML($response);
        return ($dom) ? simplexml_import_dom($dom) : false;
    }
    
    private function validatePhoneNumber($clientData){
        $result = false;
        $dom = new DOMDocument;
        $dom->loadXML(MSG860);
        $validPhoneMsg = ($dom) ? simplexml_import_dom($dom) : false;
        $validPhoneMsg["BANKID"] = "BDA";
        $validPhoneMsg["CORRELATIONID"] = date('YmdHis').bin2hex(random_bytes(5));
        $validPhoneMsg->CLIENT["ID"] = $clientData['document_id'];
        $validPhoneMsg->CLIENT["TYPE"] = $clientData['document_type'];
        $validPhoneMsg->CLIENT["TELEPHONE"] = $clientData['phone_number'];
        $validPhoneMsg->CLIENT["TELCOID"] = (strtoupper($clientData['telco_id']) == 'CLARO') ? TELCO_CLARO : TELCO_MOVISTAR;
        $validPhoneMsg->CLIENT["BPSEQUENCE"] = str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
        $validPhoneMsg->TRANSACTION["DATE"] = date('mdY');
        $validPhoneMsg->TRANSACTION["TIME"] = date('His');
        $responsePhoneMsg = $this->sentSocketMessage(ROUTE_TPAGO, $validPhoneMsg->asXML());
        if($responsePhoneMsg->TRANSACTION["RESPONSECODE"] == '0000' and $this->setEnrollmentResult('860', $clientData['client_id'], $responsePhoneMsg->TRANSACTION["RESPONSECODE"])){
            $result = $this->saveClientInfo($clientData);
        }
        return $result;
    }
    
    private function validateActivationCode($clientData){
        $result = false;
        $dom = new DOMDocument;
        $dom->loadXML(MSG815);
        $validPhoneMsg = ($dom) ? simplexml_import_dom($dom) : false;
        $validPhoneMsg["BANKID"] = "BDA";
        $validPhoneMsg["CORRELATIONID"] = date('YmdHis').bin2hex(random_bytes(5));
        $validPhoneMsg->CLIENT["ID"] = $clientData['document_id'];
        $validPhoneMsg->CLIENT["TYPE"] = $clientData['document_type'];
        $validPhoneMsg->CLIENT["TELEPHONE"] = $clientData['phone_number'];
        $validPhoneMsg->CLIENT["GCSSEQUENCE"] = str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
        $validPhoneMsg->TRANSACTION["DATE"] = date('mdY');
        $validPhoneMsg->TRANSACTION["TIME"] = date('His');
        $validPhoneMsg->TRANSACTION["SECURITYCODE"] = $clientData['activation_code'];
        $responsePhoneMsg = $this->sentSocketMessage(ROUTE_CONNECTOR, $validPhoneMsg->asXML());
        return $responsePhoneMsg->TRANSACTION["RESPONSECODE"];
    }
    
    private function enrollAccounts($clientData, $accountStructure){
        $dom = new DOMDocument;
        $dom->loadXML(MSG800);
        $enrollClientMsg =  ($dom) ? simplexml_import_dom($dom) : false;
        $enrollClientMsg["BANKID"] = "BDA";
        $enrollClientMsg["CORRELATIONID"] = date('YmdHis').bin2hex(random_bytes(5));
        $enrollClientMsg->CLIENT["ID"] = $clientData['document_id'];
        $enrollClientMsg->CLIENT["TYPE"] = $clientData['document_type'];
        $enrollClientMsg->CLIENT["TELEPHONE"] = $clientData['phone_number'];
        $enrollClientMsg->CLIENT["TELCOID"] = (strtoupper($clientData['telco_id']) == 'CLARO') ? TELCO_CLARO : TELCO_MOVISTAR;
        $enrollClientMsg->CLIENT["BPSEQUENCE"] = str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
        $accountCounter = 1;
        foreach ($accountStructure as $record => $data){
            $enrollClientMsg->PRODUCTS->PRODUCT["ID"] = str_pad(strval($accountCounter), 2, '0', STR_PAD_LEFT);
            $enrollClientMsg->PRODUCTS->PRODUCT["ACCOUNT"] = str_pad(substr($data['account_number'], -4), 10, '*', STR_PAD_LEFT);
            $enrollClientMsg->PRODUCTS->PRODUCT["TYPE"] = $data['account_type'];
            $enrollClientMsg->PRODUCTS->PRODUCT["CURRENCY"] = "GTQ";
            $enrollClientMsg->PRODUCTS->PRODUCT["ALIAS"] = $data['account_alias'];
        }
        $enrollClientMsg->TRANSACTION["DATE"] = date('mdY');
        $enrollClientMsg->TRANSACTION["TIME"] = date('His');
        $responseEnrollMsg = $this->sentSocketMessage(ROUTE_TPAGO, $enrollClientMsg->asXML());
        if($responseEnrollMsg->TRANSACTION["RESPONSECODE"] == '0000' and $this->setEnrollmentResult('800', $clientData['client_id'], $responseEnrollMsg->TRANSACTION["RESPONSECODE"])){
            $result = $this->saveAccountsInfo($accountStructure, $clientData);
        }
        return $result;
    }
    //document_id,document_type,name,last_name,telephone_number,telco_name,account_number,account_type,account_alias,account_expire_date
   
    //PUBLIC FUNCTIONS ********************************************************************
    public function loadRecord($enrollRecord){
        $validData = [$enrollRecord['document_id'], $enrollRecord['telephone_number']];
        $clientData = [$enrollRecord['document_id'],
            $enrollRecord['document_type'],
            $enrollRecord['telephone_number'],
            $enrollRecord['telco_name'],
            $enrollRecord['name'],
            $enrollRecord['last_name'],
            strval(random_int(1000, 9999))];
        if(!empty($this->enrollmentid)){
            if($this->clientExist($validData)){
                $accountData = [$this->enrollmentid, $this->clientid, $enrollRecord['account_number'], $enrollRecord['account_type'], $enrollRecord['account_alias'], null, null, null];
                $result = $this->addAccount($accountData);
            } else {
                $result = $this->addClient($clientData);
                $accountData = [$this->enrollmentid, $this->clientid, $enrollRecord['account_number'], $enrollRecord['account_type'], $enrollRecord['account_alias'], null];
                $result = $this->addAccount($accountData);
            }
        }
        return $result;
    }
    
    public function doEnrollment(){
        $clientList = $this->getEnrollmentClients();
        foreach ($clientList as $record => $data){
            echo "Client : ".$data['document_id']." ------------------------ Phone : ".$data['telephone_number']."\n";
            $enrollmentClientAccounts = $this->getEnrollmentClientAccounts($data['client_id']);
            $result = ($this->validatePhoneNumber($data)) ? 
                    ($this->enrollAccounts($data, $enrollmentClientAccounts))  : false;
            sleep(1);
        }
        return $result;
    } 
    
    public function doReactivation(){
        $httpClient = new httpClient();
        $httpClient->setURL('http://10.225.192.130:8077/connector/api/pin/regeneration');
        $this->dbLinkCustomer->setQuery('select * from t_client where regenerate_code = $1', [true]);
        $lista = $this->dbLinkCustomer->execQry();
        var_dump($lista);
        if(!empty($lista)){
            foreach ($lista as $recode => $data){
                $telco = ($data['telco_id'] == 'Claro') ? "300" : "500";
                $client = ["id" => $data['document_id'],
                           "documentType" => $data['document_type'],
                           "telephone" => $data['phone_number'],
                           "telcoId" => $telco,
                           "name" => $data['name'],
                           "lastName" => $data['last_name']];
                $jclient = json_encode($client);
                $httpResponse = $httpClient->httpRequest('POST', ["Content-Type: application/json"], $jclient);
                $response = json_decode($httpResponse,true);
                error_log($data['document_id'].",".$data['phone_number'].",".$data['name'].",".$data['last_name'].",".$response['code'], 3, 'log/ResultFile.csv');
                echo($data['document_id'].",".$data['phone_number'].",".$data['name'].",".$data['last_name'].",".$response['code']);
            }
        }
    }
    
    public function doActivationCodeValidation($enrollRecord){
        $result = $this->validateActivationCode($enrollRecord);
        return $result;
    }
 //End of the Class   
 }
?>
<MESSAGE TYPE="815" CORRELATIONID="20191023309383" BANKID="BDA">
<CLIENT ID="1582654160208" TYPE="DPI" GCSSEQUENCE="10665" TELEPHONE="58618264"/>
<TRANSACTION DATE="23102019" TIME="174445" SECURITYCODE="0533"/>
</MESSAGE>