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
define("TOTAL_BILLS","maxbills");
define("MININUM_BILLS","minbills");
define("FILE_HEADER","01");
define("FILE_RECORD","02");
define("LOGERROR", "ERROR");
define("LOGINFO", "INFO");
define("LOGWARN", "WARNING");
define("HTTP_GET","GET");
define("HTTP_POST","POST");
define("HTTP_PUT","PUT");
define("HTTP_DELETE","DELETE");
define("HTTP_HEAD","HEAD");
define("HTTP_CONNECT","CONNECT");
define("HTTP_OPTIONS","OPTIONS");
define("HTTP_TRACE","TRACE");
define("HTTP_PATCH","PATCH");
define("CACHE_CREATE","C");
define("CACHE_UPDATE","U");
define("CACHE_DELETE","D");
define("BUILDER_PURGE","P");
define("SOCKET_CLIENT", "C");





?>