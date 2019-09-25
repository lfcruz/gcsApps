<?php
include_once 'lib/movistarGTISOPackager.php';
include_once 'lib/packagerClass.php';
include_once 'lib/socketServer.php';

$isoServer = new socketProcessor("0.0.0.0", 9000, "S");

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
    $isoRequest =  $isoServer->receiveMessage();
    $isoPackager = new isoPackager($isoRequest);
    $isoServer->returnMessage($isoPackager->process());
    
    
    //$isoResponse = isoPackaged($isoRequest);
    //$isoServer->returnMessage($isoResponse);
}while(true);
