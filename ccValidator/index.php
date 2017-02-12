<?php
require 'ccValidatorExecutors.php';
global $configStructure;
    loadConfig(true);
    openDB("On");
    switch ($configStructure['appStatus']){
        case "On":
            if (requestValidation($_SERVER["REMOTE_ADDR"], $_SERVER["HTTP_ACCEPT"], $_SERVER["CONTENT_TYPE"],
                      $_SERVER["HTTP_USERID"], $_SERVER["HTTP_AUTHENTICATION"]) === true){
                if (!verifyParameters(json_decode(file_get_contents('php://input'),true))){
                    header("HTTP/1.1 400 Invalid request");
                }
                elseif (verifyCard(json_decode(file_get_contents('php://input'),true))){
                    header("HTTP/1.1 200 Valid Card");
                }
                else {
                    header("HTTP/1.1 404 Invalid Card");
                }
            }
            else{
                header("HTTP/1.1 401 Unauthorized");
            }
            break;
        case "Off":
            if (pg_connection_status($dbConnector) <> PGSQL_CONNECTION_BAD){
                openDB("Off");
            }
            header("HTTP/1.1 503 On Maintenance");
            break;
        default:
            header("HTTP/1.1 504 Out of Service - Report to Administrator");
            break;
    }
    openDB("Off");
?>