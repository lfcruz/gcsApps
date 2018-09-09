<?php
include_once 'lib/configClass.php';
include_once 'lib/LogClass.php';
include_once 'lib/sftpEndPoint.php';

$taskid = $argv[1];

try{
    $conf = new configLoader('conf/transferTask.json');
    $task = $conf->structure[$taskid];
} catch (Exception $e) {
    echo "Exception : ".$e->getMessage();
    exit($e->getCode());
}
if(empty($task)) { echo "Invaild taskID.\n"; exit(1);}
unset($conf);

try{
    $conf = new configLoader('conf/endPoints.json');
    $srcEndPoint = $conf->structure[$task['sourceEndPoint']];
    $dstEndPoint = $conf->structure[$task['targetEndPoint']];
} catch (Exception $e) {
    echo "Exception : ".$e->getMessage();
    exit($e->getCode());
}
if(empty($srcEndPoint) or empty($dstEndPoint)) { echo "Invaild endPointID.\n"; exit(1);}
unset($conf);

try {
    $source = new sftpEndPoint($srcEndPoint['host'],$srcEndPoint['username'],$srcEndPoint['password']);
    $target = new sftpEndPoint($dstEndPoint['host'],$dstEndPoint['username'],$dstEndPoint['password']);
    
    $source->directory = $task['sourcePath'];
    $source->filename = $task['sourceFilename'].date('Ymd');

    $target->directory = $task['targetPath'];
    $target->filename = $task['sourceFilename'].date('Ymd');
    
} catch (Exception $e) {
    echo "Exception : ".$e->getMessage();
    exit($e->getCode());
}

try {
    echo date("d-m-Y H:i:s")." - Reading file from source.....\n";
    $fileStream = $source->getFile();
    echo date("d-m-Y H:i:s")." - Writing file to target......\n";
    $target->putFile($fileStream);
} catch (Exception $e) {
    echo "Exception : ".$e->getMessage();
    exit($e->getCode());
}
echo date("d-m-Y H:i:s")." - Process Completed.\n\n";