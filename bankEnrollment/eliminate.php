
<?php

   if ($_POST['telefono']==''|| $_POST['cedula']=='' || $_POST['banco']==''){

        echo "Error !!! los campos estan vacios ";
   } else {

error_reporting(0);
$telefono=$_POST['telefono'];
$cedula=$_POST['cedula'];
$banco=$_POST['banco'];

//TOMO LA FECHA Y HORA ACTUAL
$today = date("Ymd");
$now = date("His");
//ABRIR SOCKET PARA ENVIAR MSG
$socket  = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_connect($socket, "172.19.3.23", 8887);
//ARMAR MENSAJE Y PONERLO EN LA VARIABLE $OUT

// pregunto si la identificacion es cedula
if (strlen($cedula)>10) {
$out = "<MESSAGE TYPE=\"900\" CORRELATIONID=\"20120620235284\" BANKID=\"$banco\"><CLIENT ID=\"$cedula\" TYPE=\"CEDULA\" TELEPHONE=\"$telefono\" BPSEQUENCE=\"286570\" /><TRANSACTION TOKEN=\"000000000000000000000000\" MOTIVE=\"01\" DATE=\"$today\" TIME=\"$now\" /></MESSAGE>".chr(10);
} else {
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
header('Location: index2.php');
}else {
echo "se ha producido un error en el proceso. El Error fue..".$responsecode;
}


}
?>