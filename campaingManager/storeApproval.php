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
            <h1>Saving approval.........</h1>
        </div>
    </body>
</html>
<?php
include 'addedFunctions.php';
$webfilename = pgQResult("select nextval('sq_webmessagefile')", array());
$text = pgQResult("select global_message from t_campaings where campaing_id =$1", array($_GET['campaingid']));
pgQResult("update t_campaings set status = 'A', web_message_filename = $2  where campaing_id = $1", array($_GET['campaingid']), $webfilename[0]['nextval']);

pgQResult("update t_targets set status = 'A' where targets_id = $1", array($_GET['targetid']));
pgQResult("update t_targets_details set status = 'A' where targets_id = $1", array($_GET['targetid']));
email("<Broadcast Manager>bmanager@gcs-systems.com","lcruz@gcs-systems.com", "Broadcast Approval.", "Hello,\r\nScheduled campaign ".$_GET['campaingname']." has been approved.\r\nRegards,");
$data = "<html><head></head><body>".$text[0]['global_message']."</body></html>";
file_put_contents("../claroPush/".$webfilename[0]['nextval'].".html",$data);
header("Location: approveCampaigns.php");
?>