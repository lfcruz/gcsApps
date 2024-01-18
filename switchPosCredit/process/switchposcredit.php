<?php
include_once '../lib/configClass.php';
include_once '../lib/LogClass.php';
include_once '../lib/constants.php';
include_once '../lib/DakotaProcessor.php';
include_once '../lib/SwitchPosProcessor.php';

try {
     $threadid = $argv[1];
     $logger = new Logger();
     $config = new configLoader("../config/process_application.json");
     $switchporEngine = new SwitchposProcessor($threadid);
     $dktEngine = new DakotaProcessor($config->structure['dakota_host'], $config->structure['dakota_port']);
     $logger->writeLog(INFO_LOG, "Starting SwitchPosCredit instance [$threadid]........");
     $logger->writeLog(INFO_LOG, "[SwitchPosCredit-".$threadid."]: Bulk size: ".$config->structure['bulk_size']);
     $logger->writeLog(INFO_LOG, "[SwitchPosCredit-".$threadid."]: Credit Processor: tcp://".$config->structure['dakota_host'].":".$config->structure['dakota_port']);
     $timer = time();
     $jsonStructure = json_decode(XML_JSON_STRUCTURE, true);
} catch (Exception $ex) {

}

while ($config->structure['active']){
     if (time() > $timer+10){
          $config->reload();
          $logger->writeLog(INFO_LOG, "[SwitchPosCredit-".$threadid."]: Configuration Reloaded - Bulk size: ".$config->structure['bulk_size']);
          $timer = time();
     }
     try {
          $queue = json_decode($switchporEngine->getPendingNotifications($config->structure['bulk_size']), true);
          $logger->writeLog(INFO_LOG, "[SwitchPosCredit-$threadid]: New queue loaded for trhead: [".$threadid."]\n". json_encode($queue));
          foreach ($queue as $row){
               $jsonStructure['MESSAGE']['BANKID'] = $row['bankid'];
               $jsonStructure['MESSAGE']['PARTNERID'] = $row['mid'];
               $jsonStructure['MESSAGE']['AGENCYID'] = $row['agencyid'];
               $jsonStructure['MESSAGE']['TERMINALID'] = $row['tid'];
               $jsonStructure['MESSAGE']['SHIFTID'] = $row['shiftid'];
               $jsonStructure['MESSAGE']['USERNAME'] = $row['username'];
               $jsonStructure['MESSAGE']['CLIENT']['GCSSEQUENCE'] = $row['gcssequenceid'];
               $jsonStructure['MESSAGE']['TRANSACTION']['ACCOUNT'] = $row['account'];
               $jsonStructure['MESSAGE']['TRANSACTION']['AMOUNT'] = $row['amount'];
               $jsonStructure['MESSAGE']['TRANSACTION']['AUTH-CODE'] = $row['authcode'];
               $logger->writeLog(INFO_LOG, "[SwitchPosCredit-$threadid]: Sending Notification.");
               switch ($row['operationtype']) {
                    case 'W':
                         $response = $dktEngine->CashOutNotification(json_encode($jsonStructure));
                         break;
                    case 'R':
                         $response = $dktEngine->CashOutReversal(json_encode($jsonStructure));
                         break;
                    default:
                         break;
               }
               $jsonStructure = json_decode($response, true);
               $logger->writeLog(INFO_LOG, "[SwitchPosCredit-".$threadid."]: Notification response: [".$jsonStructure['MESSAGE']['TRANSACTION']['RESPONSECODE'][0]."]");
               if($jsonStructure['MESSAGE']['TRANSACTION']['RESPONSECODE'][0] == PROC_OK){                    
                    $response = $switchporEngine->setCompletedNotification($row['id'], $jsonStructure['MESSAGE']['TRANSACTION']['RESPONSECODE'][0], $jsonStructure['MESSAGE']['TRANSACTION']['RECEIPT-NUMBER'][0]);
               }else {
                    $response = $switchporEngine->setCompletedNotification($row['id'], $jsonStructure['MESSAGE']['TRANSACTION']['RESPONSECODE'][0], $jsonStructure['MESSAGE']['TRANSACTION']['RECEIPT-NUMBER'][0]);
               }
               $logger->writeLog(INFO_LOG, "[SwitchPosCredit-$threadid]: Notification completed: [$response]");
          }
          
     } catch (Exception $ex) {
          $logger->writeLog(ERROR_LOG, $ex->getMessage(), $ex->getTraceAsString());
     }
     //sleep(10);
}
$logger->writeLog(INFO_LOG, "Shutting down SwitchPosCredit instance [$threadid]........");
unset($threadid);
unset($logger);
unset($config);
unset($switchporEngine);
unset($dktEngine);
unset($jsonStructure);
unset($timer);
unset($queue);
unset($response);
unset($response);
