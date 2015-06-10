<?php
// includes/lang/espanol/cc_add.php


define('TITLE', 'Procesar Orden con Tarjeta de Credito');
define('ENTER_LAST4', 'Entre los cuatro ultimos digitos de su numero de tarjeta de credito');
define('PROCESS_ORDER', 'Procesar Orden');
define('CANCEL', 'Cancelar');

// NOTE:  The first portion of the following definion -- "Credit Card" -- is used when recording the payment method type of an order.  If you change this, you must also change the same value in the report.php language file (i.e. includes/lang/<your language>/report.php.
// In other words, if you change the definition below to:  Tarjeta de Credito:  ************   then you must also change the definition for CC in report.php to:  Tarjeta de Credito   or reporting may not correctly show the credit card sales totals.
define('CREDIT_CARD', 'Tarjeta de Credito');

// Button Titles
define('PROCESS_ORDER_BUTTON_TITLE', 'Procesar esta Orden');
define('CANCEL_BUTTON_TITLE', 'Cancelar');
?>
