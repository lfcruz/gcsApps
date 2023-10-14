<?php
// HTTP Response Codes
define("HTTP_OK","HTTP/1.1 200 OK");
define("HTTP_FAIL","HTTP/1.1 304 Not Modified");
define("HTTP_INVALID","HTTP/1.1 400 Bad Request");
define("HTTP_UNAUTHORIZED","HTTP/1.1 401 Unauthorized");
define("HTTP_METHOD","HTTP/1.1 405 Method Not Allowed");
define("HTTP_ERROR","HTTP/1.1 500 Internal Server Error");
define("HTTP_UNAVAILABLE","HTTP/1.1 503 Service Unavailable");

// Processing Response Codes
define("PROC_OK","0000");
define("E_GENERAL","9999");
define("E_METHOD","9998");
define("E_PROCESS","9997");
define("E_GENERATING_CODE", "9996");
define("E_AUTHORIZATION","8001");

// General Values
define("HTTP_GET", "GET");
define("HTTP_POST", "POST");
define("HTTP_PUT", "PUT");
define("HTTP_DELETE", "DELETE");
define("ERROR_LOG", "ERROR");
define("INFO_LOG", "INFO");
define("WARNING_LOG", "WARNING");



//TAGS
define ("STATUS_PENDING","I");
define ("STATUS_COMPLETED","C");
define ("STATUS_FAILED","F");
define ("XML_JSON_STRUCTURE",'{"MESSAGE" : {"TYPE" : "115","BANKID" : "", "CORRELATIONID" : "", "COUNTRY" : "DO","CHANNEL" : "POS", "PARTNERID" : "", "AGENCYID" : "","TERMINALID" : "","SHIFTID" : "","USERNAME" : """CLIENT" : {"ID" : "99999999999", "TYPE" : "CEDULA","GCSSEQUENCE" : "000011484904","TELEPHONE" : "9999999999"},"TRANSACTION" : {"DATE" : "","TIME" : "","TRANSACTIONTYPE" : "16","ACCOUNT" : "000725918189","TYPE" : "DDA","CURRENCY" : "DOP","AMOUNT" : "", "COMMENT" : "", "MERCHANTID" : "","BENEFICIARY" : "","CONTRACTNUMBER" : "","TERMINALID" : "","MCC" : "","SUBTRANSACTIONTYPE" : "1618","AUTH-CODE" : "604199",}}');
#define("DTO_GET_BULK","select * from accountoperation where id in (select id from accountoperation where status = 'I' order by  id limit $1) for update skip locked");
#define("DTO_UPDATE_STATUS","update accountoperation set status = '$1' where id = $2");

define("DTO_GET_BULK","select * from accountoperation where id in (select id from accountoperation where status = 'I' order by  id limit ");
define("DTO_UPDATE_STATUS","update accountoperation set status = '$1' where id = $2");
?>