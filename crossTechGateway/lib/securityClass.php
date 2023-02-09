<?php
include_once 'dbClass.php';
include_once 'configClass.php';
include_once 'constants.php';
include_once 'LogClass.php';

class gSecure {
     private $config;
     private $db;
     private $userDTO;
     private $tokenBodyDTO;
     public $valid;
     public $tokenInfo;
     
     function __construct($vToken) {
          $this->config = new configLoader('../config/security_application.json');
          $this->db = new dbRequest($this->config->structure['database']['dbtype'], 
                                    $this->config->structure['database']['dbhost'],
                                    $this->config->structure['database']['dbport'],
                                    $this->config->structure['database']['dbname'],
                                    $this->config->structure['database']['dbuser'],
                                    $this->config->structure['database']['dbpass']);
          $this->getTokenComponent($vToken);
          $this->valid = $this->validateToken();
     }
    //PRIVATE FUNCTIONS *******************************************************************
     private function verifyUser($vPartner,$vUsername, $vPassword){
          try {
               $this->db->setQuery(DTO_USER_VALIDATION, Array($vUsername, $vPartner));
               $this->userDTO = $this->db->execQry();
               $result = (!empty($this->userDTO) and $this->userDTO[0]['secured'] == hash_hmac('sha512', $vPassword, DTO_GET_DATA2)) ? true : false;
          } catch (Exception $ex) {
               echo $ex->getTraceAsString();
               $result = false;
          }
         return $result; 
          
     }
     
     private function getTokenComponent($vToken){
          $rawComponents = explode(".", $vToken);
          $tempBody = json_decode(base64_decode(urldecode($rawComponents[1])), true);
          $tempExp = date_create($tempBody['exp']);
          $this->valid = ($tempExp > date_create("now")) ? true : false;
          $this->tokenInfo['header'] = (!empty($rawComponents[0])) ? $rawComponents[0] : "";
          $this->tokenInfo['body'] = (!empty($rawComponents[1])) ? $rawComponents[1] : "";
          $this->tokenInfo['signature'] = (!empty($rawComponents[2])) ? $rawComponents[2] : "";
          unset($tempBody);
          unset($tempExp);
     }
     
     private function verifySingnature(){
          $result = ($this->valid) ?  openssl_verify($this->tokenInfo['header'].'.'.$this->tokenInfo['body'], base64_decode(urldecode($this->tokenInfo['signature'])), file_get_contents($this->config->structure['token']['rsa_public_file']), OPENSSL_ALGO_SHA512) : false;
          return $result;
     }
     
     private function generateTokenHeader(){
          return urlencode(base64_encode(json_encode($this->config->structure['token']['header'])));
     }
     
     private function generateTokenBody(){
          try {
               $this->db->setQuery(DTO_USER_PERMITS, Array($this->userDTO[0]['user_id']));
               $userPermits = $this->db->execQry();
               $this->tokenBodyDTO = $this->config->structure['token']['body'];
               $this->tokenBodyDTO['sub'] = ($this->userDTO[0]['ispartnerbase']) ? $this->userDTO[0]['partner_id'] : $this->userDTO[0]['partner_parent_id'];
               $this->tokenBodyDTO['sna'] = $this->userDTO[0]['partner_code'];
               $this->tokenBodyDTO['uid'] = $this->userDTO[0]['user_id'];
               $this->tokenBodyDTO['una'] = $this->userDTO[0]['username'];
               $this->tokenBodyDTO['ist'] = date('YmdHis');
               $this->tokenBodyDTO['nbf'] = date('YmdHis', strtotime(date('YmdHis').'+1 seconds'));
               $this->tokenBodyDTO['exp'] = date('YmdHis', strtotime($this->tokenBodyDTO['nbf'].'+'.$this->config->structure['token']['default_lifetime'].' seconds'));
               $this->tokenBodyDTO['jti'] = uniqid($this->tokenBodyDTO['sub'].'-'.$this->userDTO[0]['username'].'-', true);
               foreach ($userPermits as $key=>$permit){
                    $this->tokenBodyDTO['aud'][$permit['permits']] = true;
               }
          } catch (Exception $ex) {
               echo $ex->getTraceAsString();
          }
          return urldecode(base64_encode(json_encode($this->tokenBodyDTO)));
     }
     
     private function generateTokenSignature(){
          openssl_sign($this->tokenInfo['header'].'.'.$this->tokenInfo['body'], $signature, openssl_get_privatekey(file_get_contents($this->config->structure['token']['rsa_private_file']), DTO_GET_DATA), OPENSSL_ALGO_SHA512);
          return urlencode(base64_encode($signature));
          
     }
     
     private function validateToken(){
        try {
             return $this->verifySingnature();
           
        } catch (Exception $ex) {
             echo $ex->getTraceAsString();
        }
    }

    //PUBLIC FUNCTIONS ********************************************************************
    
    
    public function getToken($vHttpRequest){
         $response = Array("error_code"=>E_GENERAL, "payload"=>"");
         try {
              $userInfo = $this->verifyUser($vHttpRequest['body']['partner_id'], 
                                            $vHttpRequest['body']['username'],
                                            $vHttpRequest['body']['password']);
              if($userInfo){
                   $this->tokenInfo['header'] = $this->generateTokenHeader();
                   $this->tokenInfo['body'] = $this->generateTokenBody();
                   $this->tokenInfo['signature'] = $this->generateTokenSignature();
                   $response = Array("error_code"=>PROC_OK, "payload"=>Array("token"=>$this->tokenInfo['header'].'.'.$this->tokenInfo['body'].'.'.$this->tokenInfo['signature']));
              }else {
                   $response = Array("error_code"=>E_AUTHORIZATION, "payload"=>"");
              }         
         } catch (Exception $ex) {
              echo $ex->getTraceAsString();
              $response = Array("error_code"=>E_GENERAL, "payload"=>"");
         }
         
         return $response;
    }
    
    public function setPassword($vHttpRequest){
         $response = Array("error_code"=>E_GENERAL, "payload"=>"");
         try {
              $userInfo = $this->verifyUser($vHttpRequest['body']['partner_id'], 
                                            $vHttpRequest['body']['username'],
                                            $vHttpRequest['body']['password']);
              if($userInfo){
                   $this->tokenInfo['header'] = $this->generateTokenHeader();
                   $this->tokenInfo['body'] = $this->generateTokenBody();
                   $this->tokenInfo['signature'] = $this->generateTokenSignature();
                   $response = Array("error_code"=>PROC_OK, "payload"=>Array("token"=>$this->tokenInfo['header'].'.'.$this->tokenInfo['body'].'.'.$this->tokenInfo['signature']));
              }else {
                   $response = Array("error_code"=>E_AUTHORIZATION, "payload"=>"");
              }         
         } catch (Exception $ex) {
              echo $ex->getTraceAsString();
              $response = Array("error_code"=>E_GENERAL, "payload"=>"");
         }
         
         return $response;
    }

    
 //End of the Class   
 }
?>
