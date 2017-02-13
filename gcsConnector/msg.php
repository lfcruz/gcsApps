<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */







function msg515($input){
    //Mensaje Consulta Balance Vendedor Citi
    $tmp = simplexml_import_dom($input);
    //Format response message 
    $tmp["TYPE"]="516";
    $tmp->TRANSACTION["CURRENCY"]="DOP";
    $tmp->TRANSACTION["CURRENTBALANCE"]="2479.0000";
    $tmp->TRANSACTION["RESPONSECODE"]="0000";
    $bpsequence=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    $tmp->TRANSACTION["BPSEQUENCE"]=$bpsequence;
    return $tmp;    
    
}

function msg517($input){
    //Mensaje Consulta Historial Vendedor Citi
    $tmp = simplexml_import_dom($input);
    //Format response message 
    $tmp["TYPE"]="518";
    $tmp->TRANSACTION["CURRENCY"]="DOP";
    $tmp->TRANSACTION["CURRENTBALANCE"]="";
    $tmp->TRANSACTION["RESPONSECODE"]="0000";
    $bpsequence=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    $tmp->TRANSACTION["BPSEQUENCE"]=$bpsequence;
    return $tmp;    
    
}

function msg540($input){
    
    $tmp = simplexml_import_dom($input);
    //Format response message 
    $tmp["TYPE"]="545";
    switch ($tmp["BANKID"]) {
/*	case "BDI":
		$tmp->TRANSACTION["ACCOUNT"]="5424180279791732";
		$tmp->TRANSACTION["EXPDATE"]="04/16";
                #$tmp->TRANSACTION["ACCOUNT"]="4761340000000043";
                #$tmp->TRANSACTION["EXPDATE"]="12/17";
		break;
	case "ADO":
		$tmp->TRANSACTION["ACCOUNT"]="4761340000000043";
                $tmp->TRANSACTION["EXPDATE"]="12/17";
                break;
        case "BDP":
                $tmp->TRANSACTION["ACCOUNT"]="541950001998048";
                #$tmp->TRANSACTION["ACCOUNT"]="4509750047107304";
                $tmp->TRANSACTION["EXPDATE"]="08/14";
                #$tmp->TRANSACTION["ACCOUNT"]="377880301750014";
                #$tmp->TRANSACTION["EXPDATE"]="05/16";
                break; */
	default:
		// Tarjetas Ambiente pruebas FDR
    			//$tmp->TRANSACTION["ACCOUNT"]="5188920007758163";
    			//$tmp->TRANSACTION["ACCOUNT"]="4484600022222222";
			$tmp->TRANSACTION["ACCOUNT"]="4111111111111111";
    			//$tmp->TRANSACTION["ACCOUNT"]="5000506501237221";
			
			// Tarjeta Visa
    			//$tmp->TRANSACTION["ACCOUNT"]="4012000033330026";

			//Tarjeta Mastercard
    			//$tmp->TRANSACTION["ACCOUNT"]="5424180279791732";

			//Tarjeta AMEX
			//$tmp->TRANSACTION["ACCOUNT"]="377883875670642";
			//$tmp->TRANSACTION["ACCOUNT"]="377881913435648";

    			//$tmp->TRANSACTION["ACCOUNT"]="377880301750014";
    			//$tmp->TRANSACTION["ACCOUNT"]="6011000990099818";
    			//$tmp->TRANSACTION["ACCOUNT"]="4004410000000017";
    			//$tmp->TRANSACTION["ACCOUNT"]="4004830000000016";
    			//$tmp->TRANSACTION["ACCOUNT"]="5418480000000017";
    			//$tmp->TRANSACTION["ACCOUNT"]="5415990000000018";
    			//$tmp->TRANSACTION["ACCOUNT"]="4594130000000017";
    			//$tmp->TRANSACTION["ACCOUNT"]="5166910000000009";
    			//$tmp->TRANSACTION["ACCOUNT"]="5000506501515642";
    			//$tmp->TRANSACTION["EXPDATE"]="04/16";
			$tmp->TRANSACTION["EXPDATE"]="12/49";
			//$tmp->TRANSACTION["EXPDATE"]="07/20";
			//$tmp->TRANSACTION["EXPDATE"]="10/20";
		// Tarjeta Ambiente prueba Cardnet
    			//$tmp->TRANSACTION["ACCOUNT"]="4761340000000043";
    			//$tmp->TRANSACTION["EXPDATE"]="12/17";
    }
    $tmp->TRANSACTION["RESPONSECODE"]="0000";
    $bpsequence=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    $tmp->TRANSACTION["BPSEQUENCE"]=$bpsequence;
    return $tmp;    
    
}

function msg111($input){
    
    $tmp = simplexml_import_dom($input);
    //Format response message 
    $tmp["TYPE"]="112";
    ////Error condition on amount = 330 / return 9899 general error
        if($tmp->TRANSACTION["ACCOUNT"]=="749772323" and $tmp->TRANSACTION["TRANSACTIONTYPE"]=="03"){
                $tmp->TRANSACTION["RESPONSECODE"]="9850";
        }
        elseif($tmp->TRANSACTION["AMOUNT"]=="850.00"){
                $tmp->TRANSACTION["RESPONSECODE"]="9850";
        }elseif($tmp->TRANSACTION["AMOUNT"]=="150.00" and $tmp->TRANSACTION["SUBTRANSACTIONTYPE"]=="0350"){
		$tmp->TRANSACTION["RESPONSECODE"]="9899";
	}
        else {
                $tmp->TRANSACTION["RESPONSECODE"]="0000";
        }
    $bpsequence=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    $tmp->TRANSACTION["BPSEQUENCE"]=$bpsequence;
    return $tmp;    
    
}


function msg615($input){

    $tmp = simplexml_import_dom($input);
    //Format response message
    $tmp["TYPE"]="620";
    $tmp->TRANSACTION["ACCOUNT"]="*****5078";
    $tmp->TRANSACTION["TYPE"]="XCRE";
    $tmp->TRANSACTION["AVAILABLEAMOUNT"]="5500.00";
    $tmp->TRANSACTION["CURRENCY"]="DOP";
    $tmp->TRANSACTION["DESTINATIONACCOUNT"]="*****4048";
    $tmp->TRANSACTION["DESTINATIONACCOUNT-TYPE"]="SAV";
    $tmp->TRANSACTION["ALIAS-FROM"]="AS1";
    $tmp->TRANSACTION["ALIAS-TO"]="AHO2";
    $tmp->TRANSACTION["MAXAMOUNT"]="10000.00";
    $tmp->TRANSACTION["MINAMOUNT"]="100.00";
    $tmp->TRANSACTION["RESPONSECODE"]="0000";
    $bpsequence=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    $tmp->TRANSACTION["BPSEQUENCE"]=$bpsequence;
    return $tmp;

}


function msg625($input){

    $tmp = simplexml_import_dom($input);
    //Format response message
    $tmp["TYPE"]="630";
    $tmp->TRANSACTION["ACCOUNT"]="*****5078";
    $tmp->TRANSACTION["TYPE"]="ASUE";
    $tmp->TRANSACTION["AMOUNT"]="5500.00";
    $tmp->TRANSACTION["CURRENCY"]="DOP";
    $tmp->TRANSACTION["DESTINATIONACCOUNT"]="*****4048";
    $tmp->TRANSACTION["DESTINATIONACCOUNT-TYPE"]="SAV";
    $tmp->TRANSACTION["MINQUOTA"]="1";
    $tmp->TRANSACTION["MAXQUOTA"]="5";
    $tmp->TRANSACTION["ALIAS-FROM"]="AS1";
    $tmp->TRANSACTION["ALIAS-TO"]="AHO2";
    $tmp->TRANSACTION["RESPONSECODE"]="0000";
    $bpsequence=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    $tmp->TRANSACTION["BPSEQUENCE"]=$bpsequence;
    return $tmp;

}


function msg650($input){

    $tmp = simplexml_import_dom($input);
    //Format response message
    $tmp["TYPE"]="655";
    $tmp->TRANSACTION["ACCOUNT"]="*****5078";
    $tmp->TRANSACTION["TYPE"]="XCRE";
    $tmp->TRANSACTION["AMOUNT"]="2500.00";
    $tmp->TRANSACTION["CURRENCY"]="DOP";
    $tmp->TRANSACTION["DESTINATIONACCOUNT"]="*****4048";
    $tmp->TRANSACTION["DESTINATIONACCOUNT-TYPE"]="SAV";
    $tmp->TRANSACTION["QUOTA"]="1";
    $tmp->TRANSACTION["RATE"]="0.85";
    $tmp->TRANSACTION["RATE-TYPE"]="ANNUAL";
    $tmp->TRANSACTION["NEWQUOTA"]="500.00";
    $tmp->TRANSACTION["NEWBALANCE"]="5200.00";
    $tmp->TRANSACTION["INSURANCE"]="12.00";
    $tmp->TRANSACTION["ALIAS-FROM"]="AS1";
    $tmp->TRANSACTION["ALIAS-TO"]="AHO2";
    $tmp->TRANSACTION["RESPONSECODE"]="0000";
    $bpsequence=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    $tmp->TRANSACTION["BPSEQUENCE"]=$bpsequence;
    return $tmp;

}


function msg715($input){

    $tmp = simplexml_import_dom($input);
    //Format response message
    $tmp["TYPE"]="720";
    $tmp->TRANSACTION["ACCOUNT"]="*****5078";
    $tmp->TRANSACTION["TYPE"]="XCRE";
    $tmp->TRANSACTION["AMOUNT"]="2500.00";
    $tmp->TRANSACTION["CURRENCY"]="DOP";
    $tmp->TRANSACTION["DESTINATIONACCOUNT"]="*****4048";
    $tmp->TRANSACTION["DESTINATIONACCOUNT-TYPE"]="SAV";
    $tmp->TRANSACTION["QUOTA"]="4";
    $tmp->TRANSACTION["COMMENT"]="20002209***6638/BRD";
    $tmp->TRANSACTION["TRANSACTIONTYPE"]="18";
    $tmp->TRANSACTION["SUBTRANSACTIONTYPE"]="1801";
    $tmp->TRANSACTION["ALIAS-FROM"]="AS1";
    $tmp->TRANSACTION["ALIAS-TO"]="AHO2";
    $tmp->TRANSACTION["BANKSESIONID"]="00025720042013164629";
    $tmp->TRANSACTION["RESPONSECODE"]="0000";
    $bpsequence=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    $tmp->TRANSACTION["BPSEQUENCE"]=$bpsequence;
    return $tmp;

}





function msg815($input){
    
    $tmp = simplexml_import_dom($input);
    //Format response message 
    $tmp["TYPE"]="816";
    ////Error condition on security code = 1234 / return 9899 general error
    if($tmp->TRANSACTION["SECURITYCODE"]=="1234"){
        $tmp->TRANSACTION["RESPONSECODE"]="9899";
    }else {
    $tmp->TRANSACTION["RESPONSECODE"]="0000";
    }
    $bpsequence=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    $tmp->TRANSACTION["BPSEQUENCE"]=$bpsequence;
    return $tmp;    
    
}

function msg920($input){
    
    $tmp = simplexml_import_dom($input);
    //Format response message 
    $tmp["TYPE"]="925";
    ////Error condition on security code = 1234 / return 9899 general error
    $tmp->TRANSACTION["RESPONSECODE"]="0000";
    $bpsequence=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    $tmp->TRANSACTION["BPSEQUENCE"]=$bpsequence;
    return $tmp;    
    
}

function msg930($input){

    $tmp = simplexml_import_dom($input);
    //Format response message
    $tmp["TYPE"]="935";
    ////Error condition on security code = 1234 / return 9899 general error
    $tmp->TRANSACTION["RESPONSECODE"]="0000";
    $bpsequence=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    $tmp->TRANSACTION["BPSEQUENCE"]=$bpsequence;
    return $tmp;

}




function msg879($input){
    
    $tmp = simplexml_import_dom($input);
    //Format response message 
    $tmp["TYPE"]="880";
    $tmp->CLIENT["FULL-NAME"]="JOHN DOE SIMULADO";
    $tmp->CLIENT["ADDRESS"]="26 ESTE, ESQ P SECTOR LA CASTELLANA";
    $tmp->CLIENT["STATUS"]="";
    $tmp->CLIENT["CITY"]="SANTO DOMINGO ESTE SD";
    $tmp->CLIENT["PHONE"]="809-549-5717";
    $tmp->CLIENT["SEGMENT"]="2-Banca Premium";
    $tmp->CLIENT["OFFICER-CODE"]="U15682";
    $tmp->CLIENT["OFFICER-NAME"]="IRIS LUGO";
    $tmp->CLIENT["EMAIL"]="";
    $tmp->TRANSACTION["BANKSESSIONID"]='BDP'.str_pad(rand(0,99999999999999999999), 20, "0", STR_PAD_LEFT);;
    //Error condition on amount = 330 / return 9902
    if($tmp->CLIENT["ID"]=="22500581032" or $tmp->CLIENT["ID"]=="22500581032"){
        $tmp->TRANSACTION["RESPONSECODE"]="9945";
        
        $tmp->TRANSACTION["BPSEQUENCE"]=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
        $tmp->ACCOUNTS->ACCOUNT[0]["BANK"]=$tmp["BANKID"];
        $tmp->ACCOUNTS->ACCOUNT[0]["BANK-NAME"]="Banco GCS";
        $tmp->ACCOUNTS->ACCOUNT[0]["ALIAS"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["NUMBER"]="*****6326";
        $tmp->ACCOUNTS->ACCOUNT[0]["TYPE"]="DDA";
        $tmp->ACCOUNTS->ACCOUNT[0]["CURRENCY"]="DOP";
        $tmp->ACCOUNTS->ACCOUNT[0]["STATUS"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["RELATION"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["BALANCE"]="825.00";
        $tmp->ACCOUNTS->ACCOUNT[0]["AVAILABLE-BALANCE"]="825.00";


    }
    elseif($tmp->CLIENT["ID"]=="22500581263"){
        $tmp->TRANSACTION["RESPONSECODE"]="0000";

        $tmp->TRANSACTION["BPSEQUENCE"]=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
        $tmp->ACCOUNTS->ACCOUNT[0]["BANK"]=$tmp["BANKID"];
        $tmp->ACCOUNTS->ACCOUNT[0]["BANK-NAME"]="Banco GCS";
        $tmp->ACCOUNTS->ACCOUNT[0]["ALIAS"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["NUMBER"]="*****6326";
        $tmp->ACCOUNTS->ACCOUNT[0]["TYPE"]="SAV";
        $tmp->ACCOUNTS->ACCOUNT[0]["CURRENCY"]="DOP";
        $tmp->ACCOUNTS->ACCOUNT[0]["STATUS"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["RELATION"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["BALANCE"]="0.00";
        $tmp->ACCOUNTS->ACCOUNT[0]["AVAILABLE-BALANCE"]="0.00";


    }

elseif($tmp->CLIENT["ID"]=="22500582055"){
        $tmp->TRANSACTION["RESPONSECODE"]="0000";

        $tmp->TRANSACTION["BPSEQUENCE"]=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
        $tmp->ACCOUNTS->ACCOUNT[0]["BANK"]=$tmp["BANKID"];
        $tmp->ACCOUNTS->ACCOUNT[0]["BANK-NAME"]="Banco GCS";
        $tmp->ACCOUNTS->ACCOUNT[0]["ALIAS"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["NUMBER"]="*****6326";
        $tmp->ACCOUNTS->ACCOUNT[0]["TYPE"]="SAV";
        $tmp->ACCOUNTS->ACCOUNT[0]["CURRENCY"]="DOP";
        $tmp->ACCOUNTS->ACCOUNT[0]["STATUS"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["RELATION"]="";
        $tmp->ACCOUNTS->ACCOUNT[0]["BALANCE"]="0.00";
        $tmp->ACCOUNTS->ACCOUNT[0]["AVAILABLE-BALANCE"]="0.00";


    }


    else {
    	$tmp->TRANSACTION["RESPONSECODE"]="0000";
    
    	$tmp->TRANSACTION["BPSEQUENCE"]=str_pad(rand(0,999999), 6, "0", STR_PAD_LEFT);
    	$tmp->ACCOUNTS->ACCOUNT[0]["BANK"]=$tmp["BANKID"];
    	$tmp->ACCOUNTS->ACCOUNT[0]["BANK-NAME"]="Banco GCS";
    	$tmp->ACCOUNTS->ACCOUNT[0]["ALIAS"]="";
    	$tmp->ACCOUNTS->ACCOUNT[0]["NUMBER"]="*****6326";
    	$tmp->ACCOUNTS->ACCOUNT[0]["TYPE"]="LOAN";
    	$tmp->ACCOUNTS->ACCOUNT[0]["CURRENCY"]="DOP";
    	$tmp->ACCOUNTS->ACCOUNT[0]["STATUS"]="";
    	$tmp->ACCOUNTS->ACCOUNT[0]["RELATION"]="";
    	$tmp->ACCOUNTS->ACCOUNT[0]["BALANCE"]="10811.00";
    	$tmp->ACCOUNTS->ACCOUNT[0]["AVAILABLE-BALANCE"]="0.00";
    
    	$tmp->ACCOUNTS->ACCOUNT[1]["BANK"]=$tmp["BANKID"];
    	$tmp->ACCOUNTS->ACCOUNT[1]["BANK-NAME"]="Banco GCS";
    	$tmp->ACCOUNTS->ACCOUNT[1]["ALIAS"]="";
    	$tmp->ACCOUNTS->ACCOUNT[1]["NUMBER"]="************6501";
    	$tmp->ACCOUNTS->ACCOUNT[1]["TYPE"]="CC";
    	$tmp->ACCOUNTS->ACCOUNT[1]["CURRENCY"]="DOP";
    	$tmp->ACCOUNTS->ACCOUNT[1]["STATUS"]="";
    	$tmp->ACCOUNTS->ACCOUNT[1]["RELATION"]="";
    	$tmp->ACCOUNTS->ACCOUNT[1]["BALANCE"]="16244.54";
    	$tmp->ACCOUNTS->ACCOUNT[1]["AVAILABLE-BALANCE"]="13753.46";
    
	$tmp->ACCOUNTS->ACCOUNT[2]["BANK"]=$tmp["BANKID"];
        $tmp->ACCOUNTS->ACCOUNT[2]["BANK-NAME"]="Banco GCS";
        $tmp->ACCOUNTS->ACCOUNT[2]["ALIAS"]="";
        $tmp->ACCOUNTS->ACCOUNT[2]["NUMBER"]="************5528";
        $tmp->ACCOUNTS->ACCOUNT[2]["TYPE"]="SAV";
        $tmp->ACCOUNTS->ACCOUNT[2]["CURRENCY"]="DOP";
        $tmp->ACCOUNTS->ACCOUNT[2]["STATUS"]="";
        $tmp->ACCOUNTS->ACCOUNT[2]["RELATION"]="";
        $tmp->ACCOUNTS->ACCOUNT[2]["BALANCE"]="16244.54";
        $tmp->ACCOUNTS->ACCOUNT[2]["AVAILABLE-BALANCE"]="13753.46";

    	$tmp->ACCOUNTS->ACCOUNT[3]["BANK"]=$tmp["BANKID"];
    	$tmp->ACCOUNTS->ACCOUNT[3]["BANK-NAME"]="Banco GCS";
    	$tmp->ACCOUNTS->ACCOUNT[3]["ALIAS"]="";
    	$tmp->ACCOUNTS->ACCOUNT[3]["NUMBER"]="*****2487";
    	$tmp->ACCOUNTS->ACCOUNT[3]["TYPE"]="DDA";
    	$tmp->ACCOUNTS->ACCOUNT[3]["CURRENCY"]="DOP";
    	$tmp->ACCOUNTS->ACCOUNT[3]["STATUS"]="";
    	$tmp->ACCOUNTS->ACCOUNT[3]["RELATION"]="";
    	$tmp->ACCOUNTS->ACCOUNT[3]["BALANCE"]="23456.55";
    	$tmp->ACCOUNTS->ACCOUNT[3]["AVAILABLE-BALANCE"]="23456.55";

    	/*$tmp->ACCOUNTS->ACCOUNT[4]["BANK"]=$tmp["BANKID"];
    	$tmp->ACCOUNTS->ACCOUNT[4]["BANK-NAME"]="Banco". $tmp["BANKID"];
    	$tmp->ACCOUNTS->ACCOUNT[4]["ALIAS"]="";
    	$tmp->ACCOUNTS->ACCOUNT[4]["NUMBER"]="*****2590";
    	$tmp->ACCOUNTS->ACCOUNT[4]["TYPE"]="DDA";
    	$tmp->ACCOUNTS->ACCOUNT[4]["CURRENCY"]="DOP";
    	$tmp->ACCOUNTS->ACCOUNT[4]["STATUS"]="";
    	$tmp->ACCOUNTS->ACCOUNT[4]["RELATION"]="";
    	$tmp->ACCOUNTS->ACCOUNT[4]["BALANCE"]="5234.00";
    	$tmp->ACCOUNTS->ACCOUNT[4]["AVAILABLE-BALANCE"]="5034.00";

        $tmp->ACCOUNTS->ACCOUNT[5]["BANK"]=$tmp["BANKID"];
        $tmp->ACCOUNTS->ACCOUNT[5]["BANK-NAME"]="Banco ". $tmp["BANKID"];
        $tmp->ACCOUNTS->ACCOUNT[5]["ALIAS"]="";
        $tmp->ACCOUNTS->ACCOUNT[5]["NUMBER"]="*****7386";
        $tmp->ACCOUNTS->ACCOUNT[5]["TYPE"]="LOAN";
        $tmp->ACCOUNTS->ACCOUNT[5]["CURRENCY"]="DOP";
        $tmp->ACCOUNTS->ACCOUNT[5]["STATUS"]="";
        $tmp->ACCOUNTS->ACCOUNT[5]["RELATION"]="";
        $tmp->ACCOUNTS->ACCOUNT[5]["BALANCE"]="1811.00";
        $tmp->ACCOUNTS->ACCOUNT[5]["AVAILABLE-BALANCE"]="0.00";

        $tmp->ACCOUNTS->ACCOUNT[6]["BANK"]=$tmp["BANKID"];
        $tmp->ACCOUNTS->ACCOUNT[6]["BANK-NAME"]="Banco". $tmp["BANKID"];
        $tmp->ACCOUNTS->ACCOUNT[6]["ALIAS"]="";
        $tmp->ACCOUNTS->ACCOUNT[6]["NUMBER"]="************2801";
        $tmp->ACCOUNTS->ACCOUNT[6]["TYPE"]="CC";
        $tmp->ACCOUNTS->ACCOUNT[6]["CURRENCY"]="DOP";
        $tmp->ACCOUNTS->ACCOUNT[6]["STATUS"]="";
        $tmp->ACCOUNTS->ACCOUNT[6]["RELATION"]="";
        $tmp->ACCOUNTS->ACCOUNT[6]["BALANCE"]="19204.54";
        $tmp->ACCOUNTS->ACCOUNT[6]["AVAILABLE-BALANCE"]="10713.46";*/
	if($tmp["BANKID"]="BDP"){
		$tmp->ACCOUNTS->ACCOUNT[4]["BANK"]=$tmp["BANKID"];
        	$tmp->ACCOUNTS->ACCOUNT[4]["BANK-NAME"]="Banco GCS";
        	$tmp->ACCOUNTS->ACCOUNT[4]["ALIAS"]="";
        	$tmp->ACCOUNTS->ACCOUNT[4]["NUMBER"]="************5678";
        	$tmp->ACCOUNTS->ACCOUNT[4]["TYPE"]="AMEX";
        	$tmp->ACCOUNTS->ACCOUNT[4]["CURRENCY"]="DOP";
        	$tmp->ACCOUNTS->ACCOUNT[4]["STATUS"]="";
        	$tmp->ACCOUNTS->ACCOUNT[4]["RELATION"]="";
        	$tmp->ACCOUNTS->ACCOUNT[4]["BALANCE"]="19204.54";
        	$tmp->ACCOUNTS->ACCOUNT[4]["AVAILABLE-BALANCE"]="10713.46";

	}

    }
    return $tmp;
}


?>
