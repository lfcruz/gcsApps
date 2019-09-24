<?php
include_once 'lib/movistarGTISOPackager.php';
include_once 'lib/packagerClass.php';
include_once 'lib/socketServer.php';

/*
 Conuslta Balance : 00780320110926121530123456789012941-0000001100000005104629300000000000000000000
 Pago Servicio : 0155012010072313101400077635660652-00000120
0000000016.170
4486770058439854
0000000016.170000000000.000000000000.00
01007
 */
$isoServer = new socketProcessor("0.0.0.0", 9000, "S");
$incomingPackager = new gdmPackager('config/incoming_packager.json');
//$outgoingPackager = new gdmPackager('config/outgoing_packager.json');
$msgCodes = Array('01' => '51', '03' => '53');

function isoPackaged($packedData){
    $unpackedData = unpack('H*',$packedData);
    $message = $unpackedData[1];
    var_dump($message);
    $jack = new isoPack();
    $packResult = "";
    $jack->addMTI('0210');
    $jack->addData(2, substr($message,36,8));
    $jack->addData(3, substr($message,44,6));
    $jack->addData(4, (int) substr($message,50,12));
    $jack->addData(11, substr($message,62,6));
    $jack->addData(12, substr($message,68,6));
    $jack->addData(13, substr($message,74,4));
    $jack->addData(24, '119');
    //$jack->addData(24, (int) substr($message,78,4));
    $jack->addData(38, (string) rand(100000,99999));
    $jack->addData(39, '00');
    $jack->addData(41, pack('H*', substr($message,82,16)));
    $jack->addData(42, pack('H*', substr($message,98,30)));    
    $data = $jack->getData();
    var_dump($data);
    $packResult .= pack('H*', $jack->getMTI());
    $packResult .= pack('H*', $jack->getBitmap());
    $packResult .= pack('H*', $data[2]);
    $packResult .= pack('H*', $data[3]);
    $packResult .= pack('H*', $data[4]);
    $packResult .= pack('H*', $data[11]);
    $packResult .= pack('H*', $data[12]);
    $packResult .= pack('H*', $data[13]);
    $packResult .= pack('n*', $data[24]);
    $packResult .= $data[38];
    $packResult .= $data[39];
    $packResult .= $data[41];
    $packResult .= $data[42];
    $packResult = pack('H*', "6000000003").$packResult;
    $isoLength = strlen($packResult);
    $packResult = pack('n*', $isoLength).$packResult;
    unset($jack);
    var_dump(unpack('H*', $packResult));
    return $packResult;
}

function isoPrepare($message){
    $isoDOM = new DOMDocument;
    $isoDOM->loadXML($message);
    $isomsg = simplexml_import_dom($isoDOM);
    $isotag = 0;
    
    foreach($isomsg as $x){
        switch($isomsg->field[$isotag]['id']){
            case '0':
                $isomsg->field[$isotag]['value'] = '0210';
                break;
            default:
                break;
        }
        $isotag += 1;
    }
    $isomsg->field[$isotag]['id'] = '38';
    $isomsg->field[$isotag]['value'] = (string)rand(100000,999999);
    $isotag += 1;
    $isomsg->field[$isotag]['id'] = '39';
    $isomsg->field[$isotag]['value'] = '00';
    $isotag += 1;
    $isomsg->field[$isotag]['id'] = '62';
    $isomsg->field[$isotag]['value'] = (string)rand(1000000000,9999999999);
    $isotag += 1;
    return substr($isomsg->asXML(), 22, strlen($isomsg->asXML()));
}


function sockPrepare($message){
    global $msgCodes, $incomingPackager, $outgoingPackager;
    $msgLength = substr($message, 0, 4);
    $msgProcID = substr($message, 4, 2);
    $incomingPackager->setPackagerId($msgProcID);
    $incomingPackager->parseRecord($message);
    
    switch($msgProcID){
        case '01':
            $incomingPackager->recordStructured['referencia'] = (string)rand(1,999999999999);
            $incomingPackager->recordStructured['autorizacion'] = (string)rand(1,999999);
            $incomingPackager->recordStructured['saldo'] = (string)rand(50,100);
            $incomingPackager->recordStructured['nombre'] = 'JOHN';
            $incomingPackager->recordStructured['apellido'] = 'DOE';
            $incomingPackager->recordStructured['status'] = '00';
            $incomingPackager->recordStructured['descripcion'] = 'Pago Satisfactorio. TARAAAAANNN!!!!!';
            break;
        case '03':
            $incomingPackager->recordStructured['balance'] = (string)rand(50,100);
            $incomingPackager->recordStructured['nombre'] = 'JOHN';
            $incomingPackager->recordStructured['apellido'] = 'DOE';
            $incomingPackager->recordStructured['status'] = '00';
            $incomingPackager->recordStructured['descripcion'] = 'Consulta Satisfactoria. TARAAAAANNN!!!!!';
            break;
        default:
            break;
    }
    
    $incomingPackager->setPackagerId($msgCodes[$msgProcID]);
    $incomingPackager->createRecord($incomingPackager->recordStructured);
    
    return $incomingPackager->recordString;
}

do{
    $message =  $isoServer->receiveMessage();
    switch(substr($message,0,3)){
        case "<is":
            $prepareResponse = isoPrepare($message);
            break;
        case "007":
            $prepareResponse = sockPrepare($message);
            break;
        case "015":
            $prepareResponse = sockPrepare($message);
            break;
        default:
            $prepareResponse = isoPackaged($message);
            break;
    }
    $isoServer->returnMessage($prepareResponse);
}while(true);
