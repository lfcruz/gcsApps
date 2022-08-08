<?php
include_once 'lib/smppClientClass.php';
$smsc =  new smppClient('localhost', '5016', 'mpayment', 'mpayment', 'Logica', 5, 1, 'tPago', 1, 1);
$smsc->smppBind();
$smsc->sendSM('18094380771', "Habìa una vez un hombre Todas las mañanas salía");
$smsc->smppUnbind();


