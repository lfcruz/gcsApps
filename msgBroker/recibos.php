<?php
$struct = json_decode(file_get_contents('./recibos.json'),true);
foreach ($struct as $reg) {
    if($reg["auth"] == $_GET["auth"]){
        $authCode = $reg["auth"];
        $fechahora = $reg["fechahora"];
        $monto = $reg["amount"];
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
	<style>
		head{max-width: 300px; height: 100px; }
		body{max-width: 300px; height: 800px;  margin-left: 8px; text-align: left; font-size: 10px;}
		footer{max-width: 300px; height: 400px; font-size: 10px;}
		table,td{max-width: 300px;}
		img{max-width: 300px;}
		.headText{text-align: center; font-size: 14px;}
		.titleText{text-align: center; font-size: 12px;}
		.infoText{text-align: center; font-size: 11px;}
	</style>
	<div>
        	<img src="PopularLogo.bmp" align="middle">
	</div>
	<div class="infoText">   
        <p>
        <b>Consorcio Mercaderes <br>Mercado Modelo <br>0001 - modelo01</b>
        </p>
        </div>

    </head>
    <body>
	<div>
        <p class="headText">
	<b>RETIRO DESDE CUENTA</b>
	</p>
	</div>
        <div class="titleText">
        DATOS DEL CLIENTE
	</div>
        <p><br><b>Cedula :</b> _______________________________<br><br>
        <div class="titleText">
        DATOS TRANSACCION
        </div><br>
        <b>Cajero:</b> 0001<br><br>
        <b>Fecha/Hora:</b> <?php echo $fechahora; ?><br><br>
        <b>Banco:</b> Banco Popular Dominicano<br><br>
        <b>No. Aut. Bco.:</b> <?php echo $authCode; ?><br><br>
        <b>No. Cuenta:</b> 789593670<br><br>
        <b>No. Tarjeta DB:</b> ************7131<br><br>
        <b>Monto:</b> RD$<?php echo $monto; ?><br><br>
	</p><br><br>
        <body>
	<footer>
	<div>
	<table align="left">
	<tr>
	<th>___________________________</th>
	<th></th>
	<th>___________________________</th>
	</tr>
	<tr>
	<td>Firma del Cliente</td>
	<td></td>
	<td>Firma Representante</td>
	</tr>
	<tr>
	<td></td>
	<td></td>
	<td> SubAgente</td>
	</tr>
	</table>
	</div>
	<p align="center">
	<br><br><br><br><br><br><br>COPIA DEL CLIENTE
	</p>
    </footer>
</html>

