<?php

echo "Starting Scotiabank migration file spliter process............\n";
sleep(30);
$handler860 = fopen($argv[1], 'r');
$handler800 = fopen($argv[2], 'r');
$handerSpliter = null;
$data860 = null;
$data800 = null;
$recordsPerFile = $argv[3];
$fileParts = 0;

do{
    try {
        $handerSpliter = fopen('migrationFile-'.$fileParts, 'w');
        for($counter = 1 ; $counter <= $recordsPerFile ; $counter++){
            $running = ($data860 = fgets($handler860)) ? true : false;
            $data800 = fgets($handler800);
            fputs($handerSpliter, $data860.$data800);
            if(($counter % 2) > 0 ){
                echo "\b|";
            }else{
                echo "\b--";
            }
        }
        fclose($handerSpliter);
        unset($handerSpliter);
        echo "\bFile migrationFile-".$fileParts." was generated.\n";
        $fileParts++;  
    }catch(Exception $e) {
        echo "\n----------------------------------------------------\n";
        echo "Error Code: ".$e->getCode()."\n";
        echo "On: ".$e->getFile()." - [".$e->getLine()."]\n";
        echo "Error Message: ".$e->getMessage()."\n";
        echo "Stack Trace: ".$e->getTraceAsString()."\n";
        echo "----------------------------------------------------\n\n\n";
        exit(1);
    }
}while($running);
fclose($handler860);
fclose($handler800);
