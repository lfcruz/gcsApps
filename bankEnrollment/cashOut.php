<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title></title>
</head>
<body>
<?php
        include 'addedFunctions.php';
        $dbpgStructure = array ("dbIP" => "172.19.1.236",
                                    "dbPort" => "5432",
                                    "dbName" => "vcash",
                                    "dbUser" => "sa",
                                    "dbPassword" => "password",
                                    "dbQueryName" => "findDocData",
                                    "dbQuery" => "select a.phone,substr(b.realid,1,3)as doctype, substr(b.realid,5,length(b.realid))as docnumber from dakota_phone a, cardholder b where b.id = a.cardholder_id and a.phone = $1",
                                    "dbQueryVariables" => array($_POST['phone1']));
                
            $cashOutStructure = array ("id" => rand(0,999999),
                                      "operation" => "CASH-OUT",
                                      "phone" => $_POST['phone1'],
                                      "amount" => $_POST['amount1'],
                                      "currency" => "USD",
                                      "reasonCode" => "11404",
                                      "options" => array ("code" => $_POST['code1']),
                                      "origin" => array ("id" => "18828",
                                                         "name" => "Purperia Pancracio",
                                                         "city" => "San Salvador",
                                                         "country" => "SV"));
                    
        echo '<div id="header">';
        echo '<h2> Cash Out </h2>';
        echo '</div>';
        echo '<form name="form1" method="post" action="">';
        echo 'Por favor introduzca la informacion solicitada:<br/>';
        echo 'Telefono: <input type="text" name="phone1" value=""><br/>';
        echo 'Code: <input type="text" name="code1" value=""><br/>';
        echo 'Monto   : <input type="text" name="amount1" value=""><br/>';
        echo '<div>';
        echo '<input type="submit" value="submit">';
        echo '</div>';
        echo '</form>';

        if($cashOutStructure['phone'] !== null and $cashOutStructure['amount'] !== null){
            $docInfo = dbpg_query($dbpgStructure);
            $cashOutResult = vCashFinantials('172.19.1.243', $cashOutStructure, $docInfo[1], $docInfo[2]);    
            if ($cashOutResult == null){
                echo "<br/><p>Resultado Cash-In: 0000 </p><br/>";
            }
            else {
                echo "<br/><p>Resultado Cash-In: $cashOutResult</p><br/>";
            }
        }
?>
</body>
</html>