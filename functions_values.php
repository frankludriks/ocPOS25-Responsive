<?php
// includes/functions_values.php

// Report all errors except E_NOTICE
//error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

function get_default_lang() {
    $lang_config_query = mysql_query("SELECT configuration_value FROM " . CONFIGURATION . " WHERE configuration_key = 'DEFAULT_LANGUAGE'");
    $lang_config_results = mysql_fetch_array($lang_config_query);
    $lang_config_value = $lang_config_results['configuration_value'];

    $lang_query = mysql_query("SELECT languages_id FROM " . LANGUAGES . " WHERE code = '" . $lang_config_value . "'");
    $lang_query_results = mysql_fetch_array($lang_query);
    $language_id = $lang_query_results['languages_id'];
    
    return $language_id;
}

// the following used to be in includes/db.php.  This section initializes some data
if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . CUSTOMERS . "'")) == 0) {
    $failed = true;
}

if (!isset($failed)) {

    // get the store address for receipts
    $StoreAddress_query = mysql_query("select configuration_value from " . CONFIGURATION . " where configuration_key = 'STORE_NAME_ADDRESS'");
    $StoreAddress_results = mysql_fetch_array($StoreAddress_query);
    $StoreAddress = $StoreAddress_results[0];

    // get store name for receipts, page titles
    // can override this value and reduce overhead by commenting out the next 3 lines then adding a new line that just defines the $StoreName variable
    // like this:  $StoreName = 'My Store';
    // leave the $POSName line there, do not comment it out
    $StoreName_query = mysql_query("select configuration_value from " . CONFIGURATION . " where configuration_key = 'STORE_NAME'");
    $StoreName_results = mysql_fetch_array($StoreName_query);
    $StoreName = $StoreName_results[0];
    $POSName = $StoreName . ' - OllaCart POS';

    // get store administrator email address for out of stock emails
    // can override this value and reduce overhead by commenting out the next 3 lines then adding a new line that just defines the $StoreOwner variable
    // like this:  $StoreOwner = 'emailme@mydomain.com';
    // $StoreOwner_query = mysql_query("select configuration_value from " . CONFIGURATION . " where configuration_key = 'STORE_OWNER_EMAIL_ADDRESS'");
    // $StoreOwner_results = mysql_fetch_array($StoreOwner_query);
    // $StoreOwner = $StoreOwner_results[0];

    $StoreWebsite = $_SERVER['HTTP_HOST'];
       
    // get default currency code
    // can override this value and reduce overhead by commenting out the next 8 lines then adding a new line that just defines the $default_currency_symbol variable
    // like this:  $default_currency_symbol = '£';   OR    $default_currency_symbol = '$';
    $default_currency_query_raw = "select configuration_value from " . CONFIGURATION . " where configuration_key = 'DEFAULT_CURRENCY'";
    $default_currency_query = mysql_query($default_currency_query_raw);
    $default_currency_results = mysql_fetch_array($default_currency_query);
    $default_currency_code = $default_currency_results[0];

    // get default currency symbol that is associated with the default currency code
    $default_currency_symbol_query_raw = "select symbol_left from " . CURRENCIES . " where code = '" . $default_currency_code . "'";

    $default_currency_symbol_query = mysql_query($default_currency_symbol_query_raw);
    $default_currency_symbol_results = mysql_fetch_array($default_currency_symbol_query);
    $default_currency_symbol = $default_currency_symbol_results['symbol_left']; 

    // get default customer id
    $default_customer_query_raw = "SELECT customers_id FROM " . CUSTOMERS. " WHERE customers_firstname= '" . DEFAULT_CUSTOMER_FIRST_NAME . "' and customers_lastname = '" . DEFAULT_CUSTOMER_LAST_NAME . "'";
    $default_customer_query = mysql_query($default_customer_query_raw);
    $default_customer_results = mysql_fetch_array($default_customer_query);

    $num_results = mysql_num_rows($default_customer_query);

    // If no default customer exists that matches the current default customer configuration, create an account for the default customer
    // NOTE: As the default customer name is changed, ocPOS will create a new one to match the new config if needed.
    if ($num_results > 0) {
    // existing customer found to use as default customer
        $default_customer_id = $default_customer_results['customers_id'];
    } else {
    // create new default customer
        $country_query = mysql_query("SELECT configuration_value from " . CONFIGURATION . " WHERE
            configuration_key = 'STORE_COUNTRY'");
        $default_country_results = mysql_fetch_array($country_query);
        $default_country_id = $default_country_results['configuration_value'];
        
        $zone_query = mysql_query("SELECT configuration_value from " . CONFIGURATION . " WHERE
            configuration_key = 'STORE_ZONE'");
        $default_zone_results = mysql_fetch_array($zone_query);
        $default_zone_id = $default_zone_results['configuration_value'];
        
        mysql_query("INSERT INTO " . CUSTOMERS . " SET
            customers_firstname='" . DEFAULT_CUSTOMER_FIRST_NAME . "',
            customers_lastname='" . DEFAULT_CUSTOMER_LAST_NAME . "'
        ");
        
        $default_customer_id = mysql_insert_id();
        
        mysql_query("INSERT INTO " . ADDRESS_BOOK . " SET
            customers_id='$default_customer_id',
            entry_firstname='" . DEFAULT_CUSTOMER_FIRST_NAME . "',
            entry_lastname='" . DEFAULT_CUSTOMER_LAST_NAME . "',
            entry_country_id = '$default_country_id',
            entry_zone_id = '$default_zone_id'
        ");
        
        $default_addrbook_id = mysql_insert_id();
        
        mysql_query("UPDATE " . CUSTOMERS . " SET
            customers_default_address_id='$default_addrbook_id'
            WHERE customers_id = '$default_customer_id'	
            ");
            

        mysql_query("INSERT INTO " . CUSTOMERS_INFO . " SET
            customers_info_id='$default_customer_id',
            customers_info_date_account_created=now();
        ");
    }


    // These variables are set here in case you want to override them.
    
    // language for the ocPOS interface - this is used to write order status values to the database in the correct language
    $default_lang = get_default_lang();
    
    // "shop" or product language 
    $language_id = get_default_lang();
    
    // get completed order status id
    $completed_status_query_raw = "SELECT orders_status_id FROM " . ORDERS_STATUS. " WHERE orders_status_name = '" . COMPLETED_ORDER_STATUS . "' AND language_id = '" . $default_lang . "'";
    $completed_status_query = mysql_query($completed_status_query_raw);
    $completed_order_status_results = mysql_fetch_array($completed_status_query);

    $num_results = mysql_num_rows($completed_status_query);
    if ($num_results > 0) {
        $completed_order_status_id = $completed_order_status_results['orders_status_id'];
    } else {
    // if the default order status does not exist as configured above, create that default order status
    // NOTE: As the default order status name is changed, ocPOS will create a new one to match the new config if needed.
        $max_status_query = mysql_query("SELECT max(orders_status_id) as maxid from " . ORDERS_STATUS . " ");
        $max_status_result = mysql_fetch_array($max_status_query);
        $max_status = $max_status_result['maxid'] + 1;

    // create new default status
        mysql_query("INSERT INTO " . ORDERS_STATUS . " SET
        language_id = '" . $default_lang . "', 
        orders_status_id = '" . $max_status . "', 
        orders_status_name = '" . COMPLETED_ORDER_STATUS . "'");

        $completed_order_status_id = mysql_insert_id();
    }

    // get voided order status id
    $voided_status_query_raw = "select orders_status_id from " . ORDERS_STATUS. " where orders_status_name = '" . VOIDED_ORDER_STATUS . "' AND language_id = '" . $default_lang . "'";
    $voided_status_query = mysql_query($voided_status_query_raw);
    $voided_order_status_results = mysql_fetch_array($voided_status_query);

    $num_results = mysql_num_rows($voided_status_query);
    if ($num_results > 0) {
        $voided_order_status_id = $voided_order_status_results['orders_status_id'];
    } else {
    // if the default order status does not exist as configured above, create that default order status
    // NOTE: As the default order status name is changed, ocPOS will create a new one to match the new config if needed.
        $max_status_query = mysql_query("SELECT max(orders_status_id) as maxid from " . ORDERS_STATUS . " ");
        $max_status_result = mysql_fetch_array($max_status_query);
        $max_status = $max_status_result['maxid'] + 1;

    // create new default status
        mysql_query("INSERT INTO " . ORDERS_STATUS . " SET
        language_id = '" . $default_lang . "', 
        orders_status_id = '" . $max_status . "', 
        orders_status_name = '" . VOIDED_ORDER_STATUS . "'");

        $voided_order_status_id = mysql_insert_id();
    }
}
?>