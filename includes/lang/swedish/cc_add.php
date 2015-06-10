<?php
// includes/lang/swedish/cc_add.php


define('TITLE', 'Checkout med Kreditkort');
define('ENTER_LAST4', 'Ange de 4 sista siffrorna i kortnumret');
define('PROCESS_ORDER', 'Slutför order');
define('CANCEL', 'Avbryt');

// NOTE:  The first portion of the following definion -- "Credit Card" -- is used when recording the payment method type of an order.  If you change this, you must also change the same value in the report.php language file (i.e. includes/lang/<your language>/report.php.
// In other words, if you change the definition below to 'Tarjeta de Credito:  ************' then you must also change the definition for CC in report.php to 'Tarjeta de Credito' or reporting may not correctly show the credit card sales totals.
define('CREDIT_CARD', 'Betalkort');

// Button Titles
define('PROCESS_ORDER_BUTTON_TITLE', 'Slutför denna order');
define('CANCEL_BUTTON_TITLE', 'Avbryt');
?>
