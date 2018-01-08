<?php
include_once '../lib/configClass.php';
include_once '../lib/dataLoaderClass.php';
$config = new configLoader('../config/billers_files.json');
var_dump($config->structure);

//$loader = new gdmLoader($argv[1]);
//$loader->process();
