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
    $formStructure = array ("bank" => "DKT",
                            "msisdn" => $_POST['phone1'],
                            "docType" => "",
                            "document" => ""
                            );
    $dbConnectorStructure = array ("dbIP" => "",
                            "dbPort" => "1521",
                            "dbName" => "gcsdev2",
                            "dbUser" => "gcsdevusr",
                            "dbPassword" => "gcsdevusr",
                            "dbQuery" => 'select a.phone,substr(b.realid,1,3)as doctype, substr(b.realid,5,length(b.realid))as docnumber from dakota_phone a, cardholder b where b.id = a.cardholder_id and a.phone = '.$_POST['phone1']);
    
    $enviroment = $_POST['enviroment1'];

                
        echo '<div id="header">';
        echo '<h2> Renew Activation Code </h2>';
        echo '</div>';
        echo '<form name="form1" method="post" action="">';
        echo 'Por favor introduzca la informacion solicitada:<br/>';
        echo '<select name="environment1">';
        echo '<option value="172.19.3.39">Cafe</option>';
        echo '<option value="172.19.3.41">Neoris</option>';
        echo '</select></br>';
        echo 'Telefono: <input type="text" name="phone1" value=""><br/>';
        echo '<div>';
        echo '<input type="submit" value="submit">';
        echo '</div>';
        echo '</form>';

        if($cashInStructure['phone'] !== null){
            switch ($enviroment){
                case '172.19.3.39':
                    $dbConnectorStructure['dbIP'] = '172.19.3.27';
                    $dbConnectorStructure['dbName'] = 'gcstest';
                    $dbConnectorStructure['dbUser'] = 'oramdev2';
                    $dbConnectorStructure['dbPassword'] = 'oramdev22013';
                    break;
                case '172.19.3.41':
                    $dbConnectorStructure['dbIP'] = '172.19.3.44';
                    break;
            }
            $docInfo = dbora_query($dbConnectorStructure);
            $formStructure['docType'] = $docInfo[1];
            $formStructure['document'] = $docInfo[2];
            $msg = buildMessages(UNBLOK_ACT, $formStructure);
            $rsp = sentToSocket($enviroment, CORE_PORT, $msg);
            if ($rsp->TRANSACTION["RESPONSECODE"] === "0000"){
                echo "<br/><p>Codigo de activacion renovado: ".str_pad(rand(0,9999), 4, "0", STR_PAD_LEFT)." </p><br/>";
            }
            else {
                echo "<br/><p>Error en renovacion de codigo: ".$rsp->TRANSACTION["RESPONSECODE"]."</p><br/>";
            }
        }
?>
</body>
</html>
