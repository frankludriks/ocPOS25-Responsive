<?php
// includes/lang/norwegian/cc_add.php


define('TITLE', 'Checkout med Kredittkort');
define('ENTER_LAST4', 'Vare 4 numrene av Kredittkort');
define('PROCESS_ORDER', 'Behandle Ordre');
define('CANCEL', 'Avbryt');

// NOTE:  The first portion of the following definion -- "Credit Card" -- is used when recording the payment method type of an order.  If you change this, you must also change the same value in the report.php language file (i.e. includes/lang/<your language>/report.php.
// In other words, if you change the definition below to 'Tarjeta de Credito:  ************' then you must also change the definition for CC in report.php to 'Tarjeta de Credito' or reporting may not correctly show the credit card sales totals.
define('CREDIT_CARD', 'Kredittkort');

// Button Titles
define('PROCESS_ORDER_BUTTON_TITLE', 'Behandle Ordre');
define('CANCEL_BUTTON_TITLE', 'Avbryt');
?>
