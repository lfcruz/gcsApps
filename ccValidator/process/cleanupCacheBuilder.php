<?php
include_once 'dbClass.php';
include_once 'configClass.php';
include_once 'constants.php';

// Main procedure --------------------------------------------------------------
$appconf = new configLoader('../config/ccVConf.json');
$dbconf = new configLoader('../config/db.json');
$dbLink = new dbRequest($conf->structure['dbtype'],
                                           $dbconf->structure['dbhost'],
                                           $dbconf->structure['dbport'],
                                           $dbconf->structure['dbname'],
                                           $dbconf->structure['dbuser'],
                                           $dbconf->structure['dbpass']);

while ($appconf->structure['appStatus']){
    $dbLink->setQuery('delete from update_cache where actiontype = $1 limit $2', Array(BUILDER_PURGE, $appconf->structure['deleteRecordsLimit']));
    $result = $dbLink->execQry();
    sleep((int) $appconf->structure['deleteRecordsTime']);
    $appconf->reload();
}
?>
