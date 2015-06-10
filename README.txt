=============================================
OllaCart Point of Sale 2.5
=============================================
Thank you for downloading OllaCart Point of Sale (ocPOS)!  Soon you'll be using the best integrated Point of Sale solution for your website.


=============================================
System Requirements
=============================================
OllaCart Point of Sale requires PHP, Apache web server and MySQL.  It has been tested with PHP, Apache and MySQL on FreeBSD Unix, Linux and Windows XP.  It has been successfully used with osCommerce, CRELoaded and ZenCart.  It will likely work with other osCommerce variants due to their common database structure.

NOTE: This version uses functions deprecated as of PHP 5.5, mostly mysql vs mysqli stuff.

=============================
Known issues in version 2.5
=============================
Documentation:  
 - Online documentation has not yet been fully updated

Order Processing:  
 - When using the hash mark ( # ) to change an item quantity within an existing order, you can add up to the product total quantity rather than up to the attribute's total stock quantity.
 - Return/Exchange orders:  Taxes are not applied on returned non-inventory items.
 - Return/Exchange orders:  If you try to return an order that has an item or was sold in a size/color that has since been deleted, the "return" order will not be able to restock that item or size properly.  For example, if the item still exists but the size does not, the size will be blank.  If the item no longer exists, then the item will return as a non-inventory item with no size noted.
 - Attributes: If you have two or more attributes (i.e. color and size) defined for the same product, then all values for those options will be made available in the POS.  For example, if you associate Color:Red + Size 2 as well as Color:Blue + Size 1, and only assign QT Pro stock levels for those two combinations, then you will still be able to choose Color:Red + Size 1 and Color:Blue + Size 2 in the POS.  Until a proper fix is released, the best thing is to assign 0 stock to the combinations you don't want sold.
 - Attributes: not completely working in standard osCommerce (non-QT Pro) mode.  Add a product with one attribute, add same product with different attribute, product in-cart quantity is increased rather than adding the same item with a different attribute.
 - Attributes: osC attribute mode not automatically adding attributes to return/exchange orders
 - Tax Rounding:  Sometimes tax is off by a $0.01 due to a rounding error.
 
Product Editing:
 - Editing a product does not allow you to edit stock levels for attributes.
 
Language:  
 - Non-English button titles may be too long in some cases, causing the button to wrap lines and be unattractive.  If this is the case, you can edit the language file to shorten the button title text.  For example, to edit the Spanish button title that appears in product_search.php, edit includes/lang/espanol/product_search.php.
 - Some of the language translation was done using translation tools.  Much of these translations are sub-optimal.  We appreciate corrections, so please feel free to submit them to us.
    
Return orders:  not creating tax array for:
    returned non-inventory items
    returned items that have since been deleted from catalog
    
Reporting: 
    Compounded and additive taxes show up on reporting?  Need to verify.
    Need to fix scale on report_days.php -- shows top level of 100 orders even if only 2 or 3 have been placed on the busiest day.
    Need to clean up report_compare.php and report_compare_range.php -- they should be just one file. Why does report_compare include report_compare_range when report_compare_range could work independently?
    
Returns: 
    use original per-item tax when running AddItem(), not current tax (see how it's done for noninventory section of additem function)
    
Barcodes: optimize fix current barcode logic flow
    product_search.php -> functions.php (additem) -> product.php(instasubmit) -> action.php -> functions.php (additem)
  
Settings: 
    ENABLE_BILLING_SHIPPING_ADDR  = 0 does not seems to disable the ability to use separate billing/shipping addresses
    ocPOS does not yet honor osCommerce "display prices with tax" setting
    
Ought to consolidate language files a bit.  Ex:  No need for "Back" button and its title to be defined in so many pages

Salesperson Tracking: 2.5 also tracks which salesperson (logged in user) completed the order. No reporting is yet being done on this.

Fees: shipping/restocking fees are not affecting tax (both regular and return orders?). Needs verification/investigation.

    
Other misc issues:
    Using legacy mysql libraries rather than mysqli libraries. These work in PHP 5.5 but are deprecated. Need to switch to mysqli functions. 
    Have not verified full functionality for PHP 5.5. Seems to be working just fine, though not thoroughly tested.
    Current Login system uses different database connection logic than rest of code.  Code consolidation needed.
    Login not required for some pages (though this is easy to fix).
    
============================
INSTALLATION
============================
See INSTALL.txt.
