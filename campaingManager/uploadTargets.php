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
        <title> GCS Brocast Campaing Manager - Target List Manager</title>
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
            <h1 class="form-signin-heading">BCM Targets Manager</h1>
        </div>
        <button class="btn btn-info" type="button" style="float: right" onclick="window.location.href = './loadFile.php';">Add New Target List</button>
        <br>
        <br>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <table class="table table-striped table-bordered" id="targetsListView">
                    <thead>
                        <th>Targets ID</th>
                        <th>Name</th>
                        <th>Count</th>
                        <th>Status</th>
                        <th></th>
                        <th></th>
                    </thead>
                    <tbody>
                        <?php
                        include 'addedFunctions.php';
                        $targetsDS = pgQResult("select * from v_targetscounter where country = $1",array($_SESSION['country']));
                        if($targetsDS['0']['targetsid'] <> ""){
                            foreach ($targetsDS as $record){
                                echo "<tr>";
                                echo "<td>".$record["targetsid"]."</td>";
                                echo "<td>".$record["targetsname"]."</td>";
                                echo "<td>".$record["targetscount"]."</td>";
                                echo "<td>".$record["targetsstatus"]."</td>";
                                echo "<td width='5%'><button type='button' class='btn btn-primary btnViewTargets' onclick='window.location.href = \"./targetsList.php?record=".$record["targetsid"]."\";'>\n<span class='glyphicon glyphicon-file'></span></button></td>";
                                if($record["targetsstatus"] == 'I'){
                                    echo "<td width='5%'><button type='button' class='btn btn-danger btnDeleteTargets' onclick='window.location.href = \"./deleteTargets.php?record=".$record["targetsid"]."\";'>\n<span class='glyphicon glyphicon-trash'></span></button></td>";
                                } else {
                                    echo "<td width='5%'><button type='button' disabled class='btn btn-danger btnDeleteTargets' onclick='window.location.href = \"./deleteTargets.php?record=".$record["targetsid"]."\";'>\n<span class='glyphicon glyphicon-trash'></span></button></td>";
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
