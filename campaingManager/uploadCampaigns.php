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
        <title> GCS Brocast Campaing Manager - Campaigns Manager</title>
        <link rel="icon" href="img/tPago.ico" type="image/x-icon">
        <link href="css/bootstrap.css" rel="stylesheet">
        <style type="text/css">
        h1{text-align: left; margin-left: 5%}
        img{margin-left: 3%}
        button{margin-right: 18%}
        th,tbody {text-align:center}
        body{margin-top: 15px;}
        </style>
    </head>
    <body>
        <div class="header">
            <img src="img/tPago.png" alt="logo" />
            <h1 class="form-signin-heading">BCM Campaigns Manager</h1>
        </div>
        <button class="btn btn-info" type="button" style="float: right" onclick="window.location.href = './createCampaign.php';">Add New Campaign</button>
        <br>
        <br>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <table class="table table-striped table-bordered" id="campaignsListView">
                    <thead>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Targets Count</th>
                        <th>Status</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </thead>
                    <tbody>
                        <?php
                        include 'addedFunctions.php';
                        $campaignsDS = pgQResult("select * from v_campaignscounter where country = $1",array($_SESSION['country']));
                        if($campaignsDS['0']['name'] <> ""){
                            foreach ($campaignsDS as $ccRecord){
                                echo "<tr>";
                                echo "<td>".$ccRecord["name"]."</td>";
                                echo "<td>".$ccRecord["description"]."</td>";
                                echo "<td>".$ccRecord["type"]."</td>";
                                echo "<td>".$ccRecord["targetscount"]."</td>";
                                echo "<td>".$ccRecord["status"]."</td>";
                                if($ccRecord["status"] <> 'I'){
                                    echo "<td width='5%'><button type='button' disabled class='btn btn-info btnViewTargets' onclick=''>\n<span class='glyphicon glyphicon-time'></span></button></td>";
                                    echo "<td width='5%'><button type='button' disabled class='btn btn-primary btnViewTargets' onclick=''>\n<span class='glyphicon glyphicon-file'></span></button></td>";
                                    echo "<td width='5%'><button type='button' disabled class='btn btn-danger btnDeleteTargets' onclick=''>\n<span class='glyphicon glyphicon-trash'></span></button></td>";
                                } else {
                                    echo "<td width='5%'><button type='button' class='btn btn-info btnViewTargets' onclick='window.location.href = \"./scheduleCampaign.php?campaingid=".$ccRecord['campaignid']."&targetid=".$ccRecord['targetid']."&name=".$ccRecord['name']."&description=".$ccRecord['description']."\";'>\n<span class='glyphicon glyphicon-time'></span></button></td>";
                                    echo "<td width='5%'><button type='button' disabled class='btn btn-primary btnViewTargets' onclick='window.location.href = \"./editCampaign.php?record=".$ccRecord["campaignid"]."\";'>\n<span class='glyphicon glyphicon-file'></span></button></td>";
                                    echo "<td width='5%'><button type='button' class='btn btn-danger btnDeleteTargets' onclick='window.location.href = \"./deleteCampaigns.php?record=".$ccRecord["campaignid"]."\";'>\n<span class='glyphicon glyphicon-trash'></span></button></td>";
                                }
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        // put your code here
        ?>
    </body>
</html>
