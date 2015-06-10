<?php
// includes/lang/french/includes/functions.php


define('FIELD_BLANK', 'Champ obligatoire non complété:  %s');
define('FIELD_MUST_BE_NUMBER', 'Ce champ est numérique:  %s');
define('NOBODY', '(Nobody)');
define('QUANTITY_EXCEEDED', 'Vous avez voulu accroitre la quantité de ce produit au delà de\nla quantité enregistrée dans la base de données.\n\nSi vous souhaitez ajouter plus de ce produit, vous devez modifier la quantité\nenregistrée en cliquant sur son nom et en choisissant:  Modifier Produit.');

//PrintReceipt function strings
define('INVOICE', 'Facture #');
define('NOT_PROCESSED', '(Non Traité)');
define('SEPARATOR', '===================================');
define('SUBTOTAL', 'Soustotal');
define('TAX', 'Taxe');
define('TOTAL', 'Total');
define('CASH_TENDERED', 'Montant reçu Cash');
define('CHECK_TENDERED', 'Montant du chèque ');
define('CHANGE', 'à rembourser');
define('THANK_YOU', 'Merci d\'avoir effectué vos achats chez %s!');
define('VISIT_ONLINE', 'Visitez notre site web à %s.');

//Print_Full function strings
define('ADD_ITEM', 'Ajouter Article');
define('DISCOUNT', 'Discount');
define('DISCOUNT_ENTIRE_ORDER', 'Remise globle sur la Commande');
define('MODEL', 'Modèle #');
define('NAME', 'Nom Produit');
define('PRICE', 'Prix');
define('QUANTITY', 'Quantité');
define('NON_INVENTORY', '(Hors-Inventaire)');
define('NOT_SET', 'Non définie');  
define('ADJUST_PRICE', 'Modifier Prix');
define('ADD_ONE', 'Ajout 1 à Quantité');
define('SUBTRACT_ONE', 'Soustraire 1 de Quantité');
define('ADD_MULT', 'Ajout Multiple à Quantité');
define('REMOVE', 'Retirer de la Commande');
define('ORDER_TOTAL', 'Total');
define('ITEM_COUNT', '# d\'articles:');
define('COMMENTS', 'Commentaires');
define('REMOVE_ORDER', 'Retirer Commande');
define('DROP_ASSIGNED_CUST', 'Retirer le Client');
define('ADD_COMMENTS', 'Ajout/Modif Comment.');
define('APPLY_TAX', 'Appliquer Taxe');
define('TAX_EXEMPT', 'Exempt. Taxe');
define('ARCHIVE_ORDER', 'Archiver Commande');
define('ARCHIVE_MESSAGE', 'Les éléments non-inventaire ne peut être archivé. Archives cet ordre?');
define('COMPLETE_ORDER', 'Enregistrer la Commande');

// Process function strings
define('IN_STORE_ORDER', 'Commande enregistrée en Magasin');
define('PERSONAL_ID', '  DL# ');
define('OUT_OF_STOCK_EMAIL_SENDER', 'ivenegef@gmail.com');
define('OUT_OF_STOCK_EMAIL_SUBJECT', 'Produit en rupture de Stock: ');
define('OUT_OF_STOCK_EMAIL_MSG1', 'Commande: ');
define('OUT_OF_STOCK_EMAIL_MSG2', ' commandé les derniers ');
define('OUT_OF_STOCK_EMAIL_MSG3', '(s) disponible.  Ce produit est maintenant en rupture de stock.  Le numéro de modèle est le ');

// Button Titles
define('ADD_ITEM_BUTTON_TITLE', 'Ajouter article');
define('NON_INVENTORY_BUTTON_TITLE', 'Ajouter un article Hors-Inventaire');
define('DISCOUNT_BUTTON_TITLE', 'Ajouter une ristourne à cette commande');
define('REMOVE_ORDER_BUTTON_TITLE', 'Retirer cette Commande');
define('DROP_ASSIGNED_CUST_BUTTON_TITLE', 'Retirer le Client de cette Commande');
define('ADD_COMMENTS_BUTTON_TITLE', 'Ajouter/Modifiet Commentaires de cette Commande');
define('APPLY_TAX_BUTTON_TITLE', 'Modifier le statut Exempt/Taxe de cette Commande');
define('ARCHIVE_ORDER_BUTTON_TITLE', 'Archiver cette Commande (Sauvegarder pour plus tard)');
define('COMPLETE_ORDER_BUTTON_TITLE', 'Enregistrer complètement cette Commande');

// Titles of entries written to orders_total database table
define('OT_TITLE_DISCOUNT', 'Discount:');
define('OT_TITLE_SUBTOTAL', 'Sous-Total:');
define('OT_TITLE_TOTAL', 'Total:');
?>
