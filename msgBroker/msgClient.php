<?php
include_once "socketServer.php";
$template417 = '<MESSAGE TYPE="417" CORRELATIONID="'.date('Ymdhis').'" COUNTRY="DO" CHANNEL="POS" PARTNERID="791" AGENCYID="792" TERMINALID="276" SHIFTID="285" USERNAME="lcruz"><CLIENT ID="123456" TYPE="" TELCOID="" TELEPHONE="" /><TRANSACTION DATE="'.date('dmy').'" TIME="'.date('his').'" TRANSACTIONTYPE="03" SUBTRANSACTIONTYPE="0356" CURRENCY="DOP" AMOUNT="'.$argv[2].'" GCSSEQUENCE="'.rand(100000,999999).'" /></MESSAGE>';
$template115 = '<MESSAGE TYPE="115" BANKID="234" CORRELATIONID="'.date('Ymdhis').'" COUNTRY="DO" CHANNEL="POS" PARTNERID="791" AGENCYID="792" TERMINALID="276" SHIFTID="285" USERNAME="lcruz"><CLIENT ID="00000000000" TYPE="CEDULA" GCSSEQUENCE="'.rand(100000,999999).'" TELEPHONE="9999999999" /><TRANSACTION DATE="'.date('dmY').'" TIME="'.date('his').'" TRANSACTIONTYPE="16" SUBTRANSACTIONTYPE="1618" ACCOUNT="707547717" TYPE="SAV" CURRENCY="DOP" AMOUNT="'.$argv[2].'" COMMENT="" MERCHANTID="" BENEFICIARY="" CONTRACTNUMBER="" TERMINALID="" MCC="" /></MESSAGE>';
$validXML = new DOMDocument;
$client = new socketProcessor("localhost", 58887, "C");
switch ($argv[1]) {
    case "417":
        echo "Sent XML 417 =============================================================\n";
        $validXML->loadXML($template417);
        $msg = simplexml_import_dom($validXML);
        $rsp = $client->sendMessage($msg);
        var_dump($rsp);
        break;
    case "115":
        echo "Sent XML 115 =============================================================\n";
        $validXML->loadXML($template115);
        $msg = simplexml_import_dom($validXML);
        $rsp = $client->sendMessage($msg);
        var_dump($rsp);
        break;
    default:
        echo "Invalid XML =============================================================\n";
        break;
}
$reciboFile = json_decode(file_get_contents('./recibos.json'),true);
array_push($reciboFile,Array("auth" => $argv[3], "amount" => $argv[2], "fechahora" => date('d/m/Y h:i:s a')));
file_put_contents('./recibos.json', json_encode($reciboFile));
unset($client);
?>
