<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
</head>
<body>
<?php
include_once 'addedFunctions.php';
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
    if($formStructure["docType"] == "CEDULA"){
        $mwStructure["idType"] = "CSV";
    }
    else{ 
        $mwStructure["idType"] = "PSV";
    }
    $mwStructure["telephone"] = $formStructure["msisdn"];
    
    $tmp = buildMessages(ENROLL_MSG,$formStructure);
                
    $enrollResult = vcashEnrollment($formStructure["enviroment"],$mwStructure);
    
    if ($enrollResult == "0000") {  // VCash Validation.....
        echo '<br/>Respuesta Vcash: '.$enrollResult;
        
        $enrollResult = tpagoEnrollment($formStructure["enviroment"],$tmp);
        
        if ($enrollResult == "0000") {   // MSG 860 Validation......
            echo '<br/>Respuesta 860: '.$enrollResult;
            
            $tmp = buildMessages(ATTACH_ACCT,$formStructure);
            
            $enrollResult = accountsAttach($formStructure["enviroment"],$tmp);
            
            if($enrollResult == "0000"){
                echo '<br/>Enrolamiento Exitoso !!! Codigo de Activacion: '.str_pad(rand(0,9999), 4, "0", STR_PAD_LEFT);
            }
            else {
                echo '<br/>Respuesta 800: '.$enrollResult;
                detachPhone($formStructure["enviroment"], $mwStructure["idType"], $formStructure["document"], $formStructure["msisdn"]);
                exit;
            }
        }
        else { 
            echo '<br/>Respuesta 860: '.$enrollResult;
            detachPhone($formStructure["enviroment"], $mwStructure["idType"], $formStructure["document"], $formStructure["msisdn"]);
            exit;
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