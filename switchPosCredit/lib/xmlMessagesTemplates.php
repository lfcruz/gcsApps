<?php
// HTTP Response Codes
define("DKT_115",'<MESSAGE TYPE="115" BANKID="" CORRELATIONID="" COUNTRY="DO" CHANNEL="POS" PARTNERID="" AGENCYID="" TERMINALID="" SHIFTID="" USERNAME=""><CLIENT ID="99999999999" TYPE="CEDULA" GCSSEQUENCE="" TELEPHONE="9999999999" /><TRANSACTION DATE="" TIME="" TRANSACTIONTYPE="16" ACCOUNT="" TYPE="DDA" CURRENCY="DOP" AMOUNT="" COMMENT="" MERCHANTID="" BENEFICIARY="" CONTRACTNUMBER="" TERMINALID="" MCC="" SUBTRANSACTIONTYPE="1618" AUTH-CODE="" /></MESSAGE>');
define("DKT_400",'<MESSAGE TYPE="400" BANKID="" CORRELATIONID="" COUNTRY="DO" CHANNEL="POS" PARTNERID="" AGENCYID="" TERMINALID="" SHIFTID="" USERNAME=""><CLIENT ID="99999999999" TYPE="CEDULA" GCSSEQUENCE="" TELEPHONE="9999999999" /><TRANSACTION DATE="" TIME="" TRANSACTIONTYPE="16" ACCOUNT="" TYPE="DDA" CURRENCY="DOP" AMOUNT="" COMMENT="" MERCHANTID="" BENEFICIARY="" CONTRACTNUMBER="" TERMINALID="" MCC="" SUBTRANSACTIONTYPE="1618" GCSCOMMISSION="0" ACTION="REVERSE" /></MESSAGE>');
define("DKT_5004","HTTP/1.1 400 Bad Request");
define("DKT_419","HTTP/1.1 401 Unauthorized");
?>