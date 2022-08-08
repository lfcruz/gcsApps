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
        <title> GCS Brocast Campaing Manager - Schedule Campaigns</title>
        <link rel="icon" href="img/tPago.ico" type="image/x-icon">
        <link href="css/bootstrap.css" rel="stylesheet">
        <style type="text/css">
        h1{text-align: left; margin-left: 5%}
        img{margin-left: 3%}
        th,tbody {text-align:center}
        button{margin-right: 18%}
        body{margin-top: 15px;}
        p{margin-left: 10%}
        #sched {display: inline}
        </style>
    </head>
    <body>
        <div class="header">
            <img src="img/tPago.png" alt="logo" />
            <h1 class="form-signin-heading">BCM Schedule Campaign</h1>
        </div>
        <br>
        <br>
        <?php
        include 'addedFunctions.php';
        $minDate = date("Y-m-d");
        $minTime = date("H:i", strtotime('+10 minutes'));
        if (date("H") > 19 or date("H")< 8){
            $minTime = "08:10";
        }
        echo "<div class='container'>";
            echo "<form action='storeSchedule.php' method='post' id='sched'>";
                echo "<div class='container'>";
                echo "<input type='hidden' name='campaingid' value='".$_GET['campaingid']."'";
                echo "<label class='control-label' for='displayname'>Campaign Name</label>";
                echo "</div>";
                echo "<div class='container'>";
                echo "<input name='name' type='text' class='form-control' placeholder='".$_GET['name']."' value='".$_GET['name']."' readonly>";
                echo "</div>";
                echo "<div class='container'>";
                echo "<input type='hidden' name='targetid' value='".$_GET['targetid']."'";
                echo "<label for='displayname'>Campaign Description</label>";
                echo "</div>";
                echo "<div class='container'>";
                echo "<input name='description' type='text' class='form-control' placeholder='".$_GET['description']."' value='".$_GET['description']."' readonly>";
                echo "</div>";
                echo "<div class='container'>";
                    echo "<label for='displayname'>Schedule</label>";
                    echo "<div class='container'>";
                        echo "<label for='date'>Date</label>";
                        echo "<input class='form-control' style='width:200px' name='date' id='date' type='date' placeholder='".$minDate."' min='".$minDate."' value='".$minDate."' required>";
                    echo "</div>";
                    echo "<div class='container'>";
                        echo "<label for='time'>Time</label>";
                        echo "<input class='form-control' style='width:200px' name='time' id='time' type='time' placeholder='".$minTime."' min='08:30' max='19:00:00' value='".$minTime."' required>";
                    echo "</div>";
                echo "</div>";
                ?>
                <br>
                <div class="col-xs-2" style="float:right">
                    <button class="btn btn-lg btn-info btn-block"  style="float:right" type="submit">Schedule</button>
                </div>
            </form>
        </div>
    </body>
</html>
