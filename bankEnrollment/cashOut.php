<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title></title>
</head>
<body>
<?php
        include 'addedFunctions.php';
        $dbpgStructure = array ("dbIP" => $_POST['enviroment1'],
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
                                      "currency" => "DOP",
                                      "reasonCode" => "11404",
                                      "options" => array ("code" => $_POST['code1']),
                                      "origin" => array ("id" => "18828",
                                                         "name" => "Purperia Pancracio",
                                                         "city" => "San Salvador",
                                                         "country" => "SV"));
        $enviroment = $_POST['enviroment1'];
                    
        echo '<div id="header">';
        echo '<h2> Cash Out </h2>';
        echo '</div>';
        echo '<form name="form1" method="post" action="">';
        echo 'Por favor introduzca la informacion solicitada:<br/>';
        echo '<select name="enviroment1">';
        echo '<option value="172.19.3.39">Cafe</option>';
        echo '<option value="172.19.3.41">Neoris</option>';
        echo '</select></br>';
        echo 'Telefono: <input type="text" name="phone1" value=""><br/>';
        echo 'Code: <input type="text" name="code1" value=""><br/>';
        echo 'Monto   : <input type="text" name="amount1" value=""><br/>';
        echo '<div>';
        echo '<input type="submit" value="submit">';
        echo '</div>';
        echo '</form>';

        if($cashOutStructure['phone'] !== null and $cashOutStructure['amount'] !== null){
            if ($_POST['enviroment1'] == '172.19.3.41'){
                $cashOutStructure['currency'] = "USD";
            }
            $docInfo = dbpg_query($dbpgStructure);
            $cashOutResult = vCashFinantials($enviroment, $cashOutStructure, $docInfo[1], $docInfo[2]);    
            if ($cashOutResult == null){
                echo "<br/><p>Resultado Cash-Out: 0000 </p><br/>";
                $cashOutStructure['operation'] = "DEBIT";
                $cashOutStructure['amount'] = "4.00";
                $cashOutStructure['reasonCode'] = "A".$cashOutStructure['id'];
                $cashOutStructure['id']=rand(0,999999);
                $debitResult = vCashFinantials($enviroment, $cashOutStructure, $docInfo[1], $docInfo[2]);
                if ($debitResult == null){
                    echo "<br/><p>Resultado Debito Comision: 0000 </p><br/>";
                }
                else{
                    echo '<br/><p>Resultado Debito Comision: '.$debitResult['error']['code'].' / '.$debitResult['error']['description'].'</p><br/>';
                }
            }
            else {
                echo '<br/><p>Resultado Cash-Out: '.$cashOutResult['error']['code'].' / '.$cashOutResult['error']['description'].'</p><br/>';
            }
        }
?>
</body>
</html>
