<?php

class BR960Class {
    public function ISConsulta(){
        $xmlResponse = Array("ISConsultaResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
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

    public function ValidaTarjetaCodigo(){
        $xmlResponse = Array("ValidaTarjetaCodigoResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Valido" => "true"));
            return $xmlResponse;
    }

    public function DetalleCuentas(){
        $xmlResponse = Array("DetalleCuentasResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Producto" => Array("NumeroProducto" => "200012402087571",
                                                 "TipoProducto" => "CuentaAhorro",
                                                 "Moneda" => "DOP"),
                             "BalanceTotal" => "134899.40", "BalanceTotalAyer" => "25000.00", "BalanceEfectivo" => "80328.33",
                             "BalanceDisponible" => "80328.33", "ChequesHoy" => "0.00", "InteresAcumulado" => "0.00",
                             "InteresPeriodoAnterior" => "0.00", "Embargos" => "0.00", "Oficial" => "NA",
                             "FechaApertura" => "2011-09-02T00:00:00", "FechaUltActividad" => "2013-03-06T00:00:00",
                             "BalanceTransito" => "0.00", "Nombre" => "SALTITOPA FERNANDEZ, JUANA MARIA", "UltimoDeposito" => "120000.00",
                             "BalUltimoEstado" => "0.00", "MontoBloqueado" => "0.00", "MontoReversado" => "0.00",
                             "MontoAutorizado" => "0.00", "MontoFianzas" => "0.00", "Tasa" => "0.00", "ComisionCierre" => "0.00",
                             "ComisionRetirosMensuales" => "0.00", "ComisionRetiroAnticipado" => "0.00", "Estado" => "ACTIVA"));
            return $xmlResponse;
    }

    public function DetalleTarjeta(){
        $xmlResponse = Array("DetalleTarjetaResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Producto" => Array("NumeroProducto" => "4899510008047114",
                                                 "TipoProducto" => "TarjetaCredito",
                                                 "Moneda" => "DOP"),
                             "RsAcumuladas" => "5183",
                             "Detalle" => Array("DetalleTarjeta" => Array("Moneda" => "USD",
                                                                          "Limite" => "3000.00",
                                                                          "BalanceCorte" => "1500.00",
                                                                          "TotalAutorizaciones" => "3000.00",
                                                                          "BalanceUltEstado" => "800.00",
                                                                          "CuotasVencidas" => "0",
                                                                          "MontoVencido" => "0.00",
                                                                          "PagoMinimo" => "150.00",
                                                                          "DisponibleConsumo" => "1000.00",
                                                                          "FechaUltActividad" => "2015-02-16T00:00:00",
                                                                          "FechaUltPago" => "2015-02-16T00:00:00",
                                                                          "MontoUltPago" => "800.00",
                                                                          "LimiteCliente" => "3000.00",
                                                                          "BalancealaFecha" => "2000.00",
                                                                          "Tasa" => "2"),
                                                "DetalleTarjeta" => Array("Moneda" => "DOP",
                                                                          "Limite" => "40000.00",
                                                                          "BalanceCorte" => "15000.00",
                                                                          "TotalAutorizaciones" => "40000.00",
                                                                          "BalanceUltEstado" => "25433.00",
                                                                          "CuotasVencidas" => "0",
                                                                          "MontoVencido" => "0.00",
                                                                          "PagoMinimo" => "845.00",
                                                                          "DisponibleConsumo" => "15000.00",
                                                                          "FechaUltActividad" => "2015-02-16T00:00:00",
                                                                          "FechaUltPago" => "2015-02-16T00:00:00",
                                                                          "MontoUltPago" => "25433.00",
                                                                          "LimiteCliente" => "40000.00",
                                                                          "BalancealaFecha" => "25000.00",
                                                                          "Tasa" => "2")),
                             "FechaCorte" => "1957-01-01T00:00:00", "PageAntesDe" => "1957-01-01T00:00:00",
                             "MontoUltimoPago" => "25433.00", "Estado" => "Activo",
                             "LimiteCredimas" => "0.00", "CapitaPendienteCredimas" => "0.00",
                             "DisponibleCredimas" => "63000.00", "PagoPendienteCredimas" => "0.00"));
            return $xmlResponse;
    }

    public function ISPago(){
        $xmlResponse = Array("ISPagoResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Version" => "1"));
            return $xmlResponse;
    }

    public function PagoTarjeta(){
        $xmlResponse = Array("PagoTarjetaResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Version" => "1"));
            return $xmlResponse;
    }

    public function AvanceEfectivo(){
        $xmlResponse = Array("AvanceEfectivoResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Version" => "1"));
            return $xmlResponse;
    }

    public function PagoPrestamo(){
        $xmlResponse = Array("PagoPrestamoResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Version" => "1"));
            return $xmlResponse;
    }

    public function Transferencia(){
        $xmlResponse = Array("TransferenciaResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Version" => "1"));
            return $xmlResponse;
    }

    public function TransaccionACH(){
        $xmlResponse = Array("TransaccionACHResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Version" => "1"));
            return $xmlResponse;
    }

    public function TransferenciaRs(){
        $xmlResponse = Array("TransferenciaRsResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Version" => "1"));
            return $xmlResponse;
    }

    public function RedencionRs(){
        $xmlResponse = Array("RedencionRsResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Version" => "1"));
            return $xmlResponse;
    }

    public function ConsolidacionRs(){
        $xmlResponse = Array("ConsolidacionRsResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Version" => "1"));
            return $xmlResponse;
    }

    public function ConsultaBancosCorresponsales(){
        $xmlResponse = Array("ConsultaBancosCorresponsalesResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "BancosCorresponsales" => Array("BancoCorresponsal" => Array("NombreBanco" => "Banco Popular Dominicano",
                                                                                          "CodigoBancoCorresponsal" => "BPDODOS0XXX",
                                                                                          "Moneda" => "DOP",
                                                                                          "CodigoACH" => "10101070"),
                                                             "BancoCorresponsal" => Array("NombreBanco" => "Banco del Progreso Dominicano",
                                                                                          "CodigoBancoCorresponsal" => "BDPODOS0XXX",
                                                                                          "Moneda" => "DOP",
                                                                                          "CodigoACH" => "10101071"),
                                                             "BancoCorresponsal" => Array("NombreBanco" => "Banco BHD",
                                                                                          "CodigoBancoCorresponsal" => "BHDODOS0XXX",
                                                                                          "Moneda" => "DOP",
                                                                                          "CodigoACH" => "10101072"),
                                                             "BancoCorresponsal" => Array("NombreBanco" => "Banesco",
                                                                                          "CodigoBancoCorresponsal" => "BSCODOS0XXX",
                                                                                          "Moneda" => "DOP",
                                                                                          "CodigoACH" => "10101073"))));
            return $xmlResponse;
    }

    public function ConsultaBalanceRs(){
        $xmlResponse = Array("ConsultaBalanceRsResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Acumuladas" => "13000",
                             "Ganadas" => "0",
                             "Redimidas" => "0"));
            return $xmlResponse;
    }

    public function ConsultaBeneficiarios(){
        $xmlResponse = Array("ConsultaBeneficiariosResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Version" => "1",
                             "Beneficiarios" => Array("Beneficiario" => Array("Codigo" => "227",
                                                                              "Nombre" => "DE DIOS, MANUEL BENDECIDO",
                                                                              "Producto" => Array("NumeroProducto" => "100016200002480",
                                                                                                  "TipoProducto" => "CuentaCorriente",
                                                                                                  "Moneda" => "DOP"),
                                                                              "NombreCuenta" => "Manuel-CC"),
                                                      "Beneficiario" => Array("Codigo" => "228",
                                                                              "Nombre" => "ALIAS BOMBITA, PEDROLO JALON",
                                                                              "Producto" => Array("NumeroProducto" => "200016200002480",
                                                                                                  "TipoProducto" => "CuentaAhorro",
                                                                                                  "Moneda" => "DOP"),
                                                                              "NombreCuenta" => "Bombita-AH"))));
            return $xmlResponse;
    }

    public function CreaBeneficiario(){
        $xmlResponse = Array("CreaBeneficiarioResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Version" => "1"));
            return $xmlResponse;
    }

    public function RsCliente(){
        $xmlResponse = Array("RsClienteRsResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "ConsolidadoRs" => Array("Producto" => Array("NumeroProducto" => "200012402087571",
                                                                          "TipoProducto" => "CuentaAhorro",
                                                                          "Moneda" => "DOP"),
                                                      "Rs" => "144"),
                             "ConsolidadoRs" => Array("Producto" => Array("NumeroProducto" => "100012402087571",
                                                                          "TipoProducto" => "CuentaCorriente",
                                                                          "Moneda" => "DOP"),
                                                      "Rs" => "944"),
                             "ConsolidadoRs" => Array("Producto" => Array("NumeroProducto" => "4899510008047114",
                                                                          "TipoProducto" => "TarjetaCredito",
                                                                          "Moneda" => "DOP"),
                                                      "Rs" => "5344"),
                             "ConsolidadoRs" => Array("Producto" => Array("NumeroProducto" => "200012402087572",
                                                                          "TipoProducto" => "CuentaAhorro",
                                                                          "Moneda" => "DOP"),
                                                      "Rs" => "239")));
            return $xmlResponse;
    }

    public function ConsultaGeneralProducto(){
        $xmlResponse = Array("ConsultaGeneralProductoResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Nombre" => "PEREZ LAJARA, CANDIDA MASIEL",
                             "Estado" => "Activa",
                             "Identificacion" => Array("NumeroIdentificacion" => "00100899103",
                                                       "TipoIdentificacion" => "Cedula"),
                             "Moneda" => "DOP",
                             "FechaExpiracion" => "2018-12-12T22:34:33",
                             "TipoProducto" => "NA"));
            return $xmlResponse;
    }

    public function MovimientosCuenta(){
        $xmlResponse = Array("MovimientosCuentaResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "UltimaPagina" => "0",
                             "DetalleMovimientosCuenta" => Array("MovimientoCuenta" => Array("NroTransaccion" => "291088",
                                                                                             "Fecha" => "2015-03-23T00:00:00",
                                                                                             "Debito" => "2348.56",
                                                                                             "Credito" => "0.00",
                                                                                             "Concepto" => "Porque me dio la Gana",
                                                                                             "Balance" => "32.32",
                                                                                             "Moneda" => "DOP",
                                                                                             "Referencia" => "",
                                                                                             "Descripcion" => "",
                                                                                             "IdPaginacion" => "20150323205434490000291088000000000023",
                                                                                             "Oficina" => "220",
                                                                                             "FechaReal" => "2015-03-23T20:54:34",
                                                                                             "Causal" => "2054"),
                                                                 "MovimientoCuenta" => Array("NroTransaccion" => "291089",
                                                                                             "Fecha" => "2015-03-23T00:00:00",
                                                                                             "Debito" => "0.00",
                                                                                             "Credito" => "3466.33",
                                                                                             "Concepto" => "Otra vez....",
                                                                                             "Balance" => "22.32",
                                                                                             "Moneda" => "DOP",
                                                                                             "Referencia" => "",
                                                                                             "Descripcion" => "",
                                                                                             "IdPaginacion" => "20150323205434490000291088000000000024",
                                                                                             "Oficina" => "221",
                                                                                             "FechaReal" => "2015-03-23T20:54:34",
                                                                                             "Causal" => "2055"),
                                                                  "MovimientoCuenta" => Array("NroTransaccion" => "291090",
                                                                                             "Fecha" => "2015-03-23T00:00:00",
                                                                                             "Debito" => "348.56",
                                                                                             "Credito" => "0.00",
                                                                                             "Concepto" => "y ya!!!!",
                                                                                             "Balance" => "12.32",
                                                                                             "Moneda" => "DOP",
                                                                                             "Referencia" => "",
                                                                                             "Descripcion" => "",
                                                                                             "IdPaginacion" => "20150323205434490000291088000000000025",
                                                                                             "Oficina" => "222",
                                                                                             "FechaReal" => "2015-03-23T20:54:34",
                                                                                             "Causal" => "2056"))));
            return $xmlResponse;
    }

    public function DetallePrestamo(){
        $xmlResponse = Array("DetallePrestamoResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Producto" => Array("NumeroProducto" => "699011650001662",
                                                 "TipoProducto" => "Prestamo",
                                                 "Moneda" => "DOP"),
                              "MontoAprobado" => "320000.00",
                              "MontoDesembolsado" => "320000.00",
                              "FechaDesembolso" => "2013-07-16T00:00:00",
                              "FechaUltPago" => "2015-04-17T00:00:00",
                              "TotalAdeudado" => "159712.82",
                              "CapitalAdeudado" => "154212.88",
                              "CuentaDebitar" => Array("NumeroProducto" => "100011650001662",
                                                       "TipoProducto" => "CuentaCorriente",
                                                       "Moneda" => "DOP"),
                              "InteresesGenerados" => "5217.18",
                              "Mora" => "0.00",
                              "OtrosAdeudados" => "0.00",
                              "FechaPago" => "2015-05-18T00:00:00",
                              "CuotaRegular" => "11781.33",
                              "Cuotas" => "36",
                              "Plazo" => "0",
                              "Estado" => "Vencido",
                              "Facturas" => "True",
                              "Oficial" => "PEREZ Y PEREZ, MANUEL DE JESUS",
                              "DiaPago" => "26",
                              "Tasa" => "21"));
            return $xmlResponse;
    }

    public function MovimientosTarjeta(){
        $xmlResponse = Array("MovimientosTarjetaResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "UltimaPagina" => "0",
                             "DetalleMovimientosTarjeta" => Array("MovimientoTarjeta" => Array("NroTransaccion" => "291088",
                                                                                             "Fecha" => "2015-03-23T00:00:00",
                                                                                             "Debito" => "2348.56",
                                                                                             "Credito" => "0.00",
                                                                                             "Concepto" => "Porque me dio la Gana",
                                                                                             "Balance" => "32.32",
                                                                                             "Moneda" => "DOP",
                                                                                             "Referencia" => "",
                                                                                             "Descripcion" => "",
                                                                                             "IdPaginacion" => "20150323205434490000291088000000000023",
                                                                                             "Oficina" => "220",
                                                                                             "FechaReal" => "2015-03-23T20:54:34",
                                                                                             "Causal" => "2054"),
                                                                 "MovimientoTarjeta" => Array("NroTransaccion" => "291089",
                                                                                             "Fecha" => "2015-03-23T00:00:00",
                                                                                             "Debito" => "0.00",
                                                                                             "Credito" => "3466.33",
                                                                                             "Concepto" => "Otra vez....",
                                                                                             "Balance" => "22.32",
                                                                                             "Moneda" => "DOP",
                                                                                             "Referencia" => "",
                                                                                             "Descripcion" => "",
                                                                                             "IdPaginacion" => "20150323205434490000291088000000000024",
                                                                                             "Oficina" => "221",
                                                                                             "FechaReal" => "2015-03-23T20:54:34",
                                                                                             "Causal" => "2055"),
                                                                  "MovimientoTarjeta" => Array("NroTransaccion" => "291090",
                                                                                             "Fecha" => "2015-03-23T00:00:00",
                                                                                             "Debito" => "348.56",
                                                                                             "Credito" => "0.00",
                                                                                             "Concepto" => "y ya!!!!",
                                                                                             "Balance" => "12.32",
                                                                                             "Moneda" => "DOP",
                                                                                             "Referencia" => "",
                                                                                             "Descripcion" => "",
                                                                                             "IdPaginacion" => "20150323205434490000291088000000000025",
                                                                                             "Oficina" => "222",
                                                                                             "FechaReal" => "2015-03-23T20:54:34",
                                                                                             "Causal" => "2056"))));
            return $xmlResponse;
    }

    public function MovimientosPrestamo(){
        $xmlResponse = Array("MovimientosPrestamoResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "UltimaPagina" => "0",
                             "DetalleMovimientosPrestamo" => Array("MovimientoPrestamo" => Array("NroTransaccion" => "291088",
                                                                                             "Fecha" => "2015-03-23T00:00:00",
                                                                                             "Debito" => "2348.56",
                                                                                             "Credito" => "0.00",
                                                                                             "Concepto" => "Porque me dio la Gana",
                                                                                             "Balance" => "32.32",
                                                                                             "Moneda" => "DOP",
                                                                                             "Referencia" => "",
                                                                                             "Descripcion" => "",
                                                                                             "IdPaginacion" => "20150323205434490000291088000000000023",
                                                                                             "Oficina" => "220",
                                                                                             "FechaReal" => "2015-03-23T20:54:34",
                                                                                             "Causal" => "2054"),
                                                                 "MovimientoPrestamo" => Array("NroTransaccion" => "291089",
                                                                                             "Fecha" => "2015-03-23T00:00:00",
                                                                                             "Debito" => "0.00",
                                                                                             "Credito" => "3466.33",
                                                                                             "Concepto" => "Otra vez....",
                                                                                             "Balance" => "22.32",
                                                                                             "Moneda" => "DOP",
                                                                                             "Referencia" => "",
                                                                                             "Descripcion" => "",
                                                                                             "IdPaginacion" => "20150323205434490000291088000000000024",
                                                                                             "Oficina" => "221",
                                                                                             "FechaReal" => "2015-03-23T20:54:34",
                                                                                             "Causal" => "2055"),
                                                                  "MovimientoPrestamo" => Array("NroTransaccion" => "291090",
                                                                                             "Fecha" => "2015-03-23T00:00:00",
                                                                                             "Debito" => "348.56",
                                                                                             "Credito" => "0.00",
                                                                                             "Concepto" => "y ya!!!!",
                                                                                             "Balance" => "12.32",
                                                                                             "Moneda" => "DOP",
                                                                                             "Referencia" => "",
                                                                                             "Descripcion" => "",
                                                                                             "IdPaginacion" => "20150323205434490000291088000000000025",
                                                                                             "Oficina" => "222",
                                                                                             "FechaReal" => "2015-03-23T20:54:34",
                                                                                             "Causal" => "2056"))));
            return $xmlResponse;
    }

    public function PosicionConsolidada(){
        $xmlResponse = Array("PosicionConsolidadaResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Cuentas" => Array("ConsolidadoCuenta" => Array("Producto" => Array("NumeroProducto" => "200012402087571",
                                                                                                 "TipoProducto" => "CuentaAhorro",
                                                                                                 "Moneda" => "DOP"),
                                                                             "Balance" => "141.96",
                                                                             "Disponible" => "23883.99",
                                                                             "MontoAutorizado" => "0.00",
                                                                             "PuntosGanados" => "0",
                                                                             "PuntosAcumulados" => "0",
                                                                             "Estado" => "ACTIVA"),
                                                "ConsolidadoCuenta" => Array("Producto" => Array("NumeroProducto" => "100012402087571",
                                                                                                 "TipoProducto" => "CuentaCorriente",
                                                                                                 "Moneda" => "DOP"),
                                                                             "Balance" => "141.96",
                                                                             "Disponible" => "13883.99",
                                                                             "MontoAutorizado" => "0.00",
                                                                             "PuntosGanados" => "0",
                                                                             "PuntosAcumulados" => "0",
                                                                             "Estado" => "ACTIVA")),
                             "Tarjetas" => Array("ConsolidadoTarjeta" => Array("Producto" => Array("NumeroProducto" => "4899510008047114",
                                                                                                 "TipoProducto" => "TarjetaCredito",
                                                                                                 "Moneda" => "DOP"),
                                                                             "BalanceRD" => "26170.51",
                                                                             "DisponibleRD" => "36829.49",
                                                                             "BalanceUS" => "234.98",
                                                                             "DisponibleUS" => "1000.00",
                                                                             "VencimientoPago" => "1957-01-01T00:00:00",
                                                                             "PendienteActivacion" => "false",
                                                                             "InteresALaFecha" => "0.00",
                                                                             "InteresALaFechaUS" => "0.00",
                                                                             "BalanceCorte" => "34576.87",
                                                                             "PuntosGanados" => "0",
                                                                             "PuntosAcumulados" => "0",
                                                                             "UltimaActividad" => "2015-06-01T00:00:00",
                                                                             "Estado" => "ACTIVA")),
                             "Prestamos" => Array("ConsolidadoPrestamo" => Array("Producto" => Array("NumeroProducto" => "699011650001662",
                                                                                                 "TipoProducto" => "Prestamo",
                                                                                                 "Moneda" => "DOP"),
                                                                             "DeudaActual" => "159712.82",
                                                                             "DeudaVencida" => "10.46",
                                                                             "FechaProximoPago" => "2015-05-18T00:00:00",
                                                                             "InteresALaFecha" => "2944.98",
                                                                             "MontoCuotas" => "3456.78",
                                                                             "CuotasPendientes" => "1",
                                                                             "Tasa" => "21",
                                                                             "Estado" => "ACTIVA"))));
            return $xmlResponse;
    }

    public function RemoverBeneficiario() {
        $xmlResponse = Array("RemoverBeneficiarioResult" => Array("Canal" => "GCS960","Terminal" => "0.0.0.0","Usuario" => "GCSTest",
                             "FechaHora" => "2015-10-28","TRN_ID" => "26551362","Resultado" => "0","Mensaje" => "000 - TRANSACCION PROCESADA",
                             "Ok" => "0",
                             "Descripcion" => "Beneficiario Eliminado"));
            return $xmlResponse;
    }

}


$server = new SoapServer("GCS960.wsdl");
$server->setClass('BR960Class');
$server->handle();
?>
