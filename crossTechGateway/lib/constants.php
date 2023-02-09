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
define("CODE_LENGHT", 6);
define("REFERENCE_LENGHT", 8);
define ("CODE_STATUS_PENDING","P");
define ("CODE_STATUS_ACTIVE","A");
define ("CODE_STATUS_EXPIRED","E");
define ("CODE_STATUS_CANCELED","C");
define ("CODE_STATUS_REEDIMMED","R");
define("HMAC_ALGO_SHA512","sha512");
define("DTO_USER_VALIDATION","select prt.id as partner_id, prt.code as partner_code, prt.name as partner_name, prt.base as isPartnerBase, prt.parent as partner_parent_id, usr.id as user_id, usr.username, usr.secured, usr.salted, usr.active from api_security.users usr inner join  api_security.partners prt on (usr.partner_id = prt.id) where usr.username = $1 and (usr.partner_id = $2 or prt.parent = $2) and usr.active is true");
define("DTO_USER_PERMITS","select sr.api_tag||'/'||fu.api_tag as permits from api_security.users_roles ur inner join api_security.roles rl on (rl.id = ur.role_id) inner join api_security.roles_definition rd on (rd.roles_id = rl.id) inner join api_security.functions fu on (fu.id = rd.functions_id) inner join api_security.services sr on (sr.id = fu.service_id) where ur.user_id = $1 and sr.active is true and fu.active is true");
define("DTO_GET_DATA","L1nux2kkk@9366");
define("DTO_GET_DATA2","xOcOKudvs9kOlK85U11WjUBh7sxfN1Xj");
define("DTO_GET_CODE_SEQUENCE", "select nextval('api_purchasecode.seq_purchasecodes')");
define("DTO_INSERT_CODE_REQUEST","insert into api_purchasecode.purchasecodes (id, partner_id, user_id, request_id, unique_customer_id, amount, currency, lifetime, merchant_id, terminal_id, reference_id, purchase_code, creation_date, expire_date, status) values ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, null, null, current_timestamp, null, $11)");
define("DTO_VALIDATE_UNIQUE_CUSTOMER", "select count(1) as active_codes from api_purchasecode.purchasecodes where status in ('A','P') and id <> $1 and unique_customer_id = $2 and partner_id = $3");
define("DTO_CANCEL_UNIQUE_CUSTOMER_CODES", "update api_purchasecode.purchasecodes set status = 'C' where id <> $1 and unique_customer_id = $2 and partner_id = $3");
define("DTO_UPDATE_PURCHASE_CODE_REFERENCE", "update api_purchasecode.purchasecodes set reference_id = $1, purchase_code = $2 where id = $3");
define("DTO_UPDATE_CODE_EXPIRE_DATE", "update api_purchasecode.purchasecodes set expire_date = current_timestamp + interval '");
define("DTO_UPDATE_CODE_EXPIRE_INTERVAL_MINUTES", " minutes'");
define("DTO_UPDATE_CODE_EXPIRE_INTERVAL_SECONDS", " seconds'");
define("DTO_UPDATE_CODE_EXPIRE_INTERVAL_HOURS", " hours'");
define("DTO_UPDATE_CODE_EXPIRE_TRAIL"," where id = $1");
define("DTO_ACTIVATE_CODE", "update api_purchasecode.purchasecodes set status = 'A' where id = $1");
?>