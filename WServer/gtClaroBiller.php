<?php
//name="EBanking" targetNamespace="http://webservices-ext.orange.com.do"

class SOAPServerClass {
    public function getContractInfo() {
        $xmlResponse = [
            "getContractInfoResponse" => [
                "return1" => "0",
                "return2" => "R",
                "return3" => "ORANGE"
            ]
        ];
        var_dump($xmlResponse);
        return $xmlResponse;
    }
}

$server = new SoapServer("orangeBiller.wsdl");
$server->setClass('SOAPServerClass');
$server->handle();
exit(1);
?>