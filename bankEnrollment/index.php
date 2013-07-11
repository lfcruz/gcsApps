<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
</head>
<body>
<?php
//Example messages for enrollment
    $msg860='<MESSAGE TYPE="860" BANKID="102" CORRELATIONID="2012071212545622321UL65d"><CLIENT ID="00111054938" TYPE="Cedula" TELEPHONE="8292147747" TELCOID="200" BPSEQUENCE="111644" /><TRANSACTION DATE="12072012" TIME="125456" /></MESSAGE>';
    $msg8001='<MESSAGE TYPE="800" BANKID="102" CORRELATIONID="2012071212553631598UL9ee"><CLIENT ID="00111054938" TYPE="Cedula" TELEPHONE="8292147747" TELCOID="200" BPSEQUENCE="111645" /><PRODUCTS><PRODUCT ID="01" ACCOUNT="*****3635" TYPE="DDA" CURRENCY="DOP" ALIAS="BP_DDA" /></PRODUCTS><TRANSACTION DATE="12072012" TIME="125536" /></MESSAGE>';
    $msg8002='<MESSAGE TYPE="800" BANKID="102" CORRELATIONID="2012071212553631598UL9ee"><CLIENT ID="00111054938" TYPE="Cedula" TELEPHONE="8292147747" TELCOID="200" BPSEQUENCE="111645" /><PRODUCTS><PRODUCT ID="01" ACCOUNT="*****3635" TYPE="SAV" CURRENCY="DOP" ALIAS="BP_SAV" /><PRODUCT ID="02" ACCOUNT="*****3636" TYPE="DDA" CURRENCY="DOP" ALIAS="BP_DDA" /></PRODUCTS><TRANSACTION DATE="12072012" TIME="125536" /></MESSAGE>';
    $msg8003='<MESSAGE TYPE="800" BANKID="102" CORRELATIONID="2012071212553631598UL9ee"><CLIENT ID="00111054938" TYPE="Cedula" TELEPHONE="8292147747" TELCOID="200" BPSEQUENCE="111645" /><PRODUCTS><PRODUCT ID="01" ACCOUNT="*****3635" TYPE="SAV" CURRENCY="DOP" ALIAS="BP_SAV" /><PRODUCT ID="02" ACCOUNT="*****3636" TYPE="DDA" CURRENCY="DOP" ALIAS="BP_DDA" /><PRODUCT ID="03" ACCOUNT="*****6041" TYPE="CC" CURRENCY="DOP" ALIAS="BP_CC" /></PRODUCTS><TRANSACTION DATE="12072012" TIME="125536" /></MESSAGE>';
    $msg8004='<MESSAGE TYPE="800" BANKID="102" CORRELATIONID="2012071212553631598UL9ee"><CLIENT ID="00111054938" TYPE="Cedula" TELEPHONE="8292147747" TELCOID="200" BPSEQUENCE="111645" /><PRODUCTS><PRODUCT ID="01" ACCOUNT="*****3635" TYPE="SAV" CURRENCY="DOP" ALIAS="BP_SAV" /><PRODUCT ID="02" ACCOUNT="*****3636" TYPE="DDA" CURRENCY="DOP" ALIAS="BP_DDA" /><PRODUCT ID="03" ACCOUNT="*****6041" TYPE="CC" CURRENCY="DOP" ALIAS="BP_CC" /><PRODUCT ID="04" ACCOUNT="*****3737" TYPE="LOAN" CURRENCY="DOP" ALIAS="BP_LOAN" /></PRODUCTS><TRANSACTION DATE="12072012" TIME="125536" /></MESSAGE>';
    $msg8005='<MESSAGE TYPE="800" BANKID="102" CORRELATIONID="2012071212553631598UL9ee"><CLIENT ID="00111054938" TYPE="Cedula" TELEPHONE="8292147747" TELCOID="200" BPSEQUENCE="111645" /><PRODUCTS><PRODUCT ID="01" ACCOUNT="*****3635" TYPE="SAV" CURRENCY="DOP" ALIAS="BP_SAV" /><PRODUCT ID="02" ACCOUNT="*****3636" TYPE="DDA" CURRENCY="DOP" ALIAS="BP_DDA" /><PRODUCT ID="03" ACCOUNT="*****6041" TYPE="CC" CURRENCY="DOP" ALIAS="BP_CC" /><PRODUCT ID="04" ACCOUNT="*****3737" TYPE="LOAN" CURRENCY="DOP" ALIAS="BP_LOAN" /><PRODUCT ID="05" ACCOUNT="*****3737" TYPE="LOAN" CURRENCY="DOP" ALIAS="BP_LOAN" /></PRODUCTS><TRANSACTION DATE="12072012" TIME="125536" /></MESSAGE>';
    $msg8006='<MESSAGE TYPE="800" BANKID="102" CORRELATIONID="2012071212553631598UL9ee"><CLIENT ID="00111054938" TYPE="Cedula" TELEPHONE="8292147747" TELCOID="200" BPSEQUENCE="111645" /><PRODUCTS><PRODUCT ID="01" ACCOUNT="*****3635" TYPE="SAV" CURRENCY="DOP" ALIAS="BP_SAV" /><PRODUCT ID="02" ACCOUNT="*****3636" TYPE="DDA" CURRENCY="DOP" ALIAS="BP_DDA" /><PRODUCT ID="03" ACCOUNT="*****6041" TYPE="CC" CURRENCY="DOP" ALIAS="BP_CC" /><PRODUCT ID="04" ACCOUNT="*****3737" TYPE="LOAN" CURRENCY="DOP" ALIAS="BP_LOAN" /><PRODUCT ID="05" ACCOUNT="*****3737" TYPE="LOAN" CURRENCY="DOP" ALIAS="BP_LOAN" /><PRODUCT ID="06" ACCOUNT="*****3737" TYPE="LOAN" CURRENCY="DOP" ALIAS="BP_LOAN" /></PRODUCTS><TRANSACTION DATE="12072012" TIME="125536" /></MESSAGE>';
    $msg8007='<MESSAGE TYPE="800" BANKID="102" CORRELATIONID="2012071212553631598UL9ee"><CLIENT ID="00111054938" TYPE="Cedula" TELEPHONE="8292147747" TELCOID="200" BPSEQUENCE="111645" /><PRODUCTS><PRODUCT ID="01" ACCOUNT="*****3635" TYPE="SAV" CURRENCY="DOP" ALIAS="BP_SAV" /><PRODUCT ID="02" ACCOUNT="*****3636" TYPE="DDA" CURRENCY="DOP" ALIAS="BP_DDA" /><PRODUCT ID="03" ACCOUNT="*****6041" TYPE="CC" CURRENCY="DOP" ALIAS="BP_CC" /><PRODUCT ID="04" ACCOUNT="*****3737" TYPE="LOAN" CURRENCY="DOP" ALIAS="BP_LOAN" /><PRODUCT ID="05" ACCOUNT="*****3737" TYPE="LOAN" CURRENCY="DOP" ALIAS="BP_LOAN" /><PRODUCT ID="06" ACCOUNT="*****3737" TYPE="LOAN" CURRENCY="DOP" ALIAS="BP_LOAN" /><PRODUCT ID="07" ACCOUNT="*****3737" TYPE="LOAN" CURRENCY="DOP" ALIAS="BP_LOAN" /></PRODUCTS><TRANSACTION DATE="12072012" TIME="125536" /></MESSAGE>';
    $msg8008='<MESSAGE TYPE="800" BANKID="102" CORRELATIONID="2012071212553631598UL9ee"><CLIENT ID="00111054938" TYPE="Cedula" TELEPHONE="8292147747" TELCOID="200" BPSEQUENCE="111645" /><PRODUCTS><PRODUCT ID="01" ACCOUNT="*****3635" TYPE="SAV" CURRENCY="DOP" ALIAS="BP_SAV" /><PRODUCT ID="02" ACCOUNT="*****3636" TYPE="DDA" CURRENCY="DOP" ALIAS="BP_DDA" /><PRODUCT ID="03" ACCOUNT="*****6041" TYPE="CC" CURRENCY="DOP" ALIAS="BP_CC" /><PRODUCT ID="04" ACCOUNT="*****3737" TYPE="LOAN" CURRENCY="DOP" ALIAS="BP_LOAN" /><PRODUCT ID="05" ACCOUNT="*****3737" TYPE="LOAN" CURRENCY="DOP" ALIAS="BP_LOAN" /><PRODUCT ID="06" ACCOUNT="*****3737" TYPE="LOAN" CURRENCY="DOP" ALIAS="BP_LOAN" /><PRODUCT ID="07" ACCOUNT="*****3737" TYPE="LOAN" CURRENCY="DOP" ALIAS="BP_LOAN" /><PRODUCT ID="08" ACCOUNT="*****3737" TYPE="LOAN" CURRENCY="DOP" ALIAS="BP_LOAN" /></PRODUCTS><TRANSACTION DATE="12072012" TIME="125536" /></MESSAGE>';

//Definition of variables to receive on post -----------------------------------
    $bank=$_POST['bank'];
    $msisdn=$_POST['MSISDN'];
    $telco=$_POST['telco'];
    $tipodoc=$_POST["tipodoc"];
    $documento=$_POST["documento"];
    $environment=$_POST['environment'];
    
    $account=$_POST['account'];
    $accounttype=$_POST['accounttype'];
    $alias=$_POST['alias'];
    
    $account2=$_POST['account2'];
    $accounttype2=$_POST['accounttype2'];
    $alias2=$_POST['alias2'];
    
    $account3=$_POST['account3'];
    $accounttype3=$_POST['accounttype3'];
    $alias3=$_POST['alias3'];
    
    $account4=$_POST['account4'];
    $accounttype4=$_POST['accounttype4'];
    $alias4=$_POST['alias4'];
    
    $account5=$_POST['account5'];
    $accounttype5=$_POST['accounttype5'];
    $alias5=$_POST['alias5'];

    $account6=$_POST['account6'];
    $accounttype6=$_POST['accounttype6'];
    $alias6=$_POST['alias6'];

    $account7=$_POST['account7'];
    $accounttype7=$_POST['accounttype7'];
    $alias7=$_POST['alias7'];

    $account8=$_POST['account8'];
    $accounttype8=$_POST['accounttype8'];
    $alias8=$_POST['alias8'];
    
    $cuenta1=$_POST['cuenta1'];
    $cuenta2=$_POST['cuenta2'];
    $cuenta3=$_POST['cuenta3'];
    $cuenta4=$_POST['cuenta4'];
    $cuenta5=$_POST['cuenta5'];
    $cuenta6=$_POST['cuenta6'];
    $cuenta7=$_POST['cuenta7'];
    $cuenta8=$_POST['cuenta8'];


//Define the form components on page load --------------------------------------
    if($bank == null && $status == null){
        echo '<form name="form1" method="post" action="">';
            echo 'Seleccione el Ambiente: ';
            echo '<select name="environment">';
                echo '<option value="172.19.1.19">Desarrollo</option>';
                echo '<option value="172.19.3.12">Staging</option>';
                echo '<option value="172.19.3.41">Neoris</option>';
                echo '</select></br>';
            echo 'Seleccione el Banco: ';
            echo '<select name="bank">';
                echo '<option value="102">Banco Popular</option>';
                echo '<option value="BDP">Banco del Progreso</option>';
                echo '<option value="BDI">Banco BDI</option>';
                echo '<option value="BLH">Banco Lopez de Haro</option>';
                echo '<option value="ALV">Banco Alaver</option>';
                echo '<option value="ADO">Banco Adopem</option>';
                echo '<option value="ADM">Banco Ademi</option>';
                echo '<option value="UNB">Banco Union</option>';
                echo '<option value="SCT">Banco ScotiaBank</option>';
                echo '</select></br>';
            echo 'Seleccione el Telco: ';
            echo '<select name="telco">';
                echo '<option value="200">Codetel</option>';
                echo '<option value="300">Orange</option>';
                echo '<option value="400">Viva</option>';
                echo '</select></br>';
            echo 'Seleccione el Tipo Documento: ';
            echo '<select name="tipodoc">';
                echo '<option value="CEDULA">Cedula</option>';
                echo '<option value="PASAPORTE">Pasaporte</option>';
                echo '</select></br>';
            echo 'Digite Telefono : <input type="text" name="MSISDN" value="">';
            echo 'Digite la Cedula: <input type="text" name="documento" value=""><br/>';            
            echo 'Cuenta 1: <input type="checkbox" name="cuenta1" value="active"><input type="text" name="account" value="*****3631" readonly>';
                echo 'Tipo 1: <input type="text" name="accounttype" value="SAV">';
                echo 'Alias 1: <input type="text" name="alias" value="sav01-" ><br/>';
            echo 'Cuenta 2: <input type="checkbox" name="cuenta2" value="active"><input type="text" name="account2" value="*****3632" readonly>';
                echo 'Tipo 2: <input type="text" name="accounttype2" value="SAV">';
                echo 'Alias 2: <input type="text" name="alias2" value="sav02-" ><br/>';                
            echo 'Cuenta 3: <input type="checkbox" name="cuenta3" value="active"><input type="text" name="account3" value="*****3633" readonly>';
                echo 'Tipo 3: <input type="text" name="accounttype3" value="SAV">';
                echo 'Alias 3: <input type="text" name="alias3" value="sav03-" ><br/>';
            echo 'Cuenta 4: <input type="checkbox" name="cuenta4" value="active"><input type="text" name="account4" value="*****3634" readonly>';
                echo 'Tipo 4: <input type="text" name="accounttype4" value="DDA">';
                echo 'Alias 4: <input type="text" name="alias4" value="dda01-" ><br/>';
            echo 'Cuenta 5: <input type="checkbox" name="cuenta5" value="active"><input type="text" name="account5" value="*****3635" readonly>';
                echo 'Tipo 5: <input type="text" name="accounttype5" value="DDA">';
                echo 'Alias 5: <input type="text" name="alias5" value="dda02-" ><br/>';
                
            echo 'Cuenta 6: <input type="checkbox" name="cuenta6" value="active"><input type="text" name="account6" value="************6044" readonly>';
                echo 'Tipo 6: <input type="text" name="accounttype6" value="CC" readonly>';
                echo 'Alias 6: <input type="text" name="alias6" value="cc01-" ><br/>';
            echo 'Cuenta 7: <input type="checkbox" name="cuenta7" value="active"><input type="text" name="account7" value="************6085" readonly>';
                echo 'Tipo 7: <input type="text" name="accounttype7" value="CC" readonly>';
                echo 'Alias 7: <input type="text" name="alias7" value="cc02-" ><br/>';
            
            echo 'Cuenta 8: <input type="checkbox" name="cuenta8" value="active"><input type="text" name="account8" value="*****3638" readonly>';
                echo 'Tipo 8: <input type="text" name="accounttype8" value="LOAN">';
                echo 'Alias 8: <input type="text" name="alias8" value="loa01-" ><br/>';
        echo '<div><input type="submit" value="submit"></div></form>';
    }

$products=0;
if($cuenta1 != null)
        $products=1;

if($cuenta2 != null)
        $products+=1;

if($cuenta3 != null)
        $products+=1;

if($cuenta4 != null)
        $products+=1;

if($cuenta5 != null)
        $products=1;

if($cuenta6 != null)
        $products+=1;

if($cuenta7 != null)
        $products+=1;

if($cuenta8 != null)
        $products+=1;

switch ($products) {
        case 1:
                $msg800=$msg8001;
                break;
        case 2:
                $msg800=$msg8002;
                break;
        case 3:
                $msg800=$msg8003;
                break;
        case 4:
                $msg800=$msg8004;
                break;
        case 5:
                $msg800=$msg8005;
                break;
        case 6:
                $msg800=$msg8006;
                break;
        case 7:
                $msg800=$msg8007;
                break;
        case 8:
                $msg800=$msg8008;
                break;

        default:
                $bank='';
}

// if post is submitted
if($bank != null && $environment != null && $telco != null && $tipodoc != null) {
    echo 'Ambiente : '.$environment;

    $dom = new DOMDocument;
    $dom->loadXML($msg860);
    if (!$dom) {
        echo 'Error while parsing the message';
        //exit;
    }
    $tmp = simplexml_import_dom($dom);

    //complete message 860 with post values
    $tmp["BANKID"]=$bank;
    $tmp->CLIENT["ID"]=$documento;
    if($tipodoc=="CEDULA"){
        $tmp->CLIENT["TYPE"]="CEDULA";
    }
    else{ 
        $tmp->CLIENT["TYPE"]="PASAPORTE";
    }
    $tmp->CLIENT["TELEPHONE"]=$msisdn;
    $tmp->CLIENT["TELCOID"]=$telco;
    $tmp->CLIENT["BPSEQUENCE"]=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);

    //create a socket to send message to core
    $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_connect($sock, $environment, '8888');
    $sent = socket_write($sock, $tmp->asXML(), strlen($tmp->asXML()));

    //read response message from core
    $input = socket_read($sock, 1024);
    $dom = new DOMDocument;
    $dom->loadXML($input);
    if (!$dom) {
        echo 'Error while parsing the message';
    }
    $tmp = simplexml_import_dom($dom);

    //echo '<br/>Respuesta 860: '.$tmp->TRANSACTION["RESPONSECODE"];

    //if response code is successfull continue and send message 800
    if($tmp->TRANSACTION["RESPONSECODE"]=="0000"){

        echo '<br/>Respuesta 860: '.$tmp->TRANSACTION["RESPONSECODE"];
        $dom->loadXML($msg800);
        if (!$dom) {
            echo 'Error while parsing the message';
            //exit;
        }
        $tmp = simplexml_import_dom($dom);
        //complete message 800 with post fields
        $tmp["BANKID"]=$bank;
        $tmp->CLIENT["ID"]=$documento;
        if($tipodoc=="CEDULA"){
            $tmp->CLIENT["TYPE"]="CEDULA";
        }else{
            $tmp->CLIENT["TYPE"]="PASAPORTE";
        }
        $tmp->CLIENT["TELEPHONE"]=$msisdn;
        $tmp->CLIENT["TELCOID"]=$telco;
        $tmp->CLIENT["BPSEQUENCE"]=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);

        $order=0;
        if($cuenta1 != null){
            $tmp->PRODUCTS->PRODUCT[$order]["ID"]='0'.$order+1;
            $tmp->PRODUCTS->PRODUCT[$order]["ACCOUNT"]=$account;
            $tmp->PRODUCTS->PRODUCT[$order]["TYPE"]=$accounttype;
            $tmp->PRODUCTS->PRODUCT[$order]["CURRENCY"]="DOP";
            $tmp->PRODUCTS->PRODUCT[$order]["ALIAS"]=$alias;
        $order+=1;
        }
        if($cuenta2 != null){
            $tmp->PRODUCTS->PRODUCT[$order]["ID"]='0'.$order+1;
            $tmp->PRODUCTS->PRODUCT[$order]["ACCOUNT"]=$account2;
            $tmp->PRODUCTS->PRODUCT[$order]["TYPE"]=$accounttype2;
            $tmp->PRODUCTS->PRODUCT[$order]["CURRENCY"]="DOP";
            $tmp->PRODUCTS->PRODUCT[$order]["ALIAS"]=$alias2;
            $order+=1;
        }
        if($cuenta3 != null){
            $tmp->PRODUCTS->PRODUCT[$order]["ID"]='0'.$order+1;
            $tmp->PRODUCTS->PRODUCT[$order]["ACCOUNT"]=$account3;
            $tmp->PRODUCTS->PRODUCT[$order]["TYPE"]=$accounttype3;
            $tmp->PRODUCTS->PRODUCT[$order]["CURRENCY"]="DOP";
            $tmp->PRODUCTS->PRODUCT[$order]["ALIAS"]=$alias3;
            $order+=1;
        }
        if($cuenta4 != null){
            $tmp->PRODUCTS->PRODUCT[$order]["ID"]='0'.$order+1;
            $tmp->PRODUCTS->PRODUCT[$order]["ACCOUNT"]=$account4;
            $tmp->PRODUCTS->PRODUCT[$order]["TYPE"]=$accounttype4;
            $tmp->PRODUCTS->PRODUCT[$order]["CURRENCY"]="DOP";
            $tmp->PRODUCTS->PRODUCT[$order]["ALIAS"]=$alias4;
            $order+=1;
        }
        if($cuenta5 != null){
            $tmp->PRODUCTS->PRODUCT[$order]["ID"]='0'.$order+1;
            $tmp->PRODUCTS->PRODUCT[$order]["ACCOUNT"]=$account5;
            $tmp->PRODUCTS->PRODUCT[$order]["TYPE"]=$accounttype5;
            $tmp->PRODUCTS->PRODUCT[$order]["CURRENCY"]="DOP";
            $tmp->PRODUCTS->PRODUCT[$order]["ALIAS"]=$alias5;
            $order+=1;
        }
        if($cuenta6 != null){
            $tmp->PRODUCTS->PRODUCT[$order]["ID"]='0'.$order+1;
            $tmp->PRODUCTS->PRODUCT[$order]["ACCOUNT"]=$account6;
            $tmp->PRODUCTS->PRODUCT[$order]["TYPE"]=$accounttype6;
            $tmp->PRODUCTS->PRODUCT[$order]["CURRENCY"]="DOP";
            $tmp->PRODUCTS->PRODUCT[$order]["ALIAS"]=$alias6;
            $order+=1;
        }
        if($cuenta7 != null){
            $tmp->PRODUCTS->PRODUCT[$order]["ID"]='0'.$order+1;
            $tmp->PRODUCTS->PRODUCT[$order]["ACCOUNT"]=$account7;
            $tmp->PRODUCTS->PRODUCT[$order]["TYPE"]=$accounttype7;
            $tmp->PRODUCTS->PRODUCT[$order]["CURRENCY"]="DOP";
            $tmp->PRODUCTS->PRODUCT[$order]["ALIAS"]=$alias7;
            $order+=1;
        }
        if($cuenta8 != null){
            $tmp->PRODUCTS->PRODUCT[$order]["ID"]='0'.$order+1;
            $tmp->PRODUCTS->PRODUCT[$order]["ACCOUNT"]=$account8;
            $tmp->PRODUCTS->PRODUCT[$order]["TYPE"]=$accounttype8;
            $tmp->PRODUCTS->PRODUCT[$order]["CURRENCY"]="DOP";
            $tmp->PRODUCTS->PRODUCT[$order]["ALIAS"]=$alias8;
        }

        var_dump($tmp);

        //send message 800 to core
        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_connect($sock, $environment, '8888');
        $sent = socket_write($sock, $tmp->asXML(), strlen($tmp->asXML()));

        //read response from core
        $input = socket_read($sock, 1024);
        $dom = new DOMDocument;
        $dom->loadXML($input);
        if (!$dom) {
            echo 'Error while parsing the message';
            //exit;
        }
        $tmp = simplexml_import_dom($dom);

        if($tmp->TRANSACTION["RESPONSECODE"]=="0000"){
           echo '<br/>Respuesta 800: '.$tmp->TRANSACTION["RESPONSECODE"];
           echo '<br/>Enrolamiento Exitoso !!! Codigo de Activacion: '.str_pad(rand(0,9999), 4, "0", STR_PAD_LEFT);;
        }else echo '<br/>Respuesta 800: '.$tmp->TRANSACTION["RESPONSECODE"];



    }else echo '<br/>Respuesta 860: '.$tmp->TRANSACTION["RESPONSECODE"];



}



        
        ?>
    </body>
</html>
