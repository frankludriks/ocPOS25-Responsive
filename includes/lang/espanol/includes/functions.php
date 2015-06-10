<?php
// includes/lang/espanol/includes/functions.php


define('FIELD_BLANK', 'Un espacio requerido es blanco:  %s');
define('FIELD_MUST_BE_NUMBER', 'Data tiene que ser un numero:  %s');
define('NOBODY', '(Nadie)');
define('QUANTITY_EXCEEDED', 'Se ha intentado incrementar la cantidad del producto mas \nque lo disponible.\n\nSi es necesario vender mas de este producto, \n\nse tendra que editar el producto \ny cambiar la cantidad disponible.');

//PrintReceipt function strings
define('INVOICE', 'Factura #');
define('NOT_PROCESSED', '(No Procesado)');
define('SEPARATOR', '===================================');
define('SUBTOTAL', 'Subtotal');
define('TAX', 'Impuesto');
define('TOTAL', 'Total');
define('CASH_TENDERED', 'Efectivo');
define('CHECK_TENDERED', 'Cheque');
define('CHANGE', 'Cambio');
define('THANK_YOU', 'Gracias por comprar en %s!');
define('VISIT_ONLINE', 'Visitanos online en %s.');

//Print_Full function strings
define('ADD_ITEM', 'Agregar producto a la Orden');
define('DISCOUNT', 'Descuento');
define('DISCOUNT_ENTIRE_ORDER', 'Descontar la orden');
define('MODEL', 'Modelo #');
define('NAME', 'Nombre del Producto');
define('PRICE', 'Precio');
define('QUANTITY', 'Cantidad');
define('NON_INVENTORY', '(Producto Especial)');
define('NOT_SET', 'No establecido'); 
define('ADJUST_PRICE', 'Adjustar Precio');
define('ADD_ONE', 'Agregar 1');
define('SUBTRACT_ONE', 'Sacar 1');
define('ADD_MULT', 'Agregar Varios');
define('REMOVE', 'Sacar de la Orden');
define('ORDER_TOTAL', 'Total');
define('ITEM_COUNT', '# de articulos:');
define('COMMENTS', 'Comentarios');
define('REMOVE_ORDER', 'Abandonar Orden');
define('DROP_ASSIGNED_CUST', 'Despedir Cliente');
define('ADD_COMMENTS', 'Comentarios');
define('APPLY_TAX', 'Agregar Impuestos');
define('TAX_EXEMPT', 'Libre de Impuestos');
define('ARCHIVE_ORDER', 'Archivar Orden');
define('ARCHIVE_MESSAGE', 'No se puede archivar articulos Fuera de Inventario.  Archivar este orden?');
define('COMPLETE_ORDER', 'Procesar Orden');

// Process function strings
define('IN_STORE_ORDER', 'Orden en tienda local');
define('PERSONAL_ID', '  DL# ');
define('OUT_OF_STOCK_EMAIL_SENDER', 'ocPOS@yourdomain.com');
define('OUT_OF_STOCK_EMAIL_SUBJECT', 'Producto no disponible: ');
define('OUT_OF_STOCK_EMAIL_MSG1', 'Orden: ');
define('OUT_OF_STOCK_EMAIL_MSG2', ' compro el ultimo ');
define('OUT_OF_STOCK_EMAIL_MSG3', 'disponible.  Ya no queda mas de este producto en inventario.  El numero del modelo del producto es ');

// Button Titles
define('ADD_ITEM_BUTTON_TITLE', 'Agregar Articulo');
define('NON_INVENTORY_BUTTON_TITLE', 'Agregar Articulo Fuera de Invantario');
define('DISCOUNT_BUTTON_TITLE', 'Dar un descuento a esta Orden');
define('REMOVE_ORDER_BUTTON_TITLE', 'Abandonar esta Orden');
define('DROP_ASSIGNED_CUST_BUTTON_TITLE', 'Despedir Cliende de esta Orden');
define('ADD_COMMENTS_BUTTON_TITLE', 'Agregar/Editar Comentarios');
define('APPLY_TAX_BUTTON_TITLE', 'Cambiar Status de Libre de Impuestos');
define('ARCHIVE_ORDER_BUTTON_TITLE', 'Archivar esta Orden');
define('COMPLETE_ORDER_BUTTON_TITLE', 'Procesar esta Orden');

// Titles of entries written to orders_total database table
define('OT_TITLE_DISCOUNT', 'Descuento:');
define('OT_TITLE_SUBTOTAL', 'Sub-Total:');
define('OT_TITLE_TOTAL', 'Total:');
?>
