<?php
$option = $_GET[text];
switch ($option) {
    case '1': 
        echo "1Menu Pagos\nSeleccione una opcion:\n1.Factura\n2.Financieros\n";
        break;
    case '2': 
        echo "0Menu Consultas\nSeleccione una opcion:\n1.Cuentas\n2.Tarjetas\n3.Telefono\n";
        break;
    default: 
        echo "1Menu Principal tPago\nSeleccione una opcion:\n1.Pagos\n2.Consultas\n3.Transferencias\n4.Compras\n5.Recargas\n6.Retiros\n";
        break;
}
<HTML memory="menu_id=5">
<HEAD><TITLE>GCS Systems International</TITLE></HEAD>
<BODY>
<p>Bienvenidos a un mundo de servicios en pagos moviles.</p>
</BODY>
</HTML>



?>
