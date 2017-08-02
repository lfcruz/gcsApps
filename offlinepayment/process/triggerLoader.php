<?php
include_once '../lib/configClass.php';
include_once '../lib/dataLoaderClass.php';
$loader = new gdmLoader($argv[1]);
$loader->process();
