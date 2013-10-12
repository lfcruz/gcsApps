<?php
$option = $_GET[text];
switch ($option) {
    case '1': 
        echo "0Menu Pagos\nSeleccione una opcion:\n1.Factura\n2.Financieros\n";
        break;
    case '2': 
        echo "0Menu Consultas\nSeleccione una opcion:\n1.Cuentas\n2.Tarjetas\n3.Telefono\n";
        break;
    default: 
        echo "1Menu Principal tPago\nSeleccione una opcion:\n1.Pagos\n2.Consultas\n3.Transferencias\n4.Compras\n5.Recargas\n6.Retiros\n";
        break;
}
?>
