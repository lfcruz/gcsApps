<?php
session_start();
if(date("H:i") > date($_SESSION['expiretime'])){
    session_destroy();
    header("Location: index.php");
}
$_SESSION['expiretime'] = date("H:i", strtotime('+20 minutes'));
?>
<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title> GCS Brocast Campaing Manager - New Campaigns</title>
        <link rel="icon" href="img/tPago.ico" type="image/x-icon">
        <link href="css/bootstrap.css" rel="stylesheet">
        <style type="text/css">
        h1{text-align: left; margin-left: 5%}
        img{margin-left: 3%}
        th,tbody {text-align:center}
        button{margin-right: 18%}
        body{margin-top: 15px;}
        p{margin-left: 10%}
        </style>
    </head>
    <body>
        <div class="header">
            <img src="img/tPago.png" alt="logo" />
            <h1 class="form-signin-heading">BCM New Campaign</h1>
        </div>
        <br>
        <br>
        <div class="container">
            <form action="storeCampaign.php" method="post">
                <label class="control-label" for="displayname">Campaign Name</label>
                <input name="name" type="text" class="form-control" placeholder="Campaign Name" maxlength="20" required autofocus>
                <label for="displayname">Campaign Description</label>
                <input name="description" type="text" class="form-control" placeholder="Campaign Description" maxlength="50" required>
                <?php
                    include 'addedFunctions.php';
                    $campaignTypeDS = pgQResult("select * from t_target_types", array());
                    $targetsDS = pgQResult("select targets_id, name from t_targets where status = 'I' and country = $1", array($_SESSION['country']));
                    echo "<label for='displayname'>Campaign Type</label><br>";
                    echo "<select name='type' required>";
                    foreach ($campaignTypeDS as $cRecrod){
                        echo "<option value='".$cRecrod['target_type_id']."'>".$cRecrod['name']."</option>";
                    }
                    echo "</select><br>";
                    echo "<label for='displayname'>Campaign Target List</label><br>";
                    echo "<select name='targetlist' required>";
                    foreach ($targetsDS as $tRecrod){
                        echo "<option value='".$tRecrod['targets_id']."'>".$tRecrod['name']."</option>";
                    }
                    echo "</select><br>";
                ?>
                <label for="displayname">Message</label>
                <input name="message" type="text" class="form-control" placeholder="Campaign Message" maxlength="160" required>
                <br>
                <div class="col-xs-2" style="float:right">
                    <button class="btn btn-lg btn-info btn-block"  style="float:right" type="submit">Upload</button>
                </div>
            </form>
        </div>
 
    </body>
</html>
