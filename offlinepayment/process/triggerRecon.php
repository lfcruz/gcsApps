<?php
include_once '../lib/reconLoaderClass.php';
$recon = new conciliationReport($argv[1]);
$recon->process();
