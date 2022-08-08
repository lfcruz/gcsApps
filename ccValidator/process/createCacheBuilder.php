<?php
include_once '../lib/dbClass.php';
include_once '../lib/configClass.php';
include_once '../lib/constants.php';
include_once '../lib/socketServer.php';


$appconf = new configLoader('../config/ccVConf.json');
$dbconf = new configLoader('../config/db.json');
$dbLink = new dbRequest($dbconf->structure['dbtype'],
                                           $dbconf->structure['dbhost'],
                                           $dbconf->structure['dbport'],
                                           $dbconf->structure['dbname'],
                                           $dbconf->structure['dbuser'],
                                           $dbconf->structure['dbpass']);

function getBankBin($vRecord){
    global $appconf;
    $base540msg = '<MESSAGE TYPE="540" BANKID="'.$vRecord['bankid'].'" CORRELATIONID="'.date('YmdHis'). random_int(1000, 9999).'"><CLIENT ID="'.$vRecord['documentid'].'" TYPE="'.$vRecord['documenttype'].'" GCSSEQUENCE="'.date('YmdHis'). random_int(100, 999).'" TELEPHONE="'.$vRecord['telephone'].'" TOKEN="000000000000000000000000" PINVERIFICATION="0" PINCAPTUREFLAG="0"/><TRANSACTION ACCOUNT="'.$vRecord['accountnumber'].'" TYPE="'.$vRecord['accounttype'].'" CURRENCY="'.$vRecord['accountcurrency'].'" DATE="'.date('dmY').'" TIME="'.date('His').'"/></MESSAGE>';
    try {
        $routerSocket = new socketProcessor($appconf->structure['bankRouterHost'], $appconf->structure['bankRouterPort'], SOCKET_CLIENT);
        $xmlResponse = $routerSocket->sendMessage($base540msg);

        $domIn = new DOMDocument;
        $domIn->loadXML($xmlResponse);
        $messageIn = simplexml_import_dom($domIn);
        if($messageIn->TRANSACTION["RESPONSECODE"] == '0000'){
            $ccBin = substr($messageIn->TRANSACTION["ACCOUNT"], 0, 10);
        }else {
            $ccBin = null;
        }
    }catch(Exception $e){
        
    }
        
    unset($messageIn);
    unset($domIn);
    unset($xmlResponse);
    unset($routerSocket);

    return $ccBin;
}

while($appconf->structure['appStatus']){
    $dbLink->setQuery('select bankid, documentid, documenttype, telephone, accountnumber, accounttype, accountcurrency from update_cache where actiontype = $1 order by creationdate limit $2', Array(CACHE_CREATE, $appconf->structure['updateRecordsLimit']));
    $vRecords = $dbLink->execQry();
    if($vRecords){
        foreach ($vRecords as $vRecord){
            $ccAccountBin = getBankBin($vRecord);
            try {
                $dbLink->startTransactions();
                $dbLink->setQuery('insert into bin_cache values ($1, $2, $3, $4, $5, $6, DEFAULT, DEFAULT, $7, $8)', Array($vRecord['documentid'],
                                                                                                                $vRecord['documenttype'],
                                                                                                                $vRecord['telephone'],
                                                                                                                $vRecord['accountnumber'],
                                                                                                                $vRecord['accounttype'],
                                                                                                                $vRecord['bankid'],
                                                                                                                'CREATION_PROCESS',
                                                                                                                $ccAccountBin));
                if($dbLink->execQry()){
                    $dbLink->setQuery('update update_cache set actiontype = $1 where documentid = $2 and telephone = $3 and accountnumber = $4', Array(BUILDER_PURGE,
                                                                                                                                                        $vRecord['documentid'],
                                                                                                                                                        $vRecord['telephone'],
                                                                                                                                                        $vRecord['accountnumber']));
                    if($dbLink->execQry()){
                        $dbLink->commitTransactions();
                    }
                }
            }catch(Exception $e){
                $dbLink->rollbacTransactions();
            }
        }
    }
    sleep($appconf->structure['updateRecordsTime']);
}
