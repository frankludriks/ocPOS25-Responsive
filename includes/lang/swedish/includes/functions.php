<?php
// includes/lang/swedish/includes/functions.php

define('FIELD_BLANK', 'Obligatoriskt f�lt ej ifyllt:  %s');
define('FIELD_MUST_BE_NUMBER', 'Endast nummer f�r ifyllas:  %s');
define('NOBODY', '(Ingen)');
define('QUANTITY_EXCEEDED', 'Du har f�rs�kt att ange ett antal som �vrstiger det antal\nsom finns lagerf�rt p� denna artikel.\n\nOm du vill ange detta antal m�ste du uppdatera lagersaldot\ngenom att klicka p� dess namn och v�lja:  Redigera artikel.');

//PrintReceipt function strings
define('INVOICE', 'Order #');
define('NOT_PROCESSED', '(Ej genomf�rt)');
define('SEPARATOR', '===================================');
define('SUBTOTAL', 'Delsumma');
define('TAX', 'Moms');
define('TOTAL', 'Totalt');
define('CASH_TENDERED', 'Kontantbetalning');
define('CHECK_TENDERED', 'Checkbetalning');
define('CHANGE', 'V�xel �ter till kund');
define('THANK_YOU', 'Tack f�r att du handlar hos %s!');
define('VISIT_ONLINE', 'Bes�k oss online p� %s.');

//Print_Full function strings
define('ADD_ITEM', 'L�gg till artikel');
define('DISCOUNT', 'Rabatt');
define('DISCOUNT_ENTIRE_ORDER', 'Rabattera hela ordersumman');
define('MODEL', 'Modell');
define('NAME', 'Artikelnamn');
define('PRICE', 'Pris');
define('QUANTITY', 'Antal');
define('NON_INVENTORY', '(Ej lagerf�rd)');
define('NOT_SET', 'Inte satt');  
define('ADJUST_PRICE', '�ndra pris');
define('ADD_ONE', '�ka antalet med 1');
define('SUBTRACT_ONE', 'Minsta antalet med 1');
define('ADD_MULT', 'Ange antal (positivt eller negativt)');
define('REMOVE', 'Radera fr�n order');
define('ORDER_TOTAL', 'Totalt');
define('ITEM_COUNT', '# artiklar:');
define('COMMENTS', 'Kommentarer');
define('REMOVE_ORDER', 'Radera order');
define('DROP_ASSIGNED_CUST', 'Ta bort kund fr�n order');
define('ADD_COMMENTS', 'L�gg till/�ndra kommentarer');
define('APPLY_TAX', 'Med moms');
define('TAX_EXEMPT', 'Utan moms');
define('ARCHIVE_ORDER', 'Arkivera order');
define('ARCHIVE_MESSAGE', 'Icke-inventarier kan inte arkiveras. Arkivera denna best�llning?');
define('COMPLETE_ORDER', 'Slutf�r order');

// Process function strings
define('IN_STORE_ORDER', 'Ordrar lagda i butik');
define('PERSONAL_ID', '  Identifiering# ');
define('OUT_OF_STOCK_EMAIL_SENDER', 'adress@dindom�n.se');
define('OUT_OF_STOCK_EMAIL_SUBJECT', 'Produkt sluts�ld: ');
define('OUT_OF_STOCK_EMAIL_MSG1', 'Order: ');
define('OUT_OF_STOCK_EMAIL_MSG2', ' senast best�lld ');
define('OUT_OF_STOCK_EMAIL_MSG3', '(s) tillg�ngliga.  Denna artikel �r nu slut p� lager.  Artikelns modellnummer �r ');

// Button Titles
define('ADD_ITEM_BUTTON_TITLE', 'L�gg till artikel');
define('NON_INVENTORY_BUTTON_TITLE', 'L�gg till ej lagerf�rd artikel');
define('DISCOUNT_BUTTON_TITLE', 'Rabattera denna order');
define('REMOVE_ORDER_BUTTON_TITLE', 'Radera denna order');
define('DROP_ASSIGNED_CUST_BUTTON_TITLE', 'Ta bort kund fr�n denna order');
define('ADD_COMMENTS_BUTTON_TITLE', 'L�gg till/�ndra kommentarer');
define('APPLY_TAX_BUTTON_TITLE', 'Ta bort moms fr�n denna order');
define('ARCHIVE_ORDER_BUTTON_TITLE', 'Arkivera denna order (spara till senare)');
define('COMPLETE_ORDER_BUTTON_TITLE', 'Slutf�r denna order');

// Titles of entries written to orders_total database table
define('OT_TITLE_DISCOUNT', 'Rabatt:');
define('OT_TITLE_SUBTOTAL', 'Delsumma:');
define('OT_TITLE_TOTAL', 'Totalt:');
?>
