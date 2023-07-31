<?php
include_once 'dbClass.php';
include_once 'configClass.php';
include_once 'constants.php';
include_once 'LogClass.php';
include_once 'socketClass.php';
class gBankAgency {
    private $config;
    private $db;
    private $lastResponse;
    private $codeDTO;
    
    function __construct($vPartner_id, $vUser_id) {
        $this->config = new configLoader('../config/purchasecode_application.json');
        $this->db = new dbRequest($this->config->structure['database']['dbtype'], 
                                    $this->config->structure['database']['dbhost'],
                                    $this->config->structure['database']['dbport'],
                                    $this->config->structure['database']['dbname'],
                                    $this->config->structure['database']['dbuser'],
                                    $this->config->structure['database']['dbpass']);
        $this->codeDTO['partner_id'] = $vPartner_id;
        $this->codeDTO['user_id'] = $vUser_id;
    }
    
    // PRIVATE FUNCTIONS ******************************************************************
    private function cancelUniqueCustormerCodes($forUniqueCustomer = false){
         try {
              ($forUniqueCustomer) ? $this->db->setQuery(DTO_CANCEL_UNIQUE_CUSTOMER_CODES, Array($this->codeDTO['id'], $this->codeDTO['unique_customer_id'], $this->codeDTO['partner_id'])) : 
                                     $this->db->setQuery($query, $parameters);
              $result = $this->db->execQry();
         } catch (Exception $ex) {
              echo $ex->getTraceAsString();
              $result = false;
         }
         return $result;
    } //Done.
    
    private function validateUniqueCustomer(){
         try {
              $this->db->setQuery(DTO_VALIDATE_UNIQUE_CUSTOMER, Array($this->codeDTO['id'], $this->codeDTO['unique_customer_id'], $this->codeDTO['partner_id']));
              $count = $this->db->execQry();
              $result = ($count == 0) ? true : 
                      $result = ($this->codeDTO['unique_customer_id'] == null or trim($this->codeDTO['unique_customer_id']) == "" or !isset($this->codeDTO['unique_customer_id'])) ? true : false;
         } catch (Exception $ex) {
              echo $ex->getTraceAsString();
              $result = false;
         }
         return $result;
    } //Done.
    
    private function getCodeSequence(){
         try {
              $this->db->setQuery(DTO_GET_CODE_SEQUENCE, Array());
              $result = $this->db->execQry();
         } catch (Exception $ex) {
              echo $ex->getTraceAsString(); 
              $result[0]['nextval'] = null;
         }
         return $result[0]['nextval'];
    } //Done.
    
    private function registryRequest($vRequest){
        try {
             $this->codeDTO['id'] = $this->getCodeSequence();
             if(!empty($this->codeDTO['id'])) {
                  $this->codeDTO['request_id'] = $vRequest['request_id'];
                  $this->codeDTO['unique_customer_id'] = $vRequest['unique_customer_id'];
                  $this->codeDTO['amount'] = $vRequest['amount'];
                  $this->codeDTO['currency'] = $vRequest['currency'];
                  $this->codeDTO['lifetime'] = $vRequest['lifetime'];
                  $this->codeDTO['merchant_id'] = (isset($vRequest['merchant_id'])) ? $vRequest['merchant_id'] : null;
                  $this->codeDTO['terminal_id'] = (isset($vRequest['terminal_id'])) ? $vRequest['terminal_id'] : null;
                  $this->codeDTO['creation_date'] = date('YmdHis');
                  $this->codeDTO['status'] = CODE_STATUS_PENDING;
                  $this->db->setQuery(DTO_INSERT_CODE_REQUEST, Array($this->codeDTO['id'],
                                                                      $this->codeDTO['partner_id'],
                                                                      $this->codeDTO['user_id'],
                                                                      $this->codeDTO['request_id'],
                                                                      $this->codeDTO['unique_customer_id'],
                                                                      $this->codeDTO['amount'],
                                                                      $this->codeDTO['currency'],
                                                                      $this->codeDTO['lifetime'],
                                                                      $this->codeDTO['merchant_id'],
                                                                      $this->codeDTO['terminal_id'],
                                                                      $this->codeDTO['status']));
                  $result = $this->db->execQry();
             }else {
                  $result = false;
                  $this->lastResponse = Array("error_code"=>E_GENERAL, "payload"=>"");
             }
        } catch (Exception $ex) {
             echo $ex->getTraceAsString();
             $result = false;
                  $this->lastResponse = Array("error_code"=>E_GENERAL, "payload"=>"");
        }
        return $result;
    }// TODO: EXCEPTIONS MERCHANTS Y TERMINALES
    
    private function activateCode(){
        try {
              $this->db->setQuery(DTO_ACTIVATE_CODE, Array($this->codeDTO['id']));
              $result = $this->db->execQry();
         } catch (Exception $ex) {
              echo $ex->getTraceAsString();
              $result = false;
              $this->lastResponse = Array("error_code"=>E_GENERATING_CODE, "payload"=>"");
         }
         return $result;
    } //Done.
    
    private function setExpireDate(){
         try {
              $this->db->setQuery(DTO_UPDATE_CODE_EXPIRE_DATE.$this->codeDTO['lifetime'].DTO_UPDATE_CODE_EXPIRE_INTERVAL_SECONDS.DTO_UPDATE_CODE_EXPIRE_TRAIL, Array($this->codeDTO['id']));
              $result = $this->db->execQry();
              $this->codeDTO['expire_date'] = date('YmdHis', strtotime($this->codeDTO['creation_date'].'+'.$this->codeDTO['lifetime'].' seconds'));
         } catch (Exception $ex) {
              echo $ex->getTraceAsString();
              $result = false;
              $this->lastResponse = Array("error_code"=>E_GENERATING_CODE, "payload"=>"");
         }
         return $result;
         
    }//Done.
    
    private function randCode($vLenght){
         $vcode = "";
         try {
              $genRadix = 10;
              $bytesGen = [$vLenght];
              for ($i = 0; $i < $vLenght; $i++) {
                    $bytesGen[$i] = hexdec(bin2hex(random_bytes(1))) % $genRadix;
                    $vcode .=$bytesGen[$i];
               }
         } catch (Exception $ex) {
              echo $ex->getTraceAsString();
              $vcode = null;
         }
         return $vcode;
     }//Done.
    
    private function generateCode(){
         try {
              $tempCode = $this->randCode(CODE_LENGHT);
              $tempRef = $this->randCode(REFERENCE_LENGHT);
              if($tempCode <> null and $tempRef <> null){
                   $this->codeDTO['reference_id'] = $tempRef;
                   $this->codeDTO['purchase_code'] = $tempCode;
                   $this->db->setQuery(DTO_UPDATE_PURCHASE_CODE_REFERENCE, Array($this->codeDTO['reference_id'], $this->codeDTO['purchase_code'], $this->codeDTO['id']));
                   $result = $this->db->execQry();
              }else {
                   $result = false;
                   $this->lastResponse = Array("error_code"=>E_GENERATING_CODE, "payload"=>"");
              }
         } catch (Exception $ex) {
              echo $ex->getTraceAsString();
              $result = false;
              $this->lastResponse = Array("error_code"=>E_GENERATING_CODE, "payload"=>"");
         }
         return $result;
    }//Done.
    
    
    //PUBLIC FUNCTIONS ********************************************************************
    public function xml($vHttpRequest) {
        try {
             if($this->registryRequest($vHttpRequest['body'])){
                  $result = ($this->validateUniqueCustomer()) ? 
                          $result = (!$this->generateCode()) ? false : 
                          $result = (!$this->setExpireDate()) ? false : $this->activateCode()
                          : 
                          $result = (!$this->cancelUniqueCustormerCodes()) ? false : 
                          $result = (!$this->generateCode()) ? false : 
                          $result = (!$this->setExpireDate()) ? false : 
                          $result = (!$this->activateCode()) ? false : true;
                  $this->lastResponse = ($result) ? Array("error_code"=>PROC_OK, "payload"=>Array("reference_id"=>$this->codeDTO['reference_id'],
                                                                                                    "purchase_code"=>$this->codeDTO['partner_id'].$this->codeDTO['purchase_code'],
                                                                                                    "expire_datetime"=>$this->codeDTO['expire_date'])) : Array("error_code"=>E_GENERATING_CODE, "payload"=>"");
             }else{
                  $result = false;
                  $this->lastResponse = Array("error_code"=>E_GENERATING_CODE, "payload"=>"");
             }
        } catch (Exception $ex) {
             echo $ex->getTraceAsString();
             $result = false;
             $this->lastResponse = Array("error_code"=>E_GENERATING_CODE, "payload"=>"");
        }
        return $this->lastResponse;
    }
    
    
 //End of the Class   
 }
?>
