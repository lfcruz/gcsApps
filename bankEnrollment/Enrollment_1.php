<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
</head>
<body>
<?php
include_once 'addedFunctions_1.php';
//Definition of constants ------------------------------------------------------
    define ("ENROLL_MSG","860");
    define ("ATTACH_ACCT","800");
//Definition of variables to receive on post -----------------------------------
    $formStructure = array ("bank" => "",
                            "msisdn" => "",
                            "telco" => "",
                            "docType" => "",
                            "documento" => "",
                            "enviroment" => "",
                            "accountType" => "",
                            "alias" => ""
                            );

//Define the mWallet structure -------------------------------------------------
   $mwStructure = array ("idType" => "",
                          "id" => "",
                          "bankId" => "",
                          "telco" => "",
                          "firstName" => "",
                          "middleName" => "",
                          "lastName" => "",
                          "secondLastName" => "",
                          "address1" => "Avenida Santos 1643",
                          "address2" => "San Salvador Centro",
                          "city" => "San Salvador",
                          "state" => "SV",
                          "country" => "SV",
                          "telephone" => "",
                          "gender" => "",
                          "active" => "true",
                          "origin" => array ("id" => "356232",
                                             "name" => "Pulperia Pololo",
                                             "city" => "San Salvador",
                                             "country" => "SV")
                         );
        $dbpgStructure = array ("dbIP" => $_POST['environment'],
                                    "dbPort" => "5432",
                                    "dbName" => "vcash",
                                    "dbUser" => "sa",
                                    "dbPassword" => "password",
                                    "dbQueryName" => "findDocData",
                                    "dbQuery" => "select a.phone,substr(b.realid,1,3)as doctype, substr(b.realid,5,length(b.realid))as docnumber from dakota_phone a, cardholder b where b.id = a.cardholder_id and a.phone = $1",
                                    "dbQueryVariables" => array($_POST['MSISDN']));
                
        $cashInStructure = array ("id" => rand(0,999999),
                                      "operation" => "CASH-IN",
                                      "phone" => $_POST['MSISDN'],
                                      "amount" => "200.00",
                                      "currency" => "DOP",
                                      "reasonCode" => "E",
                                      "options" => array ("" => ""),
                                      "origin" => array ("id" => "18828",
                                                         "name" => "Purperia Pancracio",
                                                         "city" => "San Salvador",
                                                         "country" => "SV"));

   
    $formStructure["bank"] = $_POST['bank'];
    $formStructure["msisdn"] = $_POST['MSISDN'];
    $formStructure["telco"] = $_POST['telco'];
    $formStructure["docType"] = $_POST["tipodoc"];
    $formStructure["document"] = $_POST["documento"];
    $formStructure["enviroment"] = $_POST['environment'];
    $mwStructure["firstName"] = $_POST['fname'];
    $mwStructure["middleName"] = $_POST['mname'];
    $mwStructure["lastName"] = $_POST['lname'];
    $mwStructure["secondLastName"] = $_POST['sname'];
    $mwStructure["gender"] = $_POST['gender'];
    $formStructure["accountType"] = 'PPA';
    $formStructure["alias"] = 'CMAcct';
    

//Define the form components on page load --------------------------------------
    if($bank == null && $status == null){
        echo '<form name="form1" method="post" action="">';
            echo 'Seleccione el Ambiente ';
            echo '<select name="environment">';
                //echo '<option value="172.19.1.19">Desarrollo</option>';
                echo '<option value="172.19.3.39">Cafe</option>';
                echo '<option value="172.19.3.41">Neoris</option>';
                echo '</select></br>';
            echo 'Seleccione el Banco ';
            echo '<select name="bank">';
                echo '<option value="DKT">Cell Money</option>';
                echo '</select></br>';
            echo 'Seleccione el Telco ';
            echo '<select name="telco">';
                echo '<option value="200">Codetel</option>';
                echo '<option value="300">Orange</option>';
                echo '<option value="400">Viva</option>';
                echo '<option value="DGL">Digicel</option>';
                echo '</select></br>';
            echo 'Seleccione el Tipo Documento ';
            echo '<select name="tipodoc">';
                echo '<option value="CEDULA">Cedula</option>';
                echo '<option value="PASAPORTE">Pasaporte</option>';
                echo '</select></br>';
            echo 'Numero de Documento  <input type="text" name="documento" value=""><br/>';
            echo 'Telefono  <input type="text" name="MSISDN" value=""><br/>';
            echo 'Sexo  ';
            echo '<select name="gender">';
                echo '<option value="M">Masculino</option>';
                echo '<option value="F">Femenino</option>';
                echo '</select><br/>';
            echo 'Nombre  <input type="text" name="fname" value=""><br/>';
            echo 'Segundo Nombre <input type="text" name="mname" value=""><br/>';
            echo 'Apellido  <input type="text" name="lname" value=""><br/>';
            echo 'Segundo Apellido <input type="text" name="sname" value=""><br/>';
        echo '<div><input type="submit" value="submit"></div></form>';
    }

// if post is submitted
if($formStructure["bank"] != null && $formStructure["enviroment"] != null && $formStructure["telco"] != null && $formStructure["docType"] != null) {
    echo "<br/>Ambiente :".$formStructure["enviroment"]."<br>";
    //complete message mobileWalle Structure
    $mwStructure["bankId"] = 'BPD'; //$formStructure["bank"];.
    $mwStructure["id"] = $formStructure["document"];
    if ($formStructure['enviroment'] == "172.19.3.41"){
        $cashInStructure['currency'] = "USD";
        if($formStructure["docType"] == "CEDULA"){
            $mwStructure["idType"] = "CSV";
        }
        else{ 
            $mwStructure["idType"] = "PSV";
        }
    }
    else {
        if($formStructure["docType"] == "CEDULA"){
            $mwStructure["idType"] = "CDO";
        }
        else{ 
            $mwStructure["idType"] = "PDO";
        }        
    }
    $mwStructure["telephone"] = $formStructure["msisdn"];
    $mwStructure["telco"] = $formStructure["telco"];
    
    $enrollResult = vcashEnrollment($formStructure["enviroment"],$mwStructure);
    
    if (substr($enrollResult,0,1) == "A") {  // VCash Validation.....
        echo '<br/>Respuesta Vcash: 0000';
        echo '<br/>Enrolamiento Exitoso !!! Codigo de Activacion: '.substr($enrollResult,1,strlen($enrollResult));
        $docInfo = dbpg_query($dbpgStructure);
        $cashInResult = vCashFinantials($formStructure['enviroment'], $cashInStructure, $docInfo[1], $docInfo[2]);
        if ($cashInResult == null){
            echo "<br/><p>Resultado Cash-In: 0000 </p><br/>";
            $cashInStructure['operation'] = "DEBIT";
            $cashInStructure['amount'] = "100.00";
            $cashInStructure['reasonCode'] = "E".$cashInStructure['id'];
            $cashInStructure['id']=rand(0,999999);
            $debitResult = vCashFinantials($formStructure['enviroment'], $cashInStructure, $docInfo[1], $docInfo[2]);
            if ($debitResult == null){
                echo "<br/><p>Resultado Debito Comision: 0000 </p><br/>";
            }
            else{
                echo '<br/><p>Resultado Debito Comision: '.$debitResult['error']['code'].' / '.$debitResult['error']['description'].'</p><br/>';
            }
        }
        else {
            echo '<br/><p>Resultado Cash-In: '.$cashInResult['error']['code'].' / '.$cashInResult['error']['description'].'</p><br/>';
        }
    }
    else {
        echo '<br/>Respuesta Vcash: '.$enrollResult;
        detachPhone($formStructure["enviroment"], $mwStructure["idType"], $formStructure["document"], $formStructure["msisdn"]);
        exit;
    }
}
?>
</body>
</html>