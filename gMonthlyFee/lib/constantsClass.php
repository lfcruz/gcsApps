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
define("XML_RESPONSE_OK","0000");
define("E_INTERNAL","9999");
define("E_METHOD","9901");
define("E_PROCESS","9902");
define("E_AUTH_FAILED","9903");
define("E_CC_ACCOUNT_INVALID","9904");

// Security Accounts Errors
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
define("EOF","\n");
define("LOGERROR", 1);
define("LOGINFO", 2);
define("LOGTRACE", 3);
define("LOGDEBUG", 4);
define("HTTP_GET","GET");
define("HTTP_POST","POST");
define("HTTP_PUT","PUT");
define("HTTP_DELETE","DELETE");
define("HTTP_HEAD","HEAD");
define("HTTP_CONNECT","CONNECT");
define("HTTP_OPTIONS","OPTIONS");
define("HTTP_TRACE","TRACE");
define("HTTP_PATCH","PATCH");
define("SOCKET_CLIENT", "C");
define("SOCKET_SERVER","S");
define("MF_MAX_AGEING","CollectorSchedulerDays");
define("MF_MAX_INACTIVITY","InactivityDays");
define("MF_FEE","CustomerMonthlyFee");
define("MF_INACTIVITY_FEE","GCSInActivityFee");
define("NSF_FEE","MONTHLY_NSF_FEE");
define("ENGINE_DEFAULT","Default");
define("ENGINE_AGEING","Ageing");
define("ENGINE_BANKS","Banks");





?>