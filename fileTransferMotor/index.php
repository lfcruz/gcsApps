<?php
include "./lib/sftpEndPoint.php";
try {
    $origen = new sftpEndPoint("172.19.1.115", "", "");
    $destino = new sftpEndPoint("ftpseguro.bpd.com.do", "GCS", "_nCTM0;n");
} catch (Exception $e) {
    echo "Exception : ".$e->getMessage();
    exit($e->getCode());
}
$origen->directory = "";
$origen->filename = "bigTest.txt";

$destino->directory = "";
$destino->filename = "test2.txt";

try {
    echo date("d-m-Y H:i:s")." - Reading file from source.....\n";
    $fileStream = $origen->getFile();
    echo date("d-m-Y H:i:s")." - Writing file to target......\n";
    $destino->putFile($fileStream);
} catch (Exception $e) {
    echo "Exception : ".$e->getMessage();
    exit($e->getCode());
}
echo date("d-m-Y H:i:s")." - Process Completed.\n\n";

?>