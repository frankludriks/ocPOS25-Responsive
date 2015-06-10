<?php
// includes/lang/english/cc_add.php


define('TITLE', 'Authorize.Net Payment Processing');
define('CC_NUMBER', 'Número de Tarjeta de Crédito:');
define('EXPIRES_MMYY', 'Fecha de Caducidad: (MMAA)');
define('CVV_CODE', 'Código de Seguridad:');
define('FIRST_NAME', 'Primer nombre');
define('LAST_NAME', 'Apellido');
define('PHONE', 'Teléfono:');
define('COMPANY', 'Companía');
define('STREET_ADDR', 'Dirección:');
define('CITY', 'Ciudad:');
define('STATE', 'Estado:');
define('POST_CODE', 'Código Postal:');
define('COUNTRY', 'País:');
define('ORIG_TRANS_ID', 'Número de Transacción Original: ');
define('FIELDS_REQUIRED', 'Se requiere toda información en <span class="form_required">rojo</span>.');

define('ENTER_LAST4', 'Últimos 4 dígitos del Número de Tarjeta de Crédito');
define('PROCESS_ORDER', 'Procesar Orden');
define('RETURN_TO_FORM', 'Inténtalo otra vez');
define('CANCEL', 'Cancelar');
define('APPROVED', 'Transacción Autorizado');
define('TRANSACTION_ID', 'Número Transaction: ');
define('STOLEN_CARD', 'Esta tarjeta de crédito ha sido reportado robado.  Guarde la tarjeta y contacte los autoridades.');
define('DECLINED', 'Transacción No Autorizado');
define('TRANSACTION_ERROR', 'Error en Tramite');
define('HELD_FOR_REVIEW','Transacción Retrasado para Reconsiderar');
define('PROCESSING_ERROR','Error en Procesar');
define('NO_TRANS_ID', '[No se encontró un número de transacción]');
define('RETURN_AMOUNT','Cantidad de Crédito');
define('CHARGE_AMOUNT','Cantidad de Carga');



// NOTE:  The first portion of the following definion -- "Credit Card" -- is used when recording the payment method type of an order.  If you change this, you must also change the same value in the report.php language file (i.e. includes/lang/<your language>/report.php.
// In other words, if you change the definition below to 'Tarjeta de Credito:  ************' then you must also change the definition for CC in report.php to 'Tarjeta de Credito' or reporting may not correctly show the credit card sales totals.
define('CREDIT_CARD', 'Credit Card:  ************');

// Button Titles
define('RETURN_TO_FORM_BUTTON_TITLE', 'Try Again');
define('PROCESS_ORDER_BUTTON_TITLE', 'Process this Order');
define('CANCEL_BUTTON_TITLE', 'Cancel');
?>
