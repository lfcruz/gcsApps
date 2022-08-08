<?php
include_once 'lib/customerClass.php';
echo "Start File Loader time ===> ".date('Y-m-d H:i:s')."\n";
$csv = array_map('str_getcsv', file('codigos.csv'));
    array_walk($csv, function(&$a) use ($csv) {
      $a = array_combine($csv[0], $a);
    });
    array_shift($csv);
if(!empty($csv)){
    $processor = new enrollment();
    foreach ($csv as $record => $data){
        $recordResult = $processor->doActivationCodeValidation($data);
        erro_log($data['document_id'].",".$data['telephone_number'].",".$data['name'].",".$data['last_name'].",".$data['activation_code'].",".$recordResult."\n", 3, 'log/resutl.csv');
        echo($data['document_id'].",".$data['telephone_number'].",".$data['name'].",".$data['last_name'].",".$data['activation_code'].",".$recordResult."\n");
    }
}else {
    echo "File Empty.\n";
}
echo "End File Loader time ===> ".date('Y-m-d H:i:s')."\n";
