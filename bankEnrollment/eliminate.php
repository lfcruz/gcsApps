
<?php
include 'addedFunctions.php';
        $dbpgStructure = array ("dbIP" => "172.19.3.41",
                                    "dbPort" => "5432",
                                    "dbName" => "vcash",
                                    "dbUser" => "sa",
                                    "dbPassword" => "password",
                                    "dbQueryName" => "findDocData",
                                    "dbQuery" => "select a.phone,substr(b.realid,1,3)as doctype, substr(b.realid,5,length(b.realid))as docnumber from dakota_phone a, cardholder b where b.id = a.cardholder_id and a.phone = $1",
                                    "dbQueryVariables" => array($_POST['telefono']));


   if ($_POST['telefono']==''|| $_POST['cedula']=='' || $_POST['banco']==''){

        echo "Error !!! los campos estan vacios ";
   } else {

error_reporting(0);
$telefono=$_POST['telefono'];
$cedula=$_POST['cedula'];
$banco=$_POST['banco'];
$idtype=$_POST['idtype'];

//TOMO LA FECHA Y HORA ACTUAL
$today = date("Ymd");
$now = date("His");
//ABRIR SOCKET PARA ENVIAR MSG
$socket  = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_connect($socket, "172.19.3.41", 8888);
//ARMAR MENSAJE Y PONERLO EN LA VARIABLE $OUT

// pregunto si la identificacion es cedula
if ($idtype === 'C') {
$out = "<MESSAGE TYPE=\"900\" CORRELATIONID=\"20120620235284\" BANKID=\"$banco\"><CLIENT ID=\"$cedula\" TYPE=\"CEDULA\" TELEPHONE=\"$telefono\" BPSEQUENCE=\"286570\" /><TRANSACTION TOKEN=\"000000000000000000000000\" MOTIVE=\"01\" DATE=\"$today\" TIME=\"$now\" /></MESSAGE>".chr(10);
} 
else {
$out = "<MESSAGE TYPE=\"900\" CORRELATIONID=\"20120620235284\" BANKID=\"$banco\"><CLIENT ID=\"$cedula\" TYPE=\"PASAPORTE\" TELEPHONE=\"$telefono\" BPSEQUENCE=\"286570\" /><TRANSACTION TOKEN=\"000000000000000000000000\" MOTIVE=\"01\" DATE=\"$today\" TIME=\"$now\" /></MESSAGE>".chr(10);

}

//ENVIAR MSG AL CORE
socket_send($socket, $out, strLen($out), 0);

//HACER BUCLE HASTA QUE LA RESPUESTA DEL CORE LLEGUE Y ALMACENAR LA RESPUESTA EN VARIABLE $REPLY
$reply = "";

do {
     $recv = "";
     $recv = socket_read($socket, 1024);
     if($recv != "") {
         $reply .= $recv;
     }
} while($recv != "");

//LUEGO QUE TENGO LA RESPUESTA DEL CORE, SE PARSEA PARA VER EL RESPONSECODE
$dom = new DOMDocument;
$dom->loadXML($reply);
$tmp = simplexml_import_dom($dom);

$responsecode= $tmp->TRANSACTION["RESPONSECODE"];
//echo "EL RESPONSECODE FUE ....". $responsecode;
if ($responsecode=='0') {
    echo "<b>La Desafiliacion ha sido exitosa!!!!</b>";
    $docInfo = dbpg_query($dbpgStructure);
    $detachResult = detachPhone('172.19.3.41', $docInfo[1], $docInfo[2], $docInfo[0]);
header('Location: unEnrollment.php');
}else {
echo "se ha producido un error en el proceso. El Error fue..".$responsecode;
}


}
?>