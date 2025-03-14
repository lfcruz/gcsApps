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
     private function base64url($data, $action = true){
         return ($action) ? rtrim(strtr(base64_encode($data), '+/', '-_'), '=') : base64_decode(strtr($data, '-_', '+/'));
     }
     
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
          $this->tokenInfo['header'] = (!empty($rawComponents[0])) ? $rawComponents[0] : "";
          $this->tokenInfo['body'] = (!empty($rawComponents[1])) ? $rawComponents[1] : "";
          $this->tokenInfo['signature'] = (!empty($rawComponents[2])) ? $rawComponents[2] : "";
          $this->tokenInfo['data'] = json_decode($this->base64url($this->tokenInfo['body'], false), true);
     }
     
     private function verifySingnature(){
         return openssl_verify($this->tokenInfo['header'].'.'.$this->tokenInfo['body'], $this->base64url($this->tokenInfo['signature'], false), file_get_contents($this->config->structure['token']['rsa_public_file']), OPENSSL_ALGO_SHA512);
     }
     
     private function generateTokenHeader(){
          return $this->base64url(json_encode($this->config->structure['token']['header']), true);
     }
     
     private function generateTokenBody(){
          try {
               $this->db->setQuery(DTO_USER_SCOPES, Array($this->userDTO[0]['user_id']));
               $vAUTH['scopes'] = $this->db->execQry();
               $this->db->setQuery(DTO_USER_AUDIENCE, Array($this->userDTO[0]['user_id']));
               $vAUTH['audiences'] = $this->db->execQry();
               $this->db->setQuery(DTO_USER_ROLES, Array($this->userDTO[0]['user_id']));
               $vAUTH['roles'] = $this->db->execQry();
               $this->tokenBodyDTO = $this->config->structure['token']['body'];
               $this->tokenBodyDTO['sub'] = ($this->userDTO[0]['ispartnerbase']) ? $this->userDTO[0]['partner_id'] : $this->userDTO[0]['partner_parent_id'];
               $this->tokenBodyDTO['sna'] = $this->userDTO[0]['partner_code'];
               $this->tokenBodyDTO['uid'] = $this->userDTO[0]['user_id'];
               $this->tokenBodyDTO['una'] = $this->userDTO[0]['username'];
               $this->tokenBodyDTO['iat'] = strtotime(date('YmdHis'));
               //$this->tokenBodyDTO['nbf'] = date('YmdHis', strtotime(date('YmdHis').'+1 seconds'));
               //$this->tokenBodyDTO['exp'] = date('YmdHis', strtotime($this->tokenBodyDTO['nbf'].'+'.$this->config->structure['token']['default_lifetime'].' seconds'));
               $this->tokenBodyDTO['nbf'] = strtotime('+1 seconds');
               $this->tokenBodyDTO['exp'] = strtotime(date('YmdHis', $this->tokenBodyDTO['nbf']).'+'.$this->config->structure['token']['default_lifetime'].' seconds');
               $this->tokenBodyDTO['jti'] = uniqid($this->tokenBodyDTO['sub'].'-'.$this->userDTO[0]['username'].'-', true);
               $this->tokenBodyDTO['scopes'] = "";
               $this->tokenBodyDTO['aud'] = [];
               $this->tokenBodyDTO['roles'] = [];
               foreach($vAUTH['scopes'] as $key => $value){
                    //array_push($this->tokenBodyDTO['scopes'], $value['scopes']);
                    $this->tokenBodyDTO['scopes'] .= $value['scopes']." ";
               }
               foreach($vAUTH['audiences'] as $key => $value){
                    array_push($this->tokenBodyDTO['aud'], $value['audiences']);
               }
               foreach($vAUTH['roles'] as $key => $value){
                    array_push($this->tokenBodyDTO['roles'], $value['roles']);
               }
          } catch (Exception $ex) { 
               echo $ex->getTraceAsString();
          }
          return $this->base64url(json_encode($this->tokenBodyDTO), true);
     }
     
     private function generateTokenSignature(){
          openssl_sign($this->tokenInfo['header'].'.'.$this->tokenInfo['body'], $signature, openssl_get_privatekey(file_get_contents($this->config->structure['token']['rsa_private_file']), DTO_GET_DATA), OPENSSL_ALGO_SHA512);
          return $this->base64url($signature, true);
          
     }
     
     private function validateToken(){
        try {
             return ($this->tokenInfo['data']['exp'] > strtotime('now')) ? $this->verifySingnature() : false;
        } catch (Exception $ex) {
             echo $ex->getTraceAsString();
             return false;
        }
    }
    
     private function generateJWK(){
          $keyInfo = openssl_pkey_get_details(openssl_pkey_get_public(file_get_contents($this->config->structure['token']['rsa_public_file'])));
          //TODO: Multiples keys array builder
          $keys = ["kty" => "RSA",
                                   "alg" => $this->config->structure['token']['header']['alg'],
                                   "use" => "sig",
                                   "kid" => "be67dd1889ddc74501aebd5c170db8e89b8d38d7",
                                   "x5t" => "be67dd1889ddc74501aebd5c170db8e89b8d38d7",
                                   "n" => rtrim(str_replace(['+', '/'], ['-', '_'], base64_encode($keyInfo['rsa']['n'])), '='),
                                   "e" => rtrim(str_replace(['+', '/'], ['-', '_'], base64_encode($keyInfo['rsa']['e'])), '='),
                                   "x5c" => ["MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAu10B4bayhEiMbvHJ2sPF0tzTcDmwYvBU49Wvvtbn4T36wRhYGpy3+YAfuauxEgaNqSAAYDbDpd9eEmz19knIhpmYaGOeqB0eGFwymWmvgsundbLQAXNaKbHMViDHHjsoQSt5O4ggo720QUuonVakB8aLHp4k4+EZqo3wHe/3siVa/ohBk0uKKIjcb47udH9kcckKUzQupSG579kymDCOQicxzognQqftEIveVgWRx8SXRuswpb90YXyvuPkMAc/fmIgis3wFVX1v/NbBFxxC16p2nt8NLOd2xAnYS/29J1iFOmtI+1Vw89wTyhWib+EBe1cejDYVRAHmptC/UVs2HMs0XYoPjgB8dDjWK7dkUriur94H+i1Ii0ceDlC5H4qaIH7wKvUrN+YRX5yS0Pbapwr69CDF3jKy4uxx7IArckE/e8Nj9xC9A+Pxhj0wnYLfDrLvwD7ov+65YcduESgq0/FTSepd/ANPzL8SS7Z2NlXScfzcPbVf8IhoRw5JZnKLVobygwo5LMCYJgTq/a6C08isNdYrrrtuJZZ+fsBFhaSeq3OGLUUmDBv+RjFhFkjgRkawsFCxf7NKB38exCuMgYouDXsE6crNrdxRxzJ7aHg0S7dzaqXOawaEX5ahL0qxo9e1iAfzn8JupSEeW5TPpL+NsX8/P1AqrfCIqKj8tWkCAwEAAQ=="]
                                 ];
          $jsonData = ["keys" => [$keys]];
          return $jsonData;
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
    
    public function getJWK(){
         try {
              $response = Array("error_code"=>PROC_OK, "payload"=>$this->generateJWK());
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