<?php
session_start();
if(date("H:i") > date($_SESSION['expiretime'])){
    session_destroy();
    header("Location: index.php");
}
$_SESSION['expiretime'] = date("H:i", strtotime('+10 minutes'));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title> GCS Brocast Campaing Manager - New Campaigns</title>
        <link rel="icon" href="img/tPago.ico" type="image/x-icon">
        <link href="css/bootstrap.css" rel="stylesheet">
        <style type="text/css">
        h1{border-style: none;
           border-color: #ccc;
           border-radius: 10px;
           background-color: #8a2be2;
        }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <h1>Saving campaign.........</h1>
        </div>
    </body>
</html>
<?php
include 'addedFunctions.php';
pgQResult("insert into t_campaings (campaing_id,name,description,campaing_type,targets_id,created_date,status,global_message,country) values (DEFAULT,$1,$2,$3,$4,now(),DEFAULT,$5,$6)", array($_POST['name'],$_POST['description'],$_POST['type'],$_POST['targetlist'],$_POST['message'],$_SESSION['country']));
header("Location: uploadCampaigns.php");
?>
