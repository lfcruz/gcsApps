<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
define ('CONFIG_FILE','billers.json');
$hora = null;
// -----------------------------------------------------------------------------
getopt($hora);
$hora = '00';
if(!file_exists(CONFIG_FILE)){
   echo "Configuration file <".CONFIG_FILE."> don't exists.......";
   exit;
}
$fileString = file_get_contents('billers.json');
$billersDef = json_decode($fileString,true);
foreach ($billersDef as $billerParam) {
    $billerPath = $billerParam['path'];
    $billerFilePrefix = $billerParam['filePrefix'];
    $fileToLoad = $billerPath.$billerFilePrefix.date('Ymd')."-$hora"."00.xml";
    echo "$fileToLoad\n";
    if(file_exists($fileToLoad)){
        $billerCutStructure = simplexml_load_file($fileToLoad);
        var_dump($billerCutStructure);
    }
}
//}
?>
