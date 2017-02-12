<?php
 $mwHeader = array('Accept: application/json',
                   'Content-Type: application/json',
                   'UserId: demo',
                   'Authentication: X253G4TRJYS4DSO12ZRV');

 
 $mwStructure = array ("idType" => "",
                          "id" => "",
                          "bankId" => "",
                          "firstName" => "FULANITO",
                          "middleName" => "DE",
                          "lastName" => "TAL",
                          "secondLastName" => "PEREZ",
                          "address1" => "Calle Guarocuya #22",
                          "address2" => "Ens. La Fe",
                          "city" => "Santo Domingo",
                          "state" => "DN",
                          "country" => "DO",
                          "telephone" => "809-555-5555",
                          "gender" => "M",
                          "active" => "true",
                          "origin" => array ("id" => "356232",
                                             "name" => "Super Colmado Pololo",
                                             "city" => "Santo Domingo",
                                             "country" => "DO")
                         );


// Send Request ----------------------------------------------------------------
function do_post_request($url, $data, $optional_headers = null,$requestType)
{
  $urlResponse = null;
  $urlParams = array(CURLOPT_CUSTOMREQUEST => $requestType,
                     CURLOPT_FRESH_CONNECT => true,
                     CURLOPT_FORBID_REUSE => true,
                     CURLOPT_HEADER => false,
                     CURLOPT_RETURNTRANSFER => true,
                     CURLOPT_HTTPHEADER => $optional_headers
                    );
  $urlResource = curl_init($url);
  if (!$urlResource) {
    echo("Problem stablishing resource.\n");
  } 
  else {
      curl_setopt_array($urlResource, $urlParams);
      $urlResponse = curl_exec($urlResource);
    if ($urlResponse === null) {
        echo("Problem reading data from URL.\n");
    }
    curl_close($urlResource);
  }
  return $urlResponse;
}

$result = do_post_request('http://172.19.1.19:6080/cardholder/CSV/1234567892',null,$mwHeader,'GET');
echo("\n ================= \n$result\n");
$jsonString = json_decode($result,true);
if(array_key_exists('error', $jsonString)){
    echo ("Esto fue un error\n");
}
else {
    echo ("Esto fue una loquera\n");
}
?>
