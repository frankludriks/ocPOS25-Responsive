<?php
// includes/lang/english/cc_add.php


define('TITLE', 'Authorize.Net Payment Processing');
define('CC_NUMBER', 'N�mero de Tarjeta de Cr�dito:');
define('EXPIRES_MMYY', 'Fecha de Caducidad: (MMAA)');
define('CVV_CODE', 'C�digo de Seguridad:');
define('FIRST_NAME', 'Primer nombre');
define('LAST_NAME', 'Apellido');
define('PHONE', 'Tel�fono:');
define('COMPANY', 'Compan�a');
define('STREET_ADDR', 'Direcci�n:');
define('CITY', 'Ciudad:');
define('STATE', 'Estado:');
define('POST_CODE', 'C�digo Postal:');
define('COUNTRY', 'Pa�s:');
define('ORIG_TRANS_ID', 'N�mero de Transacci�n Original: ');
define('FIELDS_REQUIRED', 'Se requiere toda informaci�n en <span class="form_required">rojo</span>.');

define('ENTER_LAST4', '�ltimos 4 d�gitos del N�mero de Tarjeta de Cr�dito');
define('PROCESS_ORDER', 'Procesar Orden');
define('RETURN_TO_FORM', 'Int�ntalo otra vez');
define('CANCEL', 'Cancelar');
define('APPROVED', 'Transacci�n Autorizado');
define('TRANSACTION_ID', 'N�mero Transaction: ');
define('STOLEN_CARD', 'Esta tarjeta de cr�dito ha sido reportado robado.  Guarde la tarjeta y contacte los autoridades.');
define('DECLINED', 'Transacci�n No Autorizado');
define('TRANSACTION_ERROR', 'Error en Tramite');
define('HELD_FOR_REVIEW','Transacci�n Retrasado para Reconsiderar');
define('PROCESSING_ERROR','Error en Procesar');
define('NO_TRANS_ID', '[No se encontr� un n�mero de transacci�n]');
define('RETURN_AMOUNT','Cantidad de Cr�dito');
define('CHARGE_AMOUNT','Cantidad de Carga');



// NOTE:  The first portion of the following definion -- "Credit Card" -- is used when recording the payment method type of an order.  If you change this, you must also change the same value in the report.php language file (i.e. includes/lang/<your language>/report.php.
// In other words, if you change the definition below to 'Tarjeta de Credito:  ************' then you must also change the definition for CC in report.php to 'Tarjeta de Credito' or reporting may not correctly show the credit card sales totals.
define('CREDIT_CARD', 'Credit Card:  ************');

// Button Titles
define('RETURN_TO_FORM_BUTTON_TITLE', 'Try Again');
define('PROCESS_ORDER_BUTTON_TITLE', 'Process this Order');
define('CANCEL_BUTTON_TITLE', 'Cancel');
?>
