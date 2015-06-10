<?php
// includes/lang/swedish/includes/functions.php

define('FIELD_BLANK', 'Obligatoriskt fält ej ifyllt:  %s');
define('FIELD_MUST_BE_NUMBER', 'Endast nummer får ifyllas:  %s');
define('NOBODY', '(Ingen)');
define('QUANTITY_EXCEEDED', 'Du har försökt att ange ett antal som övrstiger det antal\nsom finns lagerfört på denna artikel.\n\nOm du vill ange detta antal måste du uppdatera lagersaldot\ngenom att klicka på dess namn och välja:  Redigera artikel.');

//PrintReceipt function strings
define('INVOICE', 'Order #');
define('NOT_PROCESSED', '(Ej genomfört)');
define('SEPARATOR', '===================================');
define('SUBTOTAL', 'Delsumma');
define('TAX', 'Moms');
define('TOTAL', 'Totalt');
define('CASH_TENDERED', 'Kontantbetalning');
define('CHECK_TENDERED', 'Checkbetalning');
define('CHANGE', 'Växel åter till kund');
define('THANK_YOU', 'Tack för att du handlar hos %s!');
define('VISIT_ONLINE', 'Besök oss online på %s.');

//Print_Full function strings
define('ADD_ITEM', 'Lägg till artikel');
define('DISCOUNT', 'Rabatt');
define('DISCOUNT_ENTIRE_ORDER', 'Rabattera hela ordersumman');
define('MODEL', 'Modell');
define('NAME', 'Artikelnamn');
define('PRICE', 'Pris');
define('QUANTITY', 'Antal');
define('NON_INVENTORY', '(Ej lagerförd)');
define('NOT_SET', 'Inte satt');  
define('ADJUST_PRICE', 'Ändra pris');
define('ADD_ONE', 'Öka antalet med 1');
define('SUBTRACT_ONE', 'Minsta antalet med 1');
define('ADD_MULT', 'Ange antal (positivt eller negativt)');
define('REMOVE', 'Radera från order');
define('ORDER_TOTAL', 'Totalt');
define('ITEM_COUNT', '# artiklar:');
define('COMMENTS', 'Kommentarer');
define('REMOVE_ORDER', 'Radera order');
define('DROP_ASSIGNED_CUST', 'Ta bort kund från order');
define('ADD_COMMENTS', 'Lägg till/ändra kommentarer');
define('APPLY_TAX', 'Med moms');
define('TAX_EXEMPT', 'Utan moms');
define('ARCHIVE_ORDER', 'Arkivera order');
define('ARCHIVE_MESSAGE', 'Icke-inventarier kan inte arkiveras. Arkivera denna beställning?');
define('COMPLETE_ORDER', 'Slutför order');

// Process function strings
define('IN_STORE_ORDER', 'Ordrar lagda i butik');
define('PERSONAL_ID', '  Identifiering# ');
define('OUT_OF_STOCK_EMAIL_SENDER', 'adress@dindomän.se');
define('OUT_OF_STOCK_EMAIL_SUBJECT', 'Produkt slutsåld: ');
define('OUT_OF_STOCK_EMAIL_MSG1', 'Order: ');
define('OUT_OF_STOCK_EMAIL_MSG2', ' senast beställd ');
define('OUT_OF_STOCK_EMAIL_MSG3', '(s) tillgängliga.  Denna artikel är nu slut på lager.  Artikelns modellnummer är ');

// Button Titles
define('ADD_ITEM_BUTTON_TITLE', 'Lägg till artikel');
define('NON_INVENTORY_BUTTON_TITLE', 'Lägg till ej lagerförd artikel');
define('DISCOUNT_BUTTON_TITLE', 'Rabattera denna order');
define('REMOVE_ORDER_BUTTON_TITLE', 'Radera denna order');
define('DROP_ASSIGNED_CUST_BUTTON_TITLE', 'Ta bort kund från denna order');
define('ADD_COMMENTS_BUTTON_TITLE', 'Lägg till/ändra kommentarer');
define('APPLY_TAX_BUTTON_TITLE', 'Ta bort moms från denna order');
define('ARCHIVE_ORDER_BUTTON_TITLE', 'Arkivera denna order (spara till senare)');
define('COMPLETE_ORDER_BUTTON_TITLE', 'Slutför denna order');

// Titles of entries written to orders_total database table
define('OT_TITLE_DISCOUNT', 'Rabatt:');
define('OT_TITLE_SUBTOTAL', 'Delsumma:');
define('OT_TITLE_TOTAL', 'Totalt:');
?>
