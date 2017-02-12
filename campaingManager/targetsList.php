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
        <title> GCS Brocast Campaing Manager - Target List</title>
        <link rel="icon" href="img/tPago.ico" type="image/x-icon">
        <link href="css/bootstrap.css" rel="stylesheet">
        <style type="text/css">
        h1{text-align: left; margin-left: 5%}
        img{margin-left: 3%}
        th,tbody {text-align:center}
        body{margin-top: 15px;}
        p{margin-left: 10%}
        </style>
    </head>
    <body>
        <div class="header">
            <img src="img/tPago.png" alt="logo" />
            <h1 class="form-signin-heading">BCM Target List</h1>
        </div>
        <br>
        <br>
        <?php
        include 'addedFunctions.php';
        $targetsDS = pgQResult("SELECT * FROM t_targets where targets_id = $1",array($_GET['record']));
        echo "<div>";
        echo "<p><b>Target List Name:</b>  ".$targetsDS['0']['name']."</p>";
        echo "<p><b>Target List Description:</b>  ".$targetsDS['0']['description']."</p>";
        echo "<p><b>Target List Status:</b>  (".$targetsDS['0']['status'].")</p>";
        echo "</div>";
        echo "<br>";
        echo "<div class='row'>";
            echo "<div class='col-md-2'></div>";
            echo "<div class='col-md-8'>";
                echo "<table class='table table-striped table-bordered' id='targetsList'>";
                    echo "<thead>";
                        echo "<th>Target</th>";
                        echo "<th>Telco</th>";
                        echo "<th>Status</th>";
                        //<!--<th></th>-->
                    echo "<thead>";
                    echo "<tbody>";
                        //<?php
                        //include 'addedFunctions.php';
                        $targetsDS = pgQResult("SELECT * FROM v_targetslist where targets_id = $1",array($_GET['record']));
                        foreach ($targetsDS as $record){
                            echo "<tr>";
                            echo "<td>".$record["target"]."</td>";
                            echo "<td>".$record["telco"]."</td>";
                            echo "<td>".$record["status"]."</td>";
                            //echo "<td width='5%'><button type='button' class='btn btn-link btnViewTargets' onclick='window.location.href = \"http://localhost/targetDetail&record=".$record["targetsid"]."\";'>\n<span class='glyphicon glyphicon-file'>View</span></button></td>";
                            echo "</tr>";
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
