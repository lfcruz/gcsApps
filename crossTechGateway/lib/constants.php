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
define("E_AUTHORIZATION","8001");

// General Values
define("HTTP_GET", "GET");
define("HTTP_POST", "POST");
define("HTTP_PUT", "PUT");
define("HTTP_DELETE", "DELETE");
define("ERROR_LOG", "ERROR");
define("INFO_LOG", "INFO");
define("WARNING_LOG", "WARNING");



?>