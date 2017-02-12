<?php
include_once "configClass.php";
class ldapComm {
    private $config;
    private $ldapEntity;
    
    function __construct() {
        $this->config = new configLoader('../config/ldapProperties.json');
        $this->ldapEntity = ldap_connect($this->config->structure['ldap_host']);
        ldap_set_option($this->ldapEntity, LDAP_OPT_PROTOCOL_VERSION,3);
        ldap_set_option($this->ldapEntity, LDAP_OPT_REFERRALS,0);
    }

    public function authUser($user, $password) {
        try {
            $bind = ldap_bind($this->ldapEntity, $user.$this->config->structure['ldap_usr_dom'], $password);
            ldap_unbind($this->ldapEntity);
        } catch (Exception $ex) {
            error_log($ex->getMessage(), 3, '../log/ldap.log');
        }
        if($bind){
            return true;
        }else {
            return false;
        }
    }
    
    public function getGroups($user) {
        try {
            $filter = "(sAMAccountName=" . $user . ")";
            $attr = array("memberof");
            $result = ldap_search($this->ldapEntity, $this->config->structure['ldap_dn'], $filter, $attr);
            $entries = ldap_get_entries($this->ldapEntity, $result);
        } catch (Exception $ex) {
            //error log class catching
        }
        return $entries;
    }
}
?>