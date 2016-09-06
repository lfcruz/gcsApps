<?php
session_start();
if((date("H:i") > date($_SESSION['expiretime'])) or ($_SESSION['username'] <> 'lrodriguez')){
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
            <h1 class="form-signin-heading">BCM Campaigns Approval</h1>
        </div>
        <br>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <table class="table table-striped table-bordered" id="campaignsListView">
                    <thead>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Claro</th>
                        <th>Orange</th>
                        <th>Viva</th>
                        <th></th>
                        <th></th>
                    </thead>
                    <tbody>
                        <?php
                        include 'addedFunctions.php';
                        $campaignsDS = pgQResult("select * from v_campaingstelcos where status = $1",array('S'));
                        if($campaignsDS['0']['targetsid'] <> ""){
                            foreach ($campaignsDS as $ccRecord){
                                echo "<tr>";
                                echo "<td>".$ccRecord["campaing"]."</td>";
                                echo "<td>".$ccRecord["type"]."</td>";
                                echo "<td>".$ccRecord["codetel"]."</td>";
                                echo "<td>".$ccRecord["orange"]."</td>";
                                echo "<td>".$ccRecord["viva"]."</td>";
                                echo "<td width='5%'><button type='button' class='btn btn-success btnViewTargets' onclick='window.location.href = \"./storeApproval.php?campaingid=".$ccRecord['campaingid']."&targetid=".$ccRecord['targetsid']."&campaingname=".$ccRecord['campaing']."\";'>\n<span class='glyphicon glyphicon-ok'></span></button></td>";
                                echo "<td width='5%'><button type='button' class='btn btn-danger btnDeleteTargets' onclick='window.location.href = \"./cancelApproval.php?campaingid=".$ccRecord["campaingid"]."&targetid=".$ccRecord['targetsid']."&campaingname=".$ccRecord['campaing']."\";'>\n<span class='glyphicon glyphicon-remove'></span></button></td>";
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
