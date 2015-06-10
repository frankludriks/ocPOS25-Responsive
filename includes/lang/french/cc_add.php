﻿<?php
// includes/lang/french/cc_add.php



define('TITLE', 'Règlement par Carte de Crédit');
define('ENTER_LAST4', 'Entrer les 4 derniers chiffres de la carte de crédit');
define('PROCESS_ORDER', 'Comfirmer la commande');
define('CANCEL', 'Annuler');

// NOTE:  The first portion of the following definion -- "Credit Card" -- is used when recording the payment method type of an order.  If you change this, you must also change the same value in the report.php language file (i.e. includes/lang/<your language>/report.php.
// In other words, if you change the definition below to 'Tarjeta de Credito:  ************' then you must also change the definition for CC in report.php to 'Tarjeta de Credito' or reporting may not correctly show the credit card sales totals.
define('CREDIT_CARD', 'Carte de Crédit:  ************');

// Button Titles
define('PROCESS_ORDER_BUTTON_TITLE', 'Confirmer cette commande');
define('CANCEL_BUTTON_TITLE', 'Annuler');
?>
