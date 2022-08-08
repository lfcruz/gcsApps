<?php
session_start();
if(date("H:i") > date($_SESSION['expiretime'])){
    session_destroy();
    header("Location: index.php");
}
$_SESSION['expiretime'] = date("H:i", strtotime('+10 minutes'));
?>

<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>GCS Broadcast Campaing Manager - Pannel</title>
        <link rel="icon" href="img/tPago.ico" type="image/x-icon">
        <link href="css/bootstrap.css" rel="stylesheet">
        <style type="text/css">
        h1{text-align: center}
        th,tbody {text-align:center}
        body{margin-top: 15px;}
        </style>
    </head>
    <body>
        <div class="header">
            <center><img src="img/tPago.png" alt="logo" align="middle"/></center>
            <h1 class="form-signin-heading">Broadcast Campaigns Manager</h1>
        </div>
        <div style="margin-left: 20%; margin-top: 10%">
        <button class="btn btn-info" type="button" style="float: left; width: 400px; height: 200px; font-weight: bolder; font-size: 200%" onclick="window.location.href = './uploadTargets.php';">Targets Manager</button>
        </div>
        <div style="margin-right: 20%; margin-top: 10%">
        <button class="btn btn-info" type="button" style="float: right; width: 400px; height: 200px; font-weight: bolder; font-size: 200%" onclick="window.location.href = './uploadCampaigns.php';">Campaigns Manager</button>
        </div>
        <br>
        <br>
    </body>
</html>
