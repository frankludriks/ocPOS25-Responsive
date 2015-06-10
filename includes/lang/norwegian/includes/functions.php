<?php
// includes/lang/norwegian/includes/functions.php

define('FIELD_BLANK', 'P�krevet felt er blankt:  %s');
define('FIELD_MUST_BE_NUMBER', 'Feltet m� v�re ett nummer:  %s');
define('NOBODY', '(Uregistrert)');
define('QUANTITY_EXCEEDED', 'Du har fors�kt � sammenlegge mer enn det anvendelig parti. \nHvis du �nske � sammenlegge flere av denne artikkel, \ndu m� redigere produktet og forh�ye anvendelig parti.');

//PrintReceipt function strings
define('INVOICE', 'Kvitteringsnummer');
define('NOT_PROCESSED', '(Ikke Bearbeidet)');
define('SEPARATOR', '===================================');
define('SUBTOTAL', 'Subtotal');
define('TAX', 'MVA');
define('TOTAL', 'Total');
define('CASH_TENDERED', 'Kontantbel�p mottatt');
define('CHECK_TENDERED', 'Sjekkbel�p mottatt');
define('CHANGE', 'Veksel');
define('THANK_YOU', 'Tusen takk for at du handlet hos %s!');
define('VISIT_ONLINE', 'Bes�k oss gjerne p� %s.');

//Print_Full function strings
define('ADD_ITEM', 'Legg til Produkt');
define('DISCOUNT', 'Diskonto');
define('DISCOUNT_ENTIRE_ORDER', 'Diskonto Hel Ordre');
define('MODEL', 'Modellnummer');
define('NAME', 'Produktnavn');
define('PRICE', 'Pris');
define('QUANTITY', 'Antall');
define('NON_INVENTORY', '(Ikke p� Lager)');
define('NOT_SET', 'Ikke angitt');  
define('ADJUST_PRICE', 'Rediger Pris');
define('ADD_ONE', 'Legg til 1');
define('SUBTRACT_ONE', 'Trekk fra 1');
define('ADD_MULT', 'Endre Antall i Ordren');
define('REMOVE', 'Slett fra Ordre');
define('ORDER_TOTAL', 'Total');
define('ITEM_COUNT', 'antall produkter:');
define('COMMENTS', 'Kommentarer');
define('REMOVE_ORDER', 'Slett Ordre');
define('DROP_ASSIGNED_CUST', 'Fjern valgte kunde');
define('ADD_COMMENTS', 'Legg til Kommentarer');
define('APPLY_TAX', 'Legg p� MVA');
define('TAX_EXEMPT', 'Uten MVA');
define('ARCHIVE_ORDER', 'Lagre Ordre');
define('ARCHIVE_MESSAGE', 'Ikke-lagervarer kan ikke arkiveres. Arkiver denne bestillingen?');
define('COMPLETE_ORDER', 'Fullf�r Ordre');

// Process function strings
define('IN_STORE_ORDER', 'Inne Lager Ordre');
define('PERSONAL_ID', '  Legitimasjonen # ');
define('OUT_OF_STOCK_EMAIL_SENDER', 'ocPOS@yourdomain.com');
define('OUT_OF_STOCK_EMAIL_SUBJECT', 'Ikke p� lager produkt: ');
define('OUT_OF_STOCK_EMAIL_MSG1', 'Ordre: ');
define('OUT_OF_STOCK_EMAIL_MSG2', ' kj�pt det vare ');
define('OUT_OF_STOCK_EMAIL_MSG3', 'disponibel.  Denne produkt er n� ikke p� lager.  Fabrikat modell ');

// Button Titles
define('ADD_ITEM_BUTTON_TITLE', 'Legg til Ordren');
define('NON_INVENTORY_BUTTON_TITLE', 'Add ikke p� lager produkt');
define('DISCOUNT_BUTTON_TITLE', 'Sammenlegge Diskonto');
define('REMOVE_ORDER_BUTTON_TITLE', 'Fjerne Denne Ordre');
define('DROP_ASSIGNED_CUST_BUTTON_TITLE', 'Fjerne Kunden fra her Ordre');
define('ADD_COMMENTS_BUTTON_TITLE', 'Sammenlegge / Redigere Kommentarer p� denne Ordre');
define('APPLY_TAX_BUTTON_TITLE', 'Endre Skattefritt Rang for denne Ordre');
define('ARCHIVE_ORDER_BUTTON_TITLE', 'Arkivet denne Ordre (bevare for Siden)');
define('COMPLETE_ORDER_BUTTON_TITLE', 'Slutten Sjekk for denne Ordre');

// Titles of entries written to orders_total database table
define('OT_TITLE_DISCOUNT', 'Diskonto:');
define('OT_TITLE_SUBTOTAL', 'Subtotal:');
define('OT_TITLE_TOTAL', 'Total:');
?>
