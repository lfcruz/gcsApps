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
    $formStructure = array ("bank" => "DKT",
                            "msisdn" => $_POST['phone1'],
                            "docType" => "",
                            "document" => ""
                            );
                
        echo '<div id="header">';
        echo '<h2> Renew Activation Code </h2>';
        echo '</div>';
        echo '<form name="form1" method="post" action="">';
        echo 'Por favor introduzca la informacion solicitada:<br/>';
        echo 'Telefono: <input type="text" name="phone1" value=""><br/>';
        echo '<div>';
        echo '<input type="submit" value="submit">';
        echo '</div>';
        echo '</form>';

        if($cashInStructure['phone'] !== null){
            $docInfo = dbora_query($dbpgStructure); //*********** TERMINAR ***************//
            $formStructure['docType'] = $docInfo[1];
            $formStructure['document'] = $docInfo[2];
            $msg = buildMessages(UNBLOK_ACT, $formStructure);
            
            if ($cashInResult == null){
                echo "<br/><p>Resultado Cash-In: 0000 </p><br/>";
            }
            else {
                echo "<br/><p>Resultado Cash-In: $cashInResult</p><br/>";
            }
        }
?>
</body>
</html>
