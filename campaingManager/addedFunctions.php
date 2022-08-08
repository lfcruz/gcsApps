<?php
// Global Variables
    $mwHeader = array('Accept: application/json',
                      'Content-Type: application/json',
                      'UserId: demo',
                      'Authentication: X253G4TRJYS4DSO12ZRV');


// Send Request ----------------------------------------------------------------
function do_post_request($url, $data, $optional_headers,$requestType)
{
  $urlResponse = null;
  switch ($requestType) {
    case 'GET': 
        $urlParams = array(CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $optional_headers);
            break;
    case 'POST':
        $urlParams = array(CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $optional_headers,
            CURLOPT_POSTFIELDS => $data);
            break;
    case 'PUT':
        $urlParams = array(CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $optional_headers);
            break;
    case 'DELETE':
        $urlParams = array(CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $optional_headers);
            break;
    default:
        break;
  }
  $urlResource = curl_init($url);
  if (!$urlResource) {
    echo("Problem stablishing resource.\n");
  } 
  else {
      curl_setopt_array($urlResource, $urlParams);
      $urlResponse = curl_exec($urlResource);
    if ($urlResponse === null) {
        echo("Problem reading data from URL.\n");
    }
    curl_close($urlResource);
  }
  return $urlResponse;
}

function dbpg_query($dbpgStructure)
{
    // Connecting to Database ------------------------------------------------------
    $connectorString = "host=".$dbpgStructure['dbIP'].
                       " port=".$dbpgStructure['dbPort'].
                       " dbname=".$dbpgStructure['dbName'].
                       " user=".$dbpgStructure['dbUser'].
                       " password=".$dbpgStructure['dbPassword'];
    $dbConnector = pg_connect($connectorString);
    if (!$dbConnector){
        echo 'Failed connection.......'  . $dbpgStructure['dbIP'] . ' ' . $dbpgStructure['dbPort'];
    }
    else {
        pg_prepare($dbConnector,$dbpgStructure['dbQueryName'],$dbpgStructure['dbQuery']);
    }
    
    $queryResult = pg_execute($dbConnector,$dbpgStructure['dbQueryName'],$dbpgStructure['dbQueryVariables']);
    if (substr($dbpgStructure['dbQuery'], 0, 6) == 'select' and !$queryResult){
        echo 'Failed to get result......';
    }
    else {
        $recordString = pg_fetch_all($queryResult);
        if(!(substr($dbpgStructure['dbQuery'], 0, 6) == 'select')){
            pg_prepare($dbConnector,'commit','commit');
            pg_exec($dbConnector, 'commit');
        }
    }
    pg_close($dbConnector);
    return $recordString;
}

function dbora_query($dboraStructure)
{
    // Connecting to Database ------------------------------------------------------
    $connectorString = '(DESCRIPTION = (CONNECT_TIMEOUT=5) (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST ='.$dboraStructure['dbIP'].')(PORT = '.$dboraStructure['dbPort'].')))(CONNECT_DATA=(SID= '.$dboraStructure['dbName'].')))';
    $dbConnector = oci_connect($dboraStructure['dbUser'],$dboraStructure['dbPassword'],$connectorString);
    if (!$dbConnector){
        echo 'Failed connection.......';
    }
    else {
        $oraQuery = oci_parse($dbConnector,$dboraStructure['dbQuery']);
    }
    
    oci_execute($oraQuery);
    $recordString = oci_fetch_row($oraQuery);
    oci_close($dbConnector);
    return $recordString;
}

function sentToSocket($enviroment,$port,$msg){
  //create a socket to send message to core
    $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_connect($sock, $enviroment, $port);
    $sent = socket_write($sock, $msg->asXML(), strlen($msg->asXML()));

    //read response message from core
    $input = socket_read($sock, 1024);
    $dom = new DOMDocument;
    $dom->loadXML($input);
    if (!$dom) {
        $result = '9903';
    }
    else{
        $result = simplexml_import_dom($dom);
    }
    socket_close($sock);
    return $result;
}

function ldap_auth($user, $password, $group) {
        // Active Directory server
        $ldap_host = "172.22.1.5";

        // Active Directory DN
        $ldap_dn = "dc=gcs,dc=local";

        // Active Directory user group
        $ldap_user_group = $group; //"bcmGroup";

        // Active Directory manager group
        $ldap_manager_group = $group; //"bcmGroup";

        // Domain, for purposes of constructing $user
        $ldap_usr_dom = "@gcs.local";

        // connect to active directory
        $ldap = ldap_connect($ldap_host);

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION,3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS,0);

        // verify user and password
        if($bind = @ldap_bind($ldap, $user . $ldap_usr_dom, $password)) {
                // valid
                // check presence in groups
                $filter = "(sAMAccountName=" . $user . ")";
                $attr = array("memberof");
                $result = ldap_search($ldap, $ldap_dn, $filter, $attr) or exit("Unable to search LDAP server");
                $entries = ldap_get_entries($ldap, $result);
                ldap_unbind($ldap);

                // check groups
                foreach($entries[0]['memberof'] as $grps) {
                        // is manager, break loop
                        if (strpos($grps, $ldap_manager_group)) { $access = 2; break; }

                        // is user
                        if (strpos($grps, $ldap_user_group)) $access = 1;
                }

                if ($access != 0) {
                        // establish session variables
                        $_SESSION['user'] = $user;
                        $_SESSION['access'] = $access;
                        return true;
        return true;
                } else {
                        // user has no rights
                        return false;
                }

        } else {
                // invalid name or password
                return false;
        }
}

function bcmLogin($user) {
    $dbpgStructure = array ("dbIP" => "localhost",
        "dbPort" => "15432",
        "dbName" => "campaings",
        "dbUser" => "postgres",
        "dbPassword" => "T3mp0r4ldev",
        "dbQueryName" => "storeLogin",
        "dbQuery" => "insert into t_login (id,username,last_login) values (DEFAULT, $1, DEFAULT)",
        "dbQueryVariables" => array($user));
    dbpg_query($dbpgStructure);
}

function pgQResult($pgQry, $pgVARArray) {
    $pgQRYName = "qry_".rand(0,999999);
    $dbpgStructure = array ("dbIP" => "localhost",
        "dbPort" => "15432",
        "dbName" => "campaings",
        "dbUser" => "postgres",
        "dbPassword" => "T3mp0r4ldev",
        "dbQueryName" => $pgQRYName,
        "dbQuery" => $pgQry,
        "dbQueryVariables" => $pgVARArray);
    return dbpg_query($dbpgStructure);
}

function loadConfig() {
// Defining Variables ---------------------------------------------------------
    global $configStructure;
    $stringfile = "";
 
// Configuration file validation ----------------------------------------------
    if(file_exists(CONFIG_FILE)){
        $stringfile = file_get_contents(CONFIG_FILE);
        $configStructure = json_decode($stringfile,true);
    } else {
        $configStructure = array();
    }
    
        /* Configuration file structure
         * BCMdbIp
         * BCMdbPort
         * BCMdbName
         * BCMdbUser
         * BCMdbPassword
         * BCMQueue
         */
    return $configStructure;
}

function email($srcAddresses,$dstAddresses,$subject,$message){
    return mail($dstAddresses, $subject, $message, 'From: '.$srcAddresses);
}
?>
