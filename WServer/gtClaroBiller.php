<?php
//name="EBanking" targetNamespace="http://webservices-ext.orange.com.do"
class SOAPServerClass {
    public function getContractInfo(){
        $xmlResponse = Array("getContractInfoResponse" => Array("return" => "0","return" => "R","return" => "ORANGE"));
        var_dump($)
        return $xmlResponse;
    }

}

<xs:complexType name="contractInfoMsg">
<xs:sequence>
<xs:element minOccurs="0" name="customerType" type="xs:string" />
<xs:element name="postpaid" type="xs:boolean" />
<xs:element name="prepaid" type="xs:boolean" />
</xs:sequence>
</xs:complexType>



$server = new SoapServer("orangeBiller.wsdl");
$server->setClass('SOAPServerClass');
$server->handle();
exit(1);
?>