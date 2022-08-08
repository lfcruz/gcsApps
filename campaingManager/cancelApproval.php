<?php
session_start();
if(date("H:i") > date($_SESSION['expiretime'])){
    session_destroy();
    header("Location: index.php");
}
$_SESSION['expiretime'] = date("H:i", strtotime('+20 minutes'));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title> GCS Brocast Campaing Manager - Schedule Campaigns</title>
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
            <h1>Canceling approval.........</h1>
        </div>
    </body>
</html>
<?php
include 'addedFunctions.php';
echo "llego.";
pgQResult("update t_campaings set status = 'I' where campaing_id = $1", array($_GET['campaingid']));
pgQResult("update t_targets set status = 'I' where targets_id = $1", array($_GET['targetid']));
pgQResult("update t_targets_details set status = 'I' where targets_id = $1", array($_GET['targetid']));
email("<Broadcast Manager>bmanager@gcs-systems.com","lcruz@gcs-systems.com", "Broadcast Cancelled.", "Hello,\r\nScheduled campaign ".$_GET['campaingname']." has been canceled, please attend if necesary.\r\nRegards,");
header("Location: approveCampaigns.php");
?>
