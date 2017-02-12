<?php
include_once "socketServer.php";
$server = new socketProcessor("localhost", 58888, "S");
$template400 = '<MESSAGE TYPE="400" CORRELATIONID="" BANKID="102"><CLIENT ID="201410090" TYPE="RNC" GCSSEQUENCE="" TELEPHONE="9999999999" TOKEN="000000000000000000000000" PINVERIFICATION="0" PINCAPTUREFLAG="0" /><TRANSACTION ACCOUNT="782779805" TYPE="DDA" DATE="" TIME="" MERCHANTID="9999999" BENEFICIARY="" TRANSACTIONTYPE="03" SUBTRANSACTIONTYPE="0356" CURRENCY="DOP" AMOUNT="0.00" CONTRACTNUMBER="0" TERMINALID="0000000" COMMENT=""/></MESSAGE>';
$template111 = '<MESSAGE TYPE="111" CORRELATIONID="" BANKID="102"><CLIENT ID="201410090" TYPE="RNC" GCSSEQUENCE="" TELEPHONE="9999999999" TOKEN="000000000000000000000000" PINVERIFICATION="0" PINCAPTUREFLAG="0" /><TRANSACTION ACCOUNT="782779805" TYPE="DDA" DATE="" TIME="" MERCHANTID="9999999" BENEFICIARY="" TRANSACTIONTYPE="03" SUBTRANSACTIONTYPE="0356" CURRENCY="DOP" AMOUNT="0.00" CONTRACTNUMBER="0" TERMINALID="0000000" COMMENT=""/></MESSAGE>';
$msgError = '<MESSAGE TYPE="ERR" ><TRANSACTION RESPONSECODE="9999" DESCRIPTION="Invalid Message" /></MESSAGE>';
$validXML = new DOMDocument;
$recordF640 = Array();
$totalsF640 = Array("transaction-code" => "99","total-transactions" => 0, "total-amount" => 0.00);
$stringF640 = "";
while (true) {
    $client = new socketProcessor("localhost", 58887, "C");
    if(file_exists('./cut.now')){
        $totalsF640["transaction-code"] = "99";
        $totalsF640["total-transactions"] = str_pad($totalsF640["total-transactions"], 8, "0",STR_PAD_LEFT);
        $totalsF640["total-amount"] = str_pad(number_format($totalsF640["total-amount"], 2), 17, "0",STR_PAD_LEFT);
        $totalsF640["total-interchange-amount"] = "00000000000.00";
        $totalsF640["total-discount-amount"] = "00000000000.00";
        $totalsF640["cardnet-proc-date"] = date('d/m/y');
        foreach ($totalsF640 as $value) {
            $stringF640 .= $value;
        }
        file_put_contents('./retirosF640.txt', $stringF640, FILE_APPEND);
        $totalsF640 = Array("transaction-code" => "99","total-transactions" => 0, "total-amount" => 0.00);
        $stringF640 = "";
        rename('./retirosF640.txt', "retirosF640_".date('Ymdhis').".txt");
        unlink('./cut.now');
    }
    $vIncomingMessage = $server->receiveMessage();
    switch ($vIncomingMessage["TYPE"]) {
        case "417":
            $recordF640["transaction-code"] = "40";
            $recordF640["account-number"] = str_pad($vIncomingMessage->CLIENT["ID"],16,"*",STR_PAD_LEFT);
            $recordF640["amount"] = str_pad(number_format($vIncomingMessage->TRANSACTION["AMOUNT"]+0.01-0.01, 2),12,"0",STR_PAD_LEFT);
            $recordF640["currency"] = "214";
            $recordF640["transaction-date"] = substr($vIncomingMessage->TRANSACTION["DATE"],1,2)."/".substr($vIncomingMessage->TRANSACTION["DATE"],3,2)."/".substr($vIncomingMessage->TRANSACTION["DATE"],5,2);
            $recordF640["merchant-number"] = str_pad($vIncomingMessage->TRANSACTION["PARTNERID"],12,"0",STR_PAD_LEFT);
            $recordF640["merchant-name"] = str_pad($vIncomingMessage->TRANSACTION["AGENCYID"],25,"0",STR_PAD_LEFT);
            $recordF640["batch-number"] = "000";
            $recordF640["authorization-code"] = str_pad($vIncomingMessage->TRANSACTION["GCSSEQUENCE"],6,"0",STR_PAD_LEFT);
            $recordF640["merchant-cat-code"] = "0000";
            $recordF640["transaction-source"] = "0000";
            $recordF640["percent-discount"] = "00.00";
            $recordF640["interchange-amount"] = "000000000.00";
            $recordF640["discount-rate"] = "00.00";
            $recordF640["discount-amount"] = "0000000.00";
            $recordF640["reference-number"] = "00000000000000000000000";
            $recordF640["card-acceptor-id"] = "000000000000000";
            $recordF640["filler"] = "C000GCSM***************************";
            foreach ($recordF640 as $value) {
            $stringF640 .= $value;
            }
            file_put_contents('./retirosF640.txt', $stringF640."\n", FILE_APPEND);
            $totalsF640["total-transactions"] += 1;
            $totalsF640["total-amount"] += $vIncomingMessage->TRANSACTION["AMOUNT"];
            $stringF640 = "";
            $validXML->loadXML($template400);
            $msg400 = simplexml_import_dom($validXML);
            $msg400["CORRELATIONID"] = date('YmdHis');
            $msg400->CLIENT["GCSSEQUENCE"] = rand(10000,999999);
            $msg400->TRANSACTION["AMOUNT"] = $vIncomingMessage->TRANSACTION["AMOUNT"];
            $msg400->TRANSACTION["DATE"] = date('dmY');
            $msg400->TRANSACTION["TIME"] = date('his');
            $vOutgoingMessage = $client->sendMessage($msg400);
            $vIncomingMessage["TYPE"] = "116";
            $vIncomingMessage->TRANSACTION["BPSEQUENCE"] = $vOutgoingMessage->TRANSACTION["BPSEQUENCE"];
            $vIncomingMessage->TRANSACTION["RESPONSECODE"] = $vOutgoingMessage->TRANSACTION["RESPONSECODE"];
            $recordF640["transaction-code"] = "41";
            $recordF640["account-number"] = str_pad($msg400->CLIENT["ID"],16,"*",STR_PAD_LEFT);
            $recordF640["amount"] = str_pad(number_format($vIncomingMessage->TRANSACTION["AMOUNT"]+0.01-0.01, 2),12,"0",STR_PAD_LEFT);
            $recordF640["currency"] = "214";
            $recordF640["transaction-date"] = substr($vIncomingMessage->TRANSACTION["DATE"],1,2)."/".substr($vIncomingMessage->TRANSACTION["DATE"],3,2)."/".substr($vIncomingMessage->TRANSACTION["DATE"],5,2);;
            $recordF640["merchant-number"] = str_pad($vIncomingMessage->TRANSACTION["PARTNERID"],12,"0",STR_PAD_LEFT);
            $recordF640["merchant-name"] = str_pad($vIncomingMessage->TRANSACTION["AGENCYID"],25,"0",STR_PAD_LEFT);
            $recordF640["batch-number"] = "000";
            $recordF640["authorization-code"] = str_pad($vIncomingMessage->TRANSACTION["BPSEQUENCE"],6,"0",STR_PAD_LEFT);
            $recordF640["merchant-cat-code"] = "0000";
            $recordF640["transaction-source"] = "0000";
            $recordF640["percent-discount"] = "00.00";
            $recordF640["interchange-amount"] = "000000000.00";
            $recordF640["discount-rate"] = "00.00";
            $recordF640["discount-amount"] = "0000000.00";
            $recordF640["reference-number"] = "00000000000000000000000";
            $recordF640["card-acceptor-id"] = "000000000000000";
            $recordF640["filler"] = "C000GCSM***************************";
            break;
        case "115":
            $recordF640["transaction-code"] = "41";
            $recordF640["account-number"] = str_pad($vIncomingMessage->CLIENT["ID"],16,"*",STR_PAD_LEFT);
            $recordF640["amount"] = str_pad(number_format($vIncomingMessage->TRANSACTION["AMOUNT"]+0.01-0.01, 2),12,"0",STR_PAD_LEFT);
            $recordF640["currency"] = "214";
            $recordF640["transaction-date"] = substr($vIncomingMessage->TRANSACTION["DATE"],1,2)."/".substr($vIncomingMessage->TRANSACTION["DATE"],3,2)."/".substr($vIncomingMessage->TRANSACTION["DATE"],5,2);;
            $recordF640["merchant-number"] = str_pad($vIncomingMessage->TRANSACTION["PARTNERID"],12,"0",STR_PAD_LEFT);
            $recordF640["merchant-name"] = str_pad($vIncomingMessage->TRANSACTION["AGENCYID"],25,"0",STR_PAD_LEFT);
            $recordF640["batch-number"] = "000";
            $recordF640["authorization-code"] = str_pad($vIncomingMessage->TRANSACTION["GCSSEQUENCE"],6,"0",STR_PAD_LEFT);
            $recordF640["merchant-cat-code"] = "0000";
            $recordF640["transaction-source"] = "0000";
            $recordF640["percent-discount"] = "00.00";
            $recordF640["interchange-amount"] = "000000000.00";
            $recordF640["discount-rate"] = "00.00";
            $recordF640["discount-amount"] = "0000000.00";
            $recordF640["reference-number"] = "00000000000000000000000";
            $recordF640["card-acceptor-id"] = "000000000000000";
            $recordF640["filler"] = "C000GCSM***************************";
            foreach ($recordF640 as $value) {
            $stringF640 .= $value;
            }
            file_put_contents('./retirosF640.txt', $stringF640."\n", FILE_APPEND);
            $totalsF640["total-transactions"] += 1;
            $totalsF640["total-amount"] += $vIncomingMessage->TRANSACTION["AMOUNT"];
            $stringF640 = "";
            $validXML->loadXML($template111);
            $msg111 = simplexml_import_dom($validXML);
            $msg111["CORRELATIONID"] = date('YmdHis');
            $msg111->TRANSACTION["DATE"] = date('dmY');
            $msg111->TRANSACTION["TIME"] = date('his');
            $msg111->TRANSACTION["GCSSEQUENCE"] = rand(100000, 999999);
            $msg111->TRANSACTION['AMOUNT'] = $vIncomingMessage->TRANSACTION["AMOUNT"];
            $vOutgoingMessage = $client->sendMessage($msg111);
            $vIncomingMessage["TYPE"] = "416";
            $vIncomingMessage->TRANSACTION["GCSSEQUENCE"] = rand(10000000, 99999999);
            $vIncomingMessage->TRANSACTION["BPSEQUENCE"] = $vOutgoingMessage->TRANSACTION["BPSEQUENCE"];
            $vIncomingMessage->TRANSACTION["RESPONSECODE"] = $vOutgoingMessage->TRANSACTION["RESPONSECODE"];
            $recordF640["transaction-code"] = "40";
            $recordF640["account-number"] = str_pad($msg111->CLIENT["ID"],16,"*",STR_PAD_LEFT);
            $recordF640["amount"] = str_pad(number_format($vIncomingMessage->TRANSACTION["AMOUNT"]+0.01-0.01, 2),12,"0",STR_PAD_LEFT);
            $recordF640["currency"] = "214";
            $recordF640["transaction-date"] = substr($vIncomingMessage->TRANSACTION["DATE"],1,2)."/".substr($vIncomingMessage->TRANSACTION["DATE"],3,2)."/".substr($vIncomingMessage->TRANSACTION["DATE"],5,2);;
            $recordF640["merchant-number"] = str_pad($vIncomingMessage->TRANSACTION["PARTNERID"],12,"0",STR_PAD_LEFT);
            $recordF640["merchant-name"] = str_pad($vIncomingMessage->TRANSACTION["AGENCYID"],25,"0",STR_PAD_LEFT);
            $recordF640["batch-number"] = "000";
            $recordF640["authorization-code"] = str_pad($vIncomingMessage->TRANSACTION["BPSEQUENCE"],6,"0",STR_PAD_LEFT);
            $recordF640["merchant-cat-code"] = "0000";
            $recordF640["transaction-source"] = "0000";
            $recordF640["percent-discount"] = "00.00";
            $recordF640["interchange-amount"] = "000000000.00";
            $recordF640["discount-rate"] = "00.00";
            $recordF640["discount-amount"] = "0000000.00";
            $recordF640["reference-number"] = "00000000000000000000000";
            $recordF640["card-acceptor-id"] = "000000000000000";
            $recordF640["filler"] = "C000GCSM***************************";
            break;
        default:
            $validXML->loadXML($msgError);
            $vIncomingMessage = simplexml_import_dom($validXML);
            break;
    }
    
    $validRecord = ($vIncomingMessage->TRANSACTION["RESPONSECODE"] == "0000") ? true : false;
    $server->returnMessage($vIncomingMessage);
    if($validRecord){
        foreach ($recordF640 as $value) {
            $stringF640 .= $value;
        }
        file_put_contents('./retirosF640.txt', $stringF640."\n", FILE_APPEND);
        $totalsF640["total-transactions"] += 1;
        $totalsF640["total-amount"] += $vIncomingMessage->TRANSACTION["AMOUNT"];
    }
    $msg400 = "";
    $msg111 = "";
    $vOutgoingMessage = "";
    $recordF640 = Array();
    $stringF640 = "";
    unset($client);
}
?>