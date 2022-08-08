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
        <title> GCS Broadcast Campaign Manager - Schedule Campaigns</title>
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
            <h1>Saving schedule.........</h1>
        </div>
    </body>
</html>
<?php
include 'addedFunctions.php';
$schedDate = $_POST['date']." ".$_POST['time'];
pgQResult("update t_campaings set status = 'S', scheduled_date = $1 where campaing_id = $2", array($schedDate,$_POST['campaingid']));
pgQResult("update t_targets set status = 'S' where targets_id = $1", array($_POST['targetid']));
pgQResult("update t_targets_details set status = 'S' where targets_id = $1", array($_POST['targetid']));
email("<Broadcast Manager>bmanager@gcs-systems.com","broadcastapproval@gcs-systems.com", "New Broadcast Approval Request.", "http://broadcastmanager.gcs.local/broadcastCampaings/campaingManager/approveCampaigns.php");
header("Location: uploadCampaigns.php");
?>
