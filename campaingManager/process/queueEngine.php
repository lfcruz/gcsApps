        <?php
        include '../addedFunctions.php';
        include '../messagesFunctions.php';
        $validQueue = pgQResult("select * from t_queue where queue_id = $1", array($argv[1]));
        var_dump($validQueue);
                if(!empty($validQueue)){
                    while(true){
                        $targetsDS = pgQResult("select * from v_processingtargets where campaingid = $1 and targetsid = $2", array($validQueue[0]['campaing_id'], $validQueue[0]['targets_id']));
                        if(!empty($targetsDS)){
                            foreach ($targetsDS as $qRecord){
				echo "\n\n\n";
				var_dump($qRecord);
                                $result = sentMessage(trim($qRecord['target']), trim($qRecord['telcoid']), $qRecord['message'], $validQueue[0]['campaing_type_id']);
                                if($result){
                                    pgQResult("update t_targets_details set status = 'F' where targets_id = $1 and target = $2 and status = 'P'", array($qRecord['targetsid'], $qRecord['target']));
                                } else {
                                    $qRecord['retries'] = $qRecord['retries'] + 1;
                                    pgQResult("update t_targets_details set retries = $1 where targets_id = $2 and target = $3 and status = 'P'", array($qRecord['retries'], $qRecord['targetsid'], $qRecord['target']));
                                }
                            } 
                        }
                        sleep(10);
                        $validQueue = pgQResult("select * from t_queue where queue_id = $1", array($argv[1]));
                    }
                    
                } else {
                }
         
        ?>
