<?php
include_once 'dbClass.php';
include_once 'configClass.php';
include_once 'constants.php';
include_once "paymentsClass.php";
include_once "httpClientClass.php";
include_once 'LogClass.php';

class gSecure {
    //PRIVATE FUNCTIONS *******************************************************************

    //PUBLIC FUNCTIONS ********************************************************************
    public function validateToken($vToken){
        try {
           
        } catch (Exception $ex) {
            return false;
        }
        return true;
    }
    
    public function getToken(){
         
    }
    
    public function setPassword(){
         
    }

    
 //End of the Class   
 }
?>
