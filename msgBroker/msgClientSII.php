<?php
include_once "socketServer.php";
include_once "dbClass.php";

$config = json_decode(file_get_contents('config.json'),true);
$dbConnector = new dbRequest($config["dbType"], $config["dbIP"], $config["dbPort"], $config["dbName"], $config["dbUser"], $config["dbPassword"]);
$xmlDOM = new DOMDocument();

do{
    $dbConnector->setQuery("select * from tranlog where id > $1 order by id limit $2", Array($config["lastTrx"], $config["bulkread"]));
    $trxList = $dbConnector->execQry();
    foreach($trxList as $trxReg){
        if ($trxReg["responsecode"] == "0000"){
            switch($trxReg["itc"]){
                case "200.00":
                    $xmlDOM->loadXML($config["msg115"]);
                    $msg = simplexml_import_dom($xmlDOM);
                    $msg["CORRELATIONID"] = date('Ymdhis');
                    $msg->CLIENT["GCSSEQUENCE"] = rand(100000,999999);
                    $msg->TRANSACTION["DATE"] = date('Ymd');
                    $msg->TRANSACTION["TIME"] = date('His');
                    $msg->TRANSACTION["AMOUNT"] = $trxReg["amount"];
                    
                    break;
                case "400.00":
                    $msg = simplexml_import_dom($xmlDOM->loadXML($config["msg417"]));
                    $msg["CORRELATIONID"] = date('Ymdhis');
                    $msg->CLIENT["GCSSEQUENCE"] = rand(100000,999999);
                    $msg->TRANSACTION["DATE"] = date('Ymd');
                    $msg->TRANSACTION["TIME"] = date('His');
                    $msg->TRANSACTION["AMOUNT"] = $trxReg["amount"];
                    break;
                default:
                    $msg=null;
                    break;
            }
            $client = new socketProcessor($config["connectorIP"], $config["connectorPort"], "C");
            $clientResult = $client->sendMessage($msg);
            $reciboFile = json_decode(file_get_contents('./recibos.json'),true);
            array_push($reciboFile,Array("auth" => $trxReg["approvalnumber"], "amount" => $trxReg["amount"], "fechahora" => date('d/m/Y h:i:s a')));
            file_put_contents('./recibos.json', json_encode($reciboFile));
            unset($client);
            $config["lastTrx"] = $trxReg["id"];
            file_put_contents('./config.json', json_encode($config));
            echo "ISO Transaction:".$trxReg["id"]."\n";
            echo $trxReg["itc"]." - ".$trxReg["amount"]." - ".$trxReg["approvalnumber"]." - ".$clientResult->TRANSACTION["RESPONSECODE"]." - ".$clientResult->TRANSACTION["BPSECUENCE"]."\n";
            echo "===================================================================================================\n";
        }
    }
    echo "\n\n\n*********** Sleeping for ".$config["monitortime"]." seconds ********************\n";
    sleep($config["monitortime"]);
}while(true);