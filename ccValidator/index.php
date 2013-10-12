<?php
require_once 'ccValidatorExecutors.php';
    if (requestValidation($_SERVER["REMOTE_ADDR"], $_SERVER["HTTP_ACCEPT"], $_SERVER["CONTENT_TYPE"],
                      $_SERVER["HTTP_USERID"], $_SERVER["HTTP_AUTHENTICATION"])){
        echo ("Request Validated!!!!!!!!");
    }
    else{
        echo ("Invalid Request!!!!!!!!!");
    }
?>