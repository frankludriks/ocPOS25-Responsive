<?php
// includes/lang/french/cc_add.php


define('TITLE', 'Authorize.Net Payment Processing');
define('CC_NUMBER', 'CC Number:');
define('EXPIRES_MMYY', 'Expires: (MMYY)');
define('CVV_CODE', 'CVV/AVS Code:');
define('FIRST_NAME', 'First Name:');
define('LAST_NAME', 'Last Name:');
define('PHONE', 'Telephone:');
define('COMPANY', 'Company');
define('STREET_ADDR', 'Street Addr:');
define('CITY', 'City:');
define('STATE', 'State:');
define('POST_CODE', 'Zip Code:');
define('COUNTRY', 'Country:');
define('ORIG_TRANS_ID', 'Original Transaction ID: ');
define('FIELDS_REQUIRED', 'Fields in <span class="form_required">red</span> are required.');

define('ENTER_LAST4', 'Enter last 4 digits of Credit Card Number');
define('PROCESS_ORDER', 'Process Order');
define('RETURN_TO_FORM', 'Try Again');
define('CANCEL', 'Cancel');
define('APPROVED', 'Transaction Approved');
define('TRANSACTION_ID', 'Transaction ID: ');
define('STOLEN_CARD', 'This credit card was reported stolen.  Please retain the card and contact authorities.');
define('DECLINED', 'Transaction Declined');
define('TRANSACTION_ERROR', 'Transaction Error');
define('HELD_FOR_REVIEW','Transaction Held for Review');
define('PROCESSING_ERROR','Processing Error');
define('NO_TRANS_ID', '[No transaction ID returned]');
define('RETURN_AMOUNT','Credit Amount');
define('CHARGE_AMOUNT','Charge Amount');



// NOTE:  The first portion of the following definion -- "Credit Card" -- is used when recording the payment method type of an order.  If you change this, you must also change the same value in the report.php language file (i.e. includes/lang/<your language>/report.php.
// In other words, if you change the definition below to 'Tarjeta de Credito:  ************' then you must also change the definition for CC in report.php to 'Tarjeta de Credito' or reporting may not correctly show the credit card sales totals.
define('CREDIT_CARD', 'Credit Card:  ************');

// Button Titles
define('RETURN_TO_FORM_BUTTON_TITLE', 'Try Again');
define('PROCESS_ORDER_BUTTON_TITLE', 'Process this Order');
define('CANCEL_BUTTON_TITLE', 'Cancel');
?>
