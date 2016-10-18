<?php
include_once 'dbClass.php';
include_once 'configClass.php';
$conf = new configLoader('../config/dbProperties.json');
$makoConnector = new dbRequest($conf->structure['mako']['dbType'],
                                   $conf->structure['mako']['dbIP'],
                                   $conf->structure['mako']['dbPort'],
                                   $conf->structure['mako']['dbName'],
                                   $conf->structure['mako']['dbUser'],
                                   $conf->structure['mako']['dbPassword']);
$query = "select * from dktterminalrelation a "
        ."where a.terminalid not in (select terminalid from terminal) "
        ."and (a.terminalid like '".$_POST['search']."%' "
             ."or a.name like '%".$_POST['search']."%')"
        . "order by terminalid ";
$makoConnector->setQuery($query, Array());
$output = '';
$postData = '';
$result = $makoConnector->execQry();
if($result) {
    $output .= '<h4 align="center">Search Result</h4>';  
    $output .= '<div class="table-responsive">  
                    <table class="table table bordered">  
                    <tr>  
                        <th>Terminal ID</th>
                        <th>SAB Name</th>  
                        <th>Address</th>  
                        <th>City</th>  
                        <th>Action</th>    
                    </tr>';
    foreach ($result as $vReg) {
        $postData = '"'.$vReg['terminalid'].'", "'.$vReg['agencyid'].'", "'.$vReg['name'].'", "'.$vReg['street'].'", "'.$vReg['city'].'", "'.$vReg['region'].'", "'.$vReg['country'].'"';
        $output .= "<tr>  
                        <td>".$vReg["terminalid"]."</td>  
                        <td>".$vReg["name"]."</td>  
                        <td>".$vReg["street"]."</td>  
                        <td>".$vReg["city"]."</td>  
                        <td width='5%'><button type='button' id='".$vReg['terminalid']."' class='btn btn-info btnViewTargets' onclick='buttonClicked(".$postData.")'>\n<span class='glyphicon glyphicon-time'></span></button></td>  
                    </tr>";  
    }  
    echo $output;  
}else {
    echo 'Data Not Found';  
}  
?>
        