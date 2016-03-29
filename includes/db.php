<?php
// includes/db.php



//	DATABASE SETTINGS
//  =================================================

$DB_Username                                    =   "username";
$DB_Password                                    =   "password";
$DB_Server                                      =   "localhost";
$DB_Name                                        =   "database";

// which language directory to include for ocPOS UI
$lang                                           =   'english';

// database table prefix (if you are not sure leave it blank)
$table_prefix                                   =   ''; 

// number of results to return from search pages
$maximum_search_results                         =   '30';
$maximum_customer_search_results                =   '90';

// Authorize.Net settings
// Note that the Authorize.Net module uses the AIM integration, not the Card Present integration.

// $auth_net_api_login_id                       =  "CHANGEME_AUTHNET_LOGIN_ID";  // only required if you have an authorize.net account for credit card processing
// $auth_net_transaction_key                    =  "CHANGEME_AUTHNET_TRANSACTION_KEY";  // only required if you have an authorize.net account for credit card processing
// $auth_net_test_mode                             =   "1"; // set to 1 to use the Authorize.Net TEST server


define('APPLICATION_NAME','OllaCart Point of Sale');
define('APPLICATION_VERSION','2.5');
define('APPLICATION_LOGO_IMAGE','logo.gif');
define('STORE_EMAIL', 'you@yourdomain.com');


//	OCPOS OPTIONAL SETTINGS
//  =================================================

//	Customer settings
//  =================================================
define('DEFAULT_CUSTOMER_FIRST_NAME', 'In-Store');
define('DEFAULT_CUSTOMER_LAST_NAME', 'Customer');
	// Values:  Whatever you like
	// Default = 'In-Store' and 'Customer'
	// This is the name that will be used during ocPOS checkouts if no customer name has been associated to the order.  If there is no customer by this name in the database, one will be automatically created
    
define('ENABLE_BILLING_SHIPPING_ADDR', '0');
	// Values:  0 or 1
	// Default = 0 
	// Set this to 1 to enable separate billing and shipping addresses when checking out customers.
    // If set to 0, the customer's default address will be used for both shipping and billing addresses
    
define('AUTOCAP_NAMES', '1');
    // Values:  0 or 1
    // Default = 1
	// Set this to 1 to enable auto-capitalization of names when creating new customers
    // For example, creating a new customer named "john doe" would be autocapitalized to "John Doe"
	
define('AUTO_CREATE_PASSWORDS', '0');
	// Values:  0 or 1
	// Default = 0 
	// Set this to 1 to automatically create website passwords for  new customer accounts as they are created in ocPOS
	
define('EMAIL_NEW_CUSTOMER' ,'0');
	// Values:  0 or 1
	// Default = 0
	// Set this to 1 if you would like new customers to be emailed when they are created via ocPOS


//	Search settings
//  =================================================
define('SEARCH_TERM', 'ALL'); 
	// values: ALL or ANY.  
	// Default = 'ALL'
	// Set this to ALL if you want the product search to look for products with ALL of the search terms in the product name.  Set this to ANY if you want the product search to look for products with ANY of the search terms in the product name.

define('EXACT_MODEL_MATCH', '0'); 
	// Values: 0 or 1 
	// Default = 0
	// Set this to 1 if you want product searches to return partial matches for product names but only exact matches for product model numbers.  Set this to 0 if you want partial matches for both product names and product model numbers.
    

//	Order settings
//  =================================================
define('ENABLE_OSC_INVOICE_LINK', '0');
	// Values:  0 or 1
	// Default = 0 
	// Set this to 1 to enable separate billing and shipping addresses when checking out customers.
    // If set to 0, the customer's default address will be used for both shipping and billing addresses

define('OSC_INVOICE_PATH', '/admin/invoice.php');
	// Values:  Varies depending on your webstore installation
	// Default = '/admin/invoice.php'
	// This is the URL path to the invoice for osC, Zen, CRE, etc.  /admin/invoice.php is a common value. 
    // This setting only matters if ENABLE_OSC_INVOICE_LINK is set to 1.

define('COMPLETED_ORDER_STATUS', 'Completed In Store');
	// Values:  Whatever you like
	// Default = 'Completed In Store'
	// This is the order status name that will be assigned to an order checked out using ocPOS.  If there is no order status by this name in the database, one will be automatically created.
    // If "Completed In Store" is changed to some other value, an order status with that name will be automatically created the next time ocPOS is accessed.

define('VOIDED_ORDER_STATUS', 'Voided In Store');
	// Values:  Whatever you like
	// Default = 'Voided In Store'
	// This is the order status name that will be assigned to an order when you void it in ocPOS. out using ocPOS.  If there is no order status by this name in the database, one will be automatically created.
    
define('NON_INVENTORY_RETURNS_ALLOWED', '0');
    // Values:  0 or 1
    // Default = 0 
    // Set this to 1 to allow non-inventory items to be automatically included when creating return/exchange orders from existing orders.
    // Even when disabled, non-inventory items may still be manually added to a return/exchange order.
    // Note that multi-rate taxes cannot be determined properly when returning non-inventory items, so more than one tax rate is in use, don't enable this option.

define('IN_STORE_PRICING', '0'); 
	// Values: 0 or 1 
	// Default = 0 
	// Set this to 1 if you want to apply a percentage price change to in-store sales.  When enabled, editing product prices and product "special" prices with ocPOS is disabled.
	
define('IN_STORE_SURCHARGE', '0'); 
    // Values:  Range from -.99 to .99 
	// Default = 0 
    // Example: a setting of .15 will give a 15% increase to all in-store purchases.  -.10 will give a 10% discount to all in-store purchases. 

define('DELETE_ORDERS', '0'); 
	// Values:  0 or 1
	// Set this to 1 to use the "Void" button to delete an order completely and return items to inventory.
	// Set this to 0 to use the "Void" button to mark an order as void and return items to inventory.  If this option is used, then the potential exists to go delete the order from osCommerce Admin and re-return the items to inventory.  If voided order are deleted in osCommerce Admin, the items should NOT be returned to inventory for a second time (since ocPOS already returned those items to inventory).


//	Report settings
//  =================================================
define('IN_STORE_ONLY', '0');
    // values: 0 or 1
    // Default = 1
    // Set this to 0 to include all orders in the order history report, even if they were not placed in-store


//  Misc settings
//  =================================================
define('OUT_OF_STOCK_EMAIL', '0');
	// Values:  0 or 1
	// Default = 0
	// Set this to 1 if you would like the store administrator to be emailed when a product becomes sold out through ocPOS.
   // NOTE that this email is only sent if "Allow Checkout" is set to FALSE in the web store admin.  In osCommerce, this setting is in Admin >> Configuration >> Stock >> Allow Checkout
	


//	Product listing settings
//  =================================================
define('SHOW_PRODUCT_DESCRIPTION', '1');
    // values: 0 or 1
    // Default = 1
    // Display product description in product.php 
    
define('REVERSE_PRODUCT_LISTING', '0');
    // values: 0 or 1
    // default = 0
    // Set this to 1 if you want to reverse the order in which products are displayed in the order summary.  
    // If this is set to 0, then the newest item added to the order will be displayed in the LAST item in the order.
    // If this is set to 1, then the newest item added to the order will be displayed as the FIRST item in the order.
    // This setting can be useful if the store processes orders with many items in them, where the orders scroll off the screen each time an item is added to the order.

define('LISTPERPAGE', '50');
    // Values: an integer
    // Default: 50
    // Number of orders to list per page on order_history.php

define('SHOW_PRODUCT_IMAGE', '1');
    // values: 0 or 1
    // Default = 1
    // Display product image in product.php 

define('IMAGE_PATH', '/images/products/');
        // Values:  Varies depending on your webstore installation
        // Default = '/images/'
        // This is the URL path to the product image directory for osC, Zen, CRE, etc.  /images/ is a common value. 
    // This setting only matters if SHOW_PRODUCT_IMAGE is set to 1.
    
define('USE_PRODUCT_BARCODE', '0');
    // values:  0 or 1
    // Default = 0
    // Set this to 1 if you have added a UPC or barcode field to your products table and want to use it for product searches.

define('PRODUCT_BARCODE_FIELD', 'products_barcode');
    // values: Whatever the name of the UPC/barcode field is in your products table
    // Default = products_barcode
    // Set this to the name of the barcode field in your products table.  Typically this field is named products_upc or products_barcode
    // This field is only used if USE_PRODUCT_BARCODE is set to 1

define('ALLOW_DISABLED_PRODUCTS', '0'); 
	// Values: 0 or 1
	// Default:  0
	// Set this to 1 if you want to be able to sell disabled products.
	
define('ALLOW_SOLDOUT_PRODUCTS', '1'); 
	// Values: 0 or 1
	// Default:  0
	// Set this to 1 if you want to be able to sell products with quantities of 0 or below


//	Product Attribute settings
//  =================================================
define('OSC_ATTRIBUTES_MODE','NONE');
    // Values: 'NONE','OSC','QTP'
    // Default: 'OSC'
    // This determines if and how product attributes are supported. 
    //    NONE = no attribute support
    //    OSC  = standard osCommerce attribute support
    //    QTP  = QTpro attribute support (Must have QTpro feature installed in osC product)

define('NUM_ATTRIB_OPTIONS_PER_CONFIG_ROW',3);
    // Values: 1 to 4
    // Default = 3
    // This determines how many options appear per row on the product edit screen
    // End of Product Attributes options

    
//	Time Zone Settings
//  =================================================
// see http://www.php.net/manual/en/timezones.php for additional global Time Zone settings.
// see http://www.statoids.com/tus.html for additional Time Zone settings within the United States of America.

// Uncomment 2 lines of code below to set a timezone for OllaCart Point of Sale
// Note that if you're doing this here, you may want to do the same thing to your osCommerce installation.

// @setlocale(LC_TIME, 'en_US.ISO_8859-1');
// putenv("TZ=America/Boise"); // Mountain Time
// putenv("TZ=America/Los_Angeles"); // Pacific Time
// putenv("TZ=America/Chicago"); // Central Time
// putenv("TZ=America/New_York"); // Eastern Time



// USER CONTROL SYSTEM
define('DB_USER', $DB_Username);
define('DB_PASSWORD', $DB_Password);
define('DB_SERVER', $DB_Server);
define('DB_NAME', $DB_Name); 

/**
 * Special Names and Level Constants - the admin
 * page will only be accessible to the user with
 * the admin name and also to those users at the
 * admin user level. Feel free to change the names
 * and level constants as you see fit, you may
 * also add additional level specifications.
 * Levels must be digits between 0-9.
 */
define("ADMIN_NAME", "admin");
define("ADMIN_LEVEL", 9);
define("USER_LEVEL",  1);


/**
 * Timeout Constants - these constants refer to
 * the maximum amount of time (in minutes) after
 * their last page fresh that a user is still 
 * considered.
 */
define("USER_TIMEOUT", 1);

/**
 * Cookie Constants - these are the parameters
 * to the setcookie function call, change them
 * if necessary to fit your website. If you need
 * help, visit www.php.net for more info.
 * <http://www.php.net/manual/en/function.setcookie.php>
 */
define("COOKIE_EXPIRE", 60*60*24*100);  //100 days by default
define("COOKIE_PATH", "/");  //Avaible in whole domain

/**
 * Email Constants - these specify what goes in
 * the from field in the emails that the script
 * sends to users, and whether to send a
 * welcome email to newly registered users.
 */
define("EMAIL_FROM_NAME", "Ollacart Point of Sale");
define("EMAIL_FROM_ADDR", "no_reply@yourdomainname.com");
define("EMAIL_WELCOME", false);

/**
 * This constant forces all users to have
 * lowercase usernames, capital letters are
 * converted automatically.
 */
define("ALL_LOWERCASE", false);

// minimum user length
define('MIN_USER_LEN', 4);
define('MIN_PASS_LEN', 4);
define('MAX_USER_LEN', 30);
//


//	No editing is generally needed below this line. 
//  =================================================

require 'db_tables.php';

if (file_exists("includes/lang/$lang/includes/functions.php")) {
	include("includes/lang/$lang/includes/functions.php");
}

global $DATABASE;
$DATABASE = @mysql_connect($DB_Server, $DB_Username, $DB_Password) or die("Could not connect to the database!  Please verify database settings in includes/db.php.");
$db_selected = mysql_select_db($DB_Name);
// mysql_connect($DB_Server, $DB_Username, $DB_Password) or die("Could not connect to the database!  Please verify database settings in includes/db.php.");
// mysql_select_db($DB_Name);


// If attributes mode is set to QTP, check for products_stock table.
// QT Pro add-on creates products_stock table. If products_stock does not exist, QT Pro cannot function properly.
$qtp_tablecheck = mysql_query("SHOW TABLES LIKE '" . PRODUCTS_STOCK . "'");

if( (mysql_num_rows($qtp_tablecheck) == 0) && OSC_ATTRIBUTES_MODE == 'QTP') {
    die("<b>ERROR: </b>QT Pro attribute mode is enabled but does not appear to be installed.<br><br> Install QT Pro or edit includes/db.php and select a different OSC_ATTRIBUTES_MODE.");
}

$debug = 0;

?>
