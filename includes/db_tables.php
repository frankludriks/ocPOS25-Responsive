<?php
// includes/db_tables.php


// The database definitions below match the default osCommerce tables.  
// Modification of this list is only suggested if you are using table prefixes and have not set that in db.php for some strange reason


define('ADDRESS_BOOK', $table_prefix . 'address_book');
define('CATEGORIES', $table_prefix . 'categories');
define('CATEGORIES_DESCRIPTION', $table_prefix . 'categories_description');
define('CONFIGURATION', $table_prefix . 'configuration');
define('COUNTRIES', $table_prefix . 'countries');
define('CURRENCIES', $table_prefix . 'currencies');
define('CUSTOMERS', $table_prefix . 'customers');
define('CUSTOMERS_INFO', $table_prefix . 'customers_info');
define('LANGUAGES', $table_prefix . 'languages');
define('ORDERS', $table_prefix . 'orders');
define('ORDERS_STATUS', $table_prefix . 'orders_status');
define('ORDERS_STATUS_HISTORY', $table_prefix . 'orders_status_history');
define('ORDERS_PRODUCTS', $table_prefix . 'orders_products');
define('ORDERS_PRODUCTS_ATTRIBUTES', $table_prefix . 'orders_products_attributes');
define('ORDERS_TOTAL', $table_prefix . 'orders_total');
define('POS_ORDERS', $table_prefix . 'pos_orders');
define('POS_ORDERS_PRODUCTS', $table_prefix . 'pos_orders_products');
define('PRODUCTS', $table_prefix . 'products');
define('PRODUCTS_DESCRIPTION', $table_prefix . 'products_description');
define('PRODUCTS_TO_CATEGORIES', $table_prefix . 'products_to_categories');
define('SPECIALS', $table_prefix . 'specials');
define('TAX_RATES', $table_prefix . 'tax_rates');
define('TAX_CLASS', $table_prefix . 'tax_class');
define('ZONES', $table_prefix . 'zones');
define('GEO_ZONES', $table_prefix . 'geo_zones');
define('ZONES_TO_GEO_ZONES', $table_prefix . 'zones_to_geo_zones');

define('POS_ORDERS_PRODUCTS_ATTRIBUTES', $table_prefix . 'pos_orders_products_attributes');
define('PRODUCTS_ATTRIBUTES', $table_prefix . 'products_attributes');
define('PRODUCTS_OPTIONS',$table_prefix.'products_options');
define('PRODUCTS_OPTIONS_VALUES',$table_prefix.'products_options_values');
define('OPTIONS_DEFINITION',$table_prefix.'products_options_values_to_products_options');
define('PRODUCTS_STOCK',$table_prefix.'products_stock');

define('POS_USERS', $table_prefix . 'pos_users');
define('POS_USERS_ACTIVE', $table_prefix . 'pos_users_active');

?>
