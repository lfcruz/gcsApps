<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title></title>
</head>
<body>
<?php
    include 'addedFunctions.php';
    define ("UNBLOK_ACT","950");
    define ("UNBLOK_PIN","940");
    define ("CORE_PORT","8888");
    define ("VCASH_PORT","6080");
    $formStructure = array ("bank" => $_POST['bank1'],
                            "msisdn" => $_POST['phone1'],
                            "docType" => "",
                            "document" => ""
                            );
    $dbConnectorStructure = array ("dbIP" => "",
                            "dbPort" => "1521",
                            "dbName" => "gcsdev2",
                            "dbUser" => "gcsdevusr",
                            "dbPassword" => "gcsdevusr",
                            "dbQuery" => "select a.MSISDN,b.ID_TYPE,b.ID from r_gcscustomer_msisdn_mp a, r_gcscustomer_account_m b where b.GCS_ACCOUNT_ID=a.GCS_ACCOUNT_ID and a.STATUS = 'AB' and a.MSISDN = '".$_POST['phone1']."'");

    $enviroment = $_POST['enviroment1'];


        echo '<div id="header">';
        echo '<h2> Desbloqueo de PIN </h2>';
        echo '</div>';
        echo '<form name="form1" method="post" action="">';
        echo 'Por favor introduzca la informacion solicitada:<br/>';
        echo 'Seleccione el Ambiente: ';
        echo '<select name="enviroment1">';
        echo '<option value="172.19.3.39">Cafe</option>';
        echo '<option value="172.19.3.41">Neoris</option>';
        echo '<option value="172.19.3.23">tPago Dev</option>';
        echo '<option value="172.19.3.12">tPago Stag</option>';
        echo '</select></br>';
        echo 'Seleccione el banco: ';
        echo '<select name="bank1">';
        echo '<option value="102">Banco Popular</option>';
        echo '<option value="BDP">Banco del Progreso</option>';
        echo '<option value="SCT">Banco Nueva Scotia</option>';
        echo '<option value="BDR">Banco de Reservas</option>';
        echo '<option value="ADO">Banco Adopem</option>';
        echo '<option value="BLH">Banco Lopez de Haro</option>';
        echo '<option value="ADM">Banco Ademi</option>';
        echo '<option value="CTB">Citi Bank</option>';
        echo '<option value="BDI">Banco BDI</option>';
        echo '<option value="ALV">Banco Alaver</option>';
        echo '<option value="DKT">Banco Tarjeta Virtual</option>';
        echo '<option value="UNB">Banco Union</option>';
        echo '</select></br>';
        echo 'Telefono: <input type="text" name="phone1" value=""><br/>';
        echo '<div>';
        echo '<input type="submit" value="submit">';
        echo '</div>';
        echo '</form>';

        if($formStructure['msisdn'] !== null){
            switch ($enviroment){
                case "172.19.3.39":
                    $dbConnectorStructure['dbIP'] = '172.19.3.27';
                    $dbConnectorStructure['dbName'] = 'gcstest';
                    $dbConnectorStructure['dbUser'] = 'oramdev2';
                    $dbConnectorStructure['dbPassword'] = 'oramdev22013';
                    break;
                case "172.19.3.23":
                    $dbConnectorStructure['dbIP'] = '172.19.3.27';
                    $dbConnectorStructure['dbName'] = 'gcsdev';
                    $dbConnectorStructure['dbUser'] = 'gcsdev';
                    $dbConnectorStructure['dbPassword'] = 'gcs2013';
                    break;
                case "172.19.3.12":
                    $dbConnectorStructure['dbIP'] = '172.19.3.26';
                    $dbConnectorStructure['dbName'] = 'gcstest';
                    $dbConnectorStructure['dbUser'] = 'gcs501';
                    $dbConnectorStructure['dbPassword'] = 'gcs';
                    break;
                case "172.19.3.41":
                    $dbConnectorStructure['dbIP'] = '172.19.3.44';
                    break;
            }
            $docInfo = dbora_query($dbConnectorStructure);
            if ($docInfo[1] === "C"){
                $formStructure['docType'] = "CEDULA";
            }
            else {
                $formStructure['docType'] = "PASAPORTE";
            }
            $formStructure['document'] = $docInfo[2];
            $msg = buildMessages(UNBLOK_PIN, $formStructure);
            $rsp = sentToSocket($enviroment, CORE_PORT, $msg);
            if ($rsp->TRANSACTION["RESPONSECODE"] == "0000"){
                echo "<br/><p>Codigo PIN desbloqueado: ".str_pad(rand(0,9999), 4, "0", STR_PAD_LEFT)." </p><br/>";
            }
            else {
                echo "<br/><p>Error en desbloqueo de PIN: ".$rsp->TRANSACTION["RESPONSECODE"]."</p><br/>";
            }
        }
?>
</body>
</html>