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
                            "dbQuery" => "select a.MSISDN,b.ID_TYPE,b.ID,d.PARTNER_ID from pre_gcscustomer_enrollment_m a, r_gcscustomer_account_m b, r_gcscustomer_msisdn_mp c, partner_m d where b.GCS_ACCOUNT_ID=a.GCS_ACCOUNT_ID and c.GCS_ACCOUNT_ID=a.GCS_ACCOUNT_ID and d.PARTNER_CODE=a.PARTNER_CODE and ((a.STATUS in ('PAB') and c.STATUS in ('A','PAB','AB')) or (a.STATUS in ('A') and c.STATUS in ('AB'))) and a.MSISDN = '".$_POST['phone1']."'");
    
    $enviroment = $_POST['enviroment1'];
    $activationCode = "";


        echo '<div id="header">';
        echo '<h2> Renew Activation Code </h2>';
        echo '</div>';
        echo '<form name="form1" method="post" action="">';
        echo 'Por favor introduzca la informacion solicitada:<br/>';
        echo 'Seleccione el ambiente: ';
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
                    $dbConnectorStructure['dbName'] = 'gcstest';
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
            if ($docInfo['1'] === "C"){
                $formStructure['docType'] = "CEDULA";
                $docT='CSV';
            }
            else {
                $formStructure['docType'] = "PASAPORTE";
                $docT='PSV';
            }
            $formStructure['document'] = $docInfo[2];
            $formStructure['bank'] = $docInfo[3];
            $activationCode = setActivationCode($enviroment, $docT, $formStructure['document'], $formStructure['msisdn']);
            if(!array_key_exists('error',$activationCode)){
                $msg = buildMessages(UNBLOK_ACT, $formStructure);
                $rsp = sentToSocket($enviroment, CORE_PORT, $msg);
                if ($rsp->TRANSACTION["RESPONSECODE"] == "0000"){
                    switch ($enviroment){
                        case '172.19.3.41':
                            echo "<br/><p>Codigo de activacion renovado: ".$activationCode['activation-code']." </p><br/>";
                            break;
                        default:
                            echo "<br/><p>Codigo de activacion renovado: ".str_pad(rand(0,9999), 4, "0", STR_PAD_LEFT)." </p><br/>";
                            break;
                    }
                }
                else {
                    echo "<br/><p>Error en renovacion de codigo: ".$rsp->TRANSACTION["RESPONSECODE"]."</p><br/>";
                }
            }
            else {
                    echo "<br/><p>Error en renovacion de codigo: ".$activationCode['error']."</p><br/>";
            }
        }
?>
</body>
</html>