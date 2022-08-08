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
/*
 * ****************
 * GENERAL ERRORS *
 ******************
 */
define("PROC_OK","0000");
define("E_INTERNAL","9999");
define("E_METHOD","9901");
define("E_PROCESS","9902");
define("E_AUTH_FAILED","9903");
define("E_INVALID_BILLER","9904");
define("E_INVALID_NIC","9005");
define("E_INVALID_AMOUNT","9006");
define("E_INVALID_PAYMENT","9007");
define("E_PAYMENT_ERROR","9900");

define("W_ACCOUNT_EXPIRING","0001");
define("W_NO_PENDING_BILLS","0002");
define("E_ACCOUNT_LOCK","8001");
define("E_ACCOUNT_EXPIRED","9002");
define("E_ACCOUNT_EXIST","8003");

// General Values
/*
 * ****************
 * GENERAL VALUES *
 ******************
 */
define("TOTAL_BILLS","maxbills");
define("MININUM_BILLS","minbills");
define("FILE_HEADER","01");
define("FILE_RECORD","02");
define("LOGERROR", "ERROR");
define("LOGINFO", "INFO");
define("LOGWARN", "WARNING");

define("TCP_CLIENT", "C");
define("TCP_SERVER", "S");
define("TELCO_CLARO", "300");
define("TELCO_MOVISTAR", "500");
define("ROUTE_TPAGO","CORE");
define("ROUTE_CONNECTOR","CONECTOR");
define("MSG860",'<MESSAGE TYPE="860" BANKID="102" CORRELATIONID="2012071212545622321UL65d"><CLIENT ID="00111054938" TYPE="Cedula" TELEPHONE="8292147747" TELCOID="200" BPSEQUENCE="111644" /><TRANSACTION DATE="12072012" TIME="125456" /></MESSAGE>');
define("MSG800",'<MESSAGE TYPE="800" BANKID="102" CORRELATIONID="2012071212553631598UL9ee"><CLIENT ID="00111054938" TYPE="Cedula" TELEPHONE="8292147747" TELCOID="200" BPSEQUENCE="111644" /><PRODUCTS><PRODUCT ID="01" ACCOUNT="*****3635" TYPE="DDA" CURRENCY="DOP" ALIAS="BP_DDA" /></PRODUCTS><TRANSACTION DATE="12072012" TIME="125536" /></MESSAGE>');
define("MSG815",'<MESSAGE TYPE="815" CORRELATIONID="" BANKID=""><CLIENT ID="" TYPE="DPI" GCSSEQUENCE="" TELEPHONE=""/><TRANSACTION DATE="" TIME="" SECURITYCODE=""/></MESSAGE>');


?>