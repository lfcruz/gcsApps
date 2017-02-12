<?php
include '../addedFunctions.php';

function cleanQueue(){
    $queues = pgQResult("select * from t_queue where campaing_id is not null", array());
    if (!empty($queues)){
        foreach ($queues as $queue){
            $finishedCampaings = pgQResult("select * from v_finishedcampaings where campaingid = $1 and targetsid = $2", array($queue['campaing_id'], $queue['targets_id']));
            if(empty($finishedCampaings)){
                pgQResult("update t_campaings set status = 'I' where campaing_id = $1", array($queue['campaing_id']));
                pgQResult("update t_targets set status = 'I' where targets_id = $1", array($queue['targets_id']));
                pgQResult("update t_targets_details set status = 'I' where targets_id = $1", array($queue['targets_id']));
                pgQResult("update t_queue set campaing_id = null, targets_id = null, campaing_type_id = null where queue_id = $1", array($queue['queue_id']));
            }
        }
    }   
}

function populateQueue(){
    $queues = pgQResult("select * from t_queue where campaing_id is null", array());
    if (!empty($queues)){
        foreach ($queues as $queue){
            $activeCampaings = pgQResult("select * from v_activecampaings", array());
            
            pgQResult("update t_campaings set status = 'P' where campaing_id = $1", array($activeCampaings[0]['campaingid']));
            pgQResult("update t_targets set status = 'P' where targets_id = $1", array($activeCampaings[0]['targetsid']));
            pgQResult("update t_targets_details set status = 'P' where targets_id = $1", array($activeCampaings[0]['targetsid']));
            pgQResult("update t_queue set campaing_id = $1, targets_id = $2, campaing_type_id = $3, filename = $4 where queue_id = $5", array($activeCampaings[0]['campaingid'], $activeCampaings[0]['targetsid'], $activeCampaings[0]['campaing_type'], $activeCampaings[0]['filename'], $queue['queue_id']));
        }
    }    
}

// Main procedure --------------------------------------------------------------
while (true) {
    cleanQueue();
    populateQueue();
    sleep(300);
}
?>
