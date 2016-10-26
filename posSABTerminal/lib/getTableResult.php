<?php
include_once 'dbClass.php';
include_once 'configClass.php';
$searchPartern = $_POST['search'];
$isActiveList = $_POST['list'];
$searchList = "''";
$conf = new configLoader('../config/dbProperties.json');
$makoConnector = new dbRequest($conf->structure['mako']['dbType'],
                                   $conf->structure['mako']['dbIP'],
                                   $conf->structure['mako']['dbPort'],
                                   $conf->structure['mako']['dbName'],
                                   $conf->structure['mako']['dbUser'],
                                   $conf->structure['mako']['dbPassword']);
if($isActiveList == 'true'){
    $query = "select a.merchantid as agencyid, b.terminalid as terminalid, a.ca_name as name, "
            ."a.ca_street as street, a.ca_city as city, a.ca_region as region, a.ca_country as country "
            ."from merchant a, terminal b, terminal_external_info c "
            ."where b.merchant = a.id and c.id = b.id ";
}else {
    $query = "select * from dktterminalrelation where terminalid not in (select terminalid from terminal) ";
}

if(strpos($searchPartern, ',') !== false){
    foreach(explode(',', $searchPartern) as $value){
        $searchList .= ", '".trim($value)."'";
    }
    $query .= "and terminalid in (".$searchList.") order by terminalid ";
}else {
    $query .= "and terminalid like '".$searchPartern."%' order by terminalid ";
}
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
                        <td>".$vReg["city"]."</td>";
        if($isActiveList == 'true'){
            $output .= "<td width='5%'><button type='button' id='".$vReg['terminalid']."' disabled class='btn btn-info btnViewTargets' onclick='buttonClicked(".$postData.")'>\n<span class='glyphicon glyphicon-export'></span></button></td></tr>";
        }else {
            $output .= "<td width='5%'><button type='button' id='".$vReg['terminalid']."' class='btn btn-info btnViewTargets' onclick='buttonClicked(".$postData.")'>\n<span class='glyphicon glyphicon-link'></span></button></td></tr>";
        }            
    }  
    echo $output;  
}else {
    echo 'Data Not Found';  
}  
?>
        