<?php
include 'ccValidatorExecutors.php';
loadConfig("On");
openDB(true);
while($configStructure["appStatus"] === 'On'){
    sleep(300);
    loadConfig(false);
    if (pg_connection_status($dbConnector) === PGSQL_CONNECTION_BAD){
        writeLog(MSG_ERROR,REF_CONNECTIONS,"Database connection lost, reconnecting.......", APP_LOG);
        openDB("On");
    }
}
openDB("Off");
?>
