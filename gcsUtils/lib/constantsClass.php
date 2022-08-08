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

//Data Queries
define("QRY_JUNCTION_AND"," and ");
define("QRY_JUNCTION_OR"," or ");
define("QRY_LIMIT"," limit ");
define("QRY_GET_MF_PARAMS","select GEN_PARAMETER_NAME, PARAM_DISPLAY_NAME, PARAMETER_VALUE from  GCSGENERIC_PARAMS_M where GEN_PARAMETER_NAME in ");
define("QRY_GET_MF_BASE","select mbp.monthlybill_payment_id, mbp.gcs_account_id, mbp.bill_due_date, mbp.monthly_bill_amount from monthly_gcs_bill_payment_l mbp inner join r_gcscustomer_account_m acc on (acc.gcs_account_id = mbp.gcs_account_id) where mbp.collector_status = '0'");
define("QRY_FILTER_DEFAULT"," and mbp.bill_due_date > current_timestamp - interval '");
define("QRY_FILTER_ACTIVE_ACCOUNTS", " and acc.status = 'A' order by bill_due_date");
define("QRY_FILTER_EXCLUDE_ISBATCH", " and is_batch_process <> 'Y'");
define("QRY_FILTER_ENGINE_AGEING_TOP"," and mbp.bill_due_date > current_timestamp - interval '");
define("QRY_FILTER_ENGINE_AGEING_BOT"," and mbp.bill_due_date < current_timestamp - interval '");
define("QRY_INTERVAL_DAY", "' day");
define("QRY_INTERVAL_MONTH", "' months");
define("QRY_INTERVAL_YEARS", "' years");
define("QRY_INTERVAL_MINUTES", "' minutes");
define("QRY_INTERVAL_SECONDS", "' seconds");
define("QRY_FILTER_ENGINE_BANKS"," and partner_code in ");
define("QRY_UPDATE_BILL_CHARGE", "update monthly_gcs_bill_payment_l mbp set mbp.gcs_sequenceno = $1 where mbp.monthlybill_payment_id = $2");
define("QRY_GET_POOL_BULK","select * from jobs where pool_id = ");
define("QRY_FILTER_JOBS_TYPE"," job_type = ");
define("QRY_FILTER_JOBS_CHANNEL"," channel_id = ");
define("QRY_FILTER_JOBS_POOL"," pool_id = ");
define("QRY_FILTER_JOBS_IS_SUBSCRIBER"," is_subscriber is true");



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
define("MF_LIST_BY_BILLS","MF_LIST_BY_BILLS");
define("MF_LIST_BY_SUBS","MF_LIST_BY_SUBS");





?>