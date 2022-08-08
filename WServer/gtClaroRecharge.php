<?php

class SOAPServerClass {
    public function getContractInfo(){
        $xmlResponse = Array("getContractInfoResponse" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "ISConsultas" => Array("Consulta" => Array("Monto" => Array("Moneda" => "DOP",
                                                                                         "Monto" => "2523.22"),
                                                                        "MontoAtraso" => Array("Moneda" => "DOP",
                                                                                               "Monto" => "523.22"),
                                                                        "MontoMinimo" => Array("Moneda" => "DOP",
                                                                                               "Monto" => "1523.22"),
                                                                        "Periodo" => "PRIMERO",
                                                                        "Nombre" => "EMPRESAS TIBURCIO",
                                                                        "Tipo" => "NA",
                                                                        "Descripcion" => "NA",
                                                                        "Identificacion" => Array("NumeroIdentificacion" => "RNC011002023",
                                                                        "TipoIdentificacion" => "Cedula"),
                                                                        "Referencia" => "00112875455",
                                                                        "TipoReferencia" => "NA",
                                                                        "FechaVencimiento" => "2015-10-28T11:33:25.745283",
                                                                        "FechaEmision" => "2015-10-28T11:33:25.745283",
                                                                        "Estado" => "ACTIVO"))));
            return $xmlResponse;
    }

}


$server = new SoapServer("orangeRecharge.wsdl");
$server->setClass('SOAPServerClass');
$server->handle();
exit(1);
?>