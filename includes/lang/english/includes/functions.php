<?php
// includes/lang/english/includes/functions.php


define('FIELD_BLANK', 'Required field left blank:  %s');
define('FIELD_MUST_BE_NUMBER', 'Field must be a number:  %s');
define('NOBODY', '(Nobody)');
define('QUANTITY_EXCEEDED', 'You have tried to increase the quantity of this item beyond the\nrecorded quantity in the database.\n\nIf you wish to add more of this item, you must edit the recorded\nquantity by clicking on its name and selecting:  Edit Product.');

//PrintReceipt function strings
define('INVOICE', 'Invoice #');
define('NOT_PROCESSED', '(Not Processed)');
define('SEPARATOR', '===================================');
define('SUBTOTAL', 'Subtotal');
define('TAX', 'Tax');
define('TOTAL', 'Total');
define('TOTAL_REMAINING', 'Total Remaining');
define('CASH_TENDERED', 'Cash Amt Tendered');
define('CHECK_TENDERED', 'Check Amt Tendered');
define('CHANGE', 'Change Due');
define('THANK_YOU', 'Thank you for shopping at %s!');
define('VISIT_ONLINE', 'Visit us online at %s.');

//Print_Full function strings
define('ADD_ITEM', 'Add Item');
define('DISCOUNT', 'Discount');
define('DISCOUNT_ENTIRE_ORDER', 'Discount Entire Order');
define('MODEL', 'Model #');
define('NAME', 'Product Name');
define('PRICE', 'Price');
define('QUANTITY', 'Quantity');
define('NON_INVENTORY', '(Non-Inventory)');
define('NOT_SET', 'Not Set');   
define('ADJUST_PRICE', 'Adjust Price');
define('ADD_ONE', 'Add 1 to Quantity');
define('SUBTRACT_ONE', 'Subtract 1 from Quantity');
define('ADD_MULT', 'Add Multiple to Quantity');
define('REMOVE', 'Remove From Order');
define('ORDER_TOTAL', 'Total');
define('ITEM_COUNT', '# of Items:');
define('COMMENTS', 'Comments');
define('REMOVE_ORDER', 'Remove Order');
define('DROP_ASSIGNED_CUST', 'Drop Assigned Customer');
define('ADD_COMMENTS', 'Add/Edit Comments');
define('APPLY_TAX', 'Apply Tax');
define('TAX_EXEMPT', 'Tax Exempt');
define('ARCHIVE_ORDER', 'Archive Order');
define('RESTOCK_FEE', 'Restocking Fee');
define('ARCHIVE_MESSAGE', 'Non-Inventory items cannot be archived.  Archive this order?');
define('RESTOCK_MESSAGE', 'Non-Inventory items cannot be returned or exchanged.  Return this order?');
define('SHIPPING_FEE', 'Shipping');
define('COMPLETE_ORDER', 'Complete Order');
define('PARTIAL_PAYMENT', 'Partial Payment');

// Process function strings
define('IN_STORE_ORDER', 'Order placed in store');
define('PERSONAL_ID', '  DL# ');
define('OUT_OF_STOCK_EMAIL_SENDER', 'ocPOS@yourdomain.com');
define('OUT_OF_STOCK_EMAIL_SUBJECT', 'Out of Stock Product: ');
define('OUT_OF_STOCK_EMAIL_MSG1', 'Order: ');
define('OUT_OF_STOCK_EMAIL_MSG2', ' ordered the last ');
define('OUT_OF_STOCK_EMAIL_MSG3', '(s) available.  This product is now out of stock.  The product model number is ');
define('ITEMS_RETURNED_TO_ORDER', 'One or more items from this order have been returned or exchanged.  Refer to Order <a href="order.php?OrderID=%s" target="_blank">%s</a>.');

// Button Titles
define('ADD_ITEM_BUTTON_TITLE', 'Add Item');
define('NON_INVENTORY_BUTTON_TITLE', 'Add Non-Inventory Item');
define('DISCOUNT_BUTTON_TITLE', 'Add Discount to this Order');
define('REMOVE_ORDER_BUTTON_TITLE', 'Remove this Order');
define('DROP_ASSIGNED_CUST_BUTTON_TITLE', 'Drop Customer from this Order');
define('ADD_COMMENTS_BUTTON_TITLE', 'Add/Edit Comments on this Order');
define('APPLY_TAX_BUTTON_TITLE', 'Change Tax Exempt Status for this Order');
define('ARCHIVE_ORDER_BUTTON_TITLE', 'Archive this Order (Save for Later)');
define('COMPLETE_ORDER_BUTTON_TITLE', 'Complete Checkout for this Order');
define('RESTOCK_FEE_BUTTON_TITLE', 'Add a Restocking Fee for this Order');
define('SHIPPING_BUTTON_TITLE', 'Add a Shipping Fee for this Order');

// Titles of entries written to orders_total database table
define('OT_TITLE_DISCOUNT', 'Discount:');
define('OT_TITLE_RESTOCK', 'Restocking Fee:');
define('OT_TITLE_SUBTOTAL', 'Sub-Total:');
define('OT_TITLE_TOTAL', 'Total:');
?>
