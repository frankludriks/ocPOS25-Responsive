<?php
// includes/lang/english/cc_add.php


define('TITLE', 'Checkout with Credit Card');
define('ENTER_LAST4', 'Enter last 4 digits of Credit Card Number');
define('PROCESS_ORDER', 'Process Order');
define('CANCEL', 'Cancel');

// NOTE:  The first portion of the following definion -- "Credit Card" -- is used when recording the payment method type of an order.  If you change this, you must also change the same value in the report.php language file (i.e. includes/lang/<your language>/report.php.
// In other words, if you change the definition below to 'Tarjeta de Credito:  ************' then you must also change the definition for CC in report.php to 'Tarjeta de Credito' or reporting may not correctly show the credit card sales totals.
define('CREDIT_CARD', 'Credit Card');

// Button Titles
define('PROCESS_ORDER_BUTTON_TITLE', 'Process this Order');
define('CANCEL_BUTTON_TITLE', 'Cancel');
?>
