<?php
include_once 'lib/customerClass.php';
echo "Start File Loader time ===> ".date('Y-m-d H:i:s')."\n";
$csv = array_map('str_getcsv', file('bulkLoadAntiguaGT.csv'));
    array_walk($csv, function(&$a) use ($csv) {
      $a = array_combine($csv[0], $a);
    });
    array_shift($csv);
if(!empty($csv)){
    $processor = new enrollment();
    foreach ($csv as $record => $data){
        $recordResult = $processor->loadRecord($data);
        echo "./";
    }
}else {
    echo "File Empty.\n";
}
echo "End File Loader time ===> ".date('Y-m-d H:i:s')."\n";
echo "-------------------------------------------------------------------------\n";
echo "Start Enroll Process time ===> ".date('Y-m-d H:i:s')."\n";
var_dump($processor->doEnrollment());
echo "Start Enroll Process time ===> ".date('Y-m-d H:i:s')."\n";

