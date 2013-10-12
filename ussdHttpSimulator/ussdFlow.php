<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>tPago - Ussd Web Simulator</title>
    </head>
    <body>
<?php
include 'addedFunctions.php';

// Variables Declaration -------------------------------------------------------
$menuResult = null;
$ussdReqStructure = array ('transactionId' => $_POST[transactionid],
                           'dialogId' => $_POST[dialogid],
                           'text' => $_POST[option],
                           'number' => $_POST[number],
                           'status' => $_POST[status]);
//setcookie('transactionid',$ussdReqStructure[transactionId],0);
//setcookie('dialogid',$ussdReqStructure[dialogid],0);
// Main procedure --------------------------------------------------------------
    $menuResult = getMenu($ussdReqStructure);
    $vtransid = '<input type="hidden" name="transactionid" value="'.$ussdReqStructure[transactionId].'">';
    $vdialoid = '<input type="hidden" name="dialogid" value="'.$ussdReqStructure[dialogId].'">';
    $vnumber = '<input type="hidden" name="number" value="'.$ussdReqStructure[number].'">';
    $vtext = '<input type="hidden" name="text" value="'.$_POST[option].'">';
        if(substr($menuResult,0,1) === "1"){
            $vstatus = '<input type="hidden" name="status" value="continue">';
        }
        else{
            $vstatus = '<input type="hidden" name="status" value="end">';
        }
        echo '<form name="mobScreen" method="post" action="/ussdHttpSimulator/ussdFlow.php">';
        echo substr($menuResult,1,  strlen($menuResult));
        echo $vtransid;
        echo $vdialoid;
        echo $vnumber;
        echo $vtext;
        echo $vstatus;
        echo 'Select Option: <input type="text" name="option" value=""><br/>';
        echo '<div><input type="submit" value="submit"></div></form>';
?>
    </body>
</html>
