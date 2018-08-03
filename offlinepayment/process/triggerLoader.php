<?php
include_once '../lib/configClass.php';
include_once '../lib/dataLoaderClass.php';
$config = new configLoader('../config/billers_files.json');
$filename = "../".$config->structure[$argv[1]]['inputFileDirectory'].$argv[1]."_".date(Ymd).".txt";
$loader = new gdmLoader($filename);
var_dump($loader);
$loader->process();
