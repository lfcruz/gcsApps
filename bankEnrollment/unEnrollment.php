<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Desafiliar numeros en Mannindia Stagging</title>
<style type="text/css">
h1,table,form {text-align:center}
</style>
    </head>
    <body>

<?php
error_reporting(0);
// PARAMETROS BASE DE DATOS
if(isset($_POST['submit'])) {
$telefono=$_POST['telefono'];

$user='gcsdev';
$pass='gcs2013';
$db = '(DESCRIPTION = (CONNECT_TIMEOUT=5) (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 172.19.3.27)(PORT = 1521)))(CONNECT_DATA=(SID=gcstest)))';

$c=@OCILogon($user, $pass, $db);
//$s = oci_parse($c, "select * from v_enrollment WHERE MSISDN='$telefono' and status='A'");
$s = oci_parse($c, "select a.msisdn,c.partner_id,b.id,a.status cel_status,b.status ced_status from pre_gcscustomer_enrollment_m a,
r_gcscustomer_account_m b, partner_m c where a.gcs_account_id=b.gcs_account_id and a.partner_code=c.partner_code and a.status in ('A','PA','PAB') and b.status in ('A','NA') and msisdn='$telefono'");
oci_execute($s);
//while (($row = oci_fetch_array($s, OCI_ASSOC))) {
  //    echo "BANCO....".$row['PARTNER_CODE']. "\n";
   //   echo "CEDULA ...".$row['ID']. "\n";
    // $banco = $row['PARTNER_CODE'];
//}
}
?>


<h1>Buscar numero para desafiliarlo en mannindia...Desarrollo </h1>
 <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
   <input type="text" name="telefono"><br>
   <input type="submit" name="submit" value="Primero buscar el numero..."><br>
</form>
<br>
<table border="1" align="center">
        <tr><th>Telefono</th><th>Cedula</th><th>Banco</th><th>Status Telefono</th><th>Status Cedula</th></tr>


<? while (($row = oci_fetch_array($s, OCI_ASSOC))) {  ?>

 <tr><td><?php echo $row['MSISDN'];?></td>
 <td><?echo $row['ID']?></td>
 <td><?echo $row['PARTNER_ID']?></td>
 <td><?echo $row['CEL_STATUS']?></td>
 <td><?echo $row['CED_STATUS']?></td>
<td> <form action="borrar2.php" method="post"> <input type="hidden" name="cedula" value="<?php echo $row['ID']; ?>" />
<input type="hidden" name="banco" value="<?php echo $row['PARTNER_ID']; ?>" />
<input type="hidden" name="telefono" value="<?php echo $row['MSISDN']; ?>" />

        <input type="submit" value="..y luego borrarlo" />
      </form>
    </td>
  </tr>


<?}


?>
</table>
    </body>
</html>