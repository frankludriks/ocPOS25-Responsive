<?php
// includes/functions.php


// Set reporting level based on debug switch
if (isset($debug) && $debug == 1) {
    error_reporting(E_STRICT); // As of PHP 5.4, E_STRICT is part of E_ALL
} else {
    error_reporting(E_ALL ^ E_NOTICE);
}

function change_pol($somenum) {
    if (!is_numeric($somenum))
        return false;
    return (0 - $somenum);
} // end function change_pol

if (!function_exists('oc_query')) {

    function oc_query($query_sql, $description = '', $dbconn = 'link') {
        global $DATABASE;

        $message = "SQL Error";
        if (strlen($description) > 1)
            $message .= ' in ' . $description;
        $result = mysql_query($query_sql, $DATABASE) or die($message . "<br><br>Query: $query_sql" . "<br><br>Error: " . mysql_errno($DATABASE) . ": " . mysql_error($DATABASE));
        return $result;
    }
} // end function oc_query

function LoadLangFiles($lang = '') {
    $basename = basename($_SERVER['SCRIPT_NAME']);
    if (file_exists("includes/lang/$lang/$basename")) {
        include("includes/lang/$lang/$basename");
    }
    if (file_exists("includes/lang/$lang/common.php")) {
        include("includes/lang/$lang/common.php");
    }
} // end LoadLangFiles


if (!function_exists('use_attribs')) {

    function use_attribs() {
        return (OSC_ATTRIBUTES_MODE != 'NONE');
    }
} // end use_attribs


// if attributes are enabled, load attribute support
if (OSC_ATTRIBUTES_MODE != "NONE")
    include_once("includes/attributes.php");

//
//  Check Required for input vars
//
function TestFormInput($ShowMsg, $ReqVars) {
    $error_count = 0;
    while (list($var, $val) = each($ReqVars)) {
        $val = trim($val);
        if (empty($val)) {
            $error_count++;
            if ($ShowMsg) {
                printf('<span class="error"><li>' . FIELD_BLANK . '</li></span>', $var);
            } else {
                break;
            }
        }
        if (($var == 'Price') || ($var == 'Quantity')) {
            if (!is_numeric($val)) {
                $error_count++;
                if ($ShowMsg) {
                    printf('<li>' . FIELD_MUST_BE_NUMBER . '</li>', $var);
                } else {
                    break;
                }
            }
        }
    }
    if ($error_count) {
        return false;
    } else {
        return true;
    }
} // end function TestFormInput

// format spaces and quotes
function StripArraySlashes($Array) {
    while (list($var, $val) = each($Array)) {
        $Array[$var] = str_replace("\'", "'", $Array[$var]);
        $Array[$var] = str_replace("\"", "'", $Array[$var]);
        $Array[$var] = str_replace("\\", "", $Array[$var]);
    }
    return $Array;
} // end function StripArraySlashes


function mysql_fetch_decode_assoc($Query) {
    $Array = mysql_fetch_assoc($Query);
    if ($Array) {
        while (list($var, $val) = each($Array)) {
            $Array[$var] = str_replace("  ", " &nbsp;", $Array[$var]);
            $Array[$var] = str_replace("\'", "'", $Array[$var]);
            $Array[$var] = str_replace("\"", "'", $Array[$var]);
        }
    }
    return $Array;
} // end function mysql_fetch_decode_assoc


//  Trunc string to length
function TruncString($string, $size) {
    if (strlen($string) > $size) {
        $string = substr($string, 0, $size - 3) . "..";
    }
    return $string;
} // end function TruncString


//	add new order
function NewOrder($CustomerID) {
    $_SESSION['Orders'][$_SESSION['NextOrderIndex']] = new Order($CustomerID);
    $_SESSION['CurrentOrderIndex'] = $_SESSION['NextOrderIndex'];
    $_SESSION['NextOrderIndex']++;
} // end function NewOrder

function ItemNewOrder($CustomerID, $ProductID) {
    $_SESSION['Orders'][$_SESSION['NextOrderIndex']] = new Order($CustomerID);
    $_SESSION['CurrentOrderIndex'] = $_SESSION['NextOrderIndex'];
    $_SESSION['NextOrderIndex']++;
    $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->AddItem($ProductID, 1, IN_STORE_PRICING);
} // end function ItemNewOrder


//	remove order
function RemoveOrder() {
    if ($_SESSION['CurrentOrderIndex'] != -1) {
        $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']] = NULL;
        $_SESSION['CurrentOrderIndex'] = -1;
    }
} // end function RemoveOrder

function MailCustomerReceipt($order_id, $customer_email) {

// make db.php settings to choose whether or not to use database email "from" settings (same thing with to/from for out of stock emails)
    // get store's email address
    // $StoreOwner_query = mysql_query("SELECT configuration_value FROM " . CONFIGURATION . " WHERE configuration_key = 'STORE_OWNER_EMAIL_ADDRESS'");
    $StoreOwnerEmail_query = mysql_query("SELECT configuration_value FROM " . CONFIGURATION . " WHERE configuration_key = 'EMAIL_FROM'");
    $StoreOwnerEmail_results = mysql_fetch_array($StoreOwnerEmail_query);
    $StoreOwnerEmail = $StoreOwnerEmail_results['configuration_value'];

    $StoreName_query = mysql_query("SELECT configuration_value FROM " . CONFIGURATION . " WHERE configuration_key = 'STORE_NAME'");
    $StoreName_results = mysql_fetch_array($StoreName_query);
    $StoreName = $StoreName_results['configuration_value'];

    $StoreNameAddress_query = mysql_query("SELECT configuration_value FROM " . CONFIGURATION . " WHERE configuration_key = 'STORE_NAME_ADDRESS'");
    $StoreNameAddress_results = mysql_fetch_array($StoreNameAddress_query);
    $StoreNameAddress = $StoreNameAddress_results['configuration_value'];


    $to = $customer_email;
    $subject = 'ocPOS: Thank you for your purchase';

    // Set HTML Mail Header
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: \"" . $StoreName . "\"<" . $StoreOwnerEmail . ">" . "\r\n";

    // use db.php settings for email sender and recipient
    // $headers  = "From: \"OllaCart Point of Sale\"<" . OUT_OF_STOCK_EMAIL_SENDER . ">" . "\r\n";
    // $headers  = "From: " . OUT_OF_STOCK_EMAIL_SENDER . "\r\n";
    // $to = STORE_EMAIL;
    // $message = 'Thank you for buying from us.  Your order number was ' . $order_id . '.';

    $message = 'test message';

    // Fix any bare linefeeds in the message to make it RFC821 Compliant.
    $message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);

    // Make sure there are no bare linefeeds in the headers
    $headers = preg_replace('#(?<!\r)\n#si', "\r\n", $headers);

    // $message = normalize($message);
    // mail($to, $subject, $message, $headers, "-f$StoreOwnerEmail");
    $result = mail($to, $subject, $message, $headers);
    return $result;
}

//
//	get archived order
//
function GetArchivedOrder($OrderID) {

    function make_seed() {
        list($usec, $sec) = explode(' ', microtime());
        return (float) $sec + ((float) $usec * 100000);
    }

    srand(make_seed());
    $Q_Order = mysql_query("SELECT * FROM " . POS_ORDERS . " WHERE pos_orders_id='" . $OrderID . "'");
    if ($R_Order = mysql_fetch_decode_assoc($Q_Order)) {
        // create order
        $_SESSION['Orders'][$_SESSION['NextOrderIndex']] = new Order($CustomerID);
        $_SESSION['CurrentOrderIndex'] = $_SESSION['NextOrderIndex'];
        $_SESSION['NextOrderIndex']++;
        $CurrentOrderIndex = $_SESSION['CurrentOrderIndex'];

        $_SESSION['Orders'][$CurrentOrderIndex]->PostTime = $R_Order['post_time'];
        $_SESSION['Orders'][$CurrentOrderIndex]->CustomerID = $R_Order['customers_id'];
        $_SESSION['Orders'][$CurrentOrderIndex]->Total = $R_Order['total'];
        $_SESSION['Orders'][$CurrentOrderIndex]->TaxExempt = $R_Order['tax_exempt'];
        $_SESSION['Orders'][$CurrentOrderIndex]->Comments = $R_Order['comments'];

        $Q_OrderProduct = mysql_query("SELECT * FROM " . POS_ORDERS_PRODUCTS . " WHERE pos_orders_id='" . $OrderID . "' ORDER BY products_model");
        while ($R_OrderProduct = mysql_fetch_decode_assoc($Q_OrderProduct)) {
            if ($R_OrderProduct['non_inventory'] == 0) {
                // this is a regular inventoried item
                $_POST['product_base_price'] = $R_OrderProduct['products_price'];
                if (use_attribs) {
                    $_GET['product_stock_attributes'] = $R_OrderProduct['products_stock_attributes'];
                } else {
                    $_GET['product_stock_attributes'] = '';
                    $R_OrderProduct['products_stock_attributes'] = '';
                }
                $_POST['Price'] = $R_OrderProduct['products_price'];

                if ($R_OrderProduct['tax_rate']) {
                    $taxexempt = 0;
                } else {
                    $taxexempt = 1;
                }

                $Index = $_SESSION['Orders'][$CurrentOrderIndex]->AddItem($R_OrderProduct['products_id'], $R_OrderProduct['products_quantity'], IN_STORE_PRICING, $R_OrderProduct['products_stock_attributes'], $OrderID);
                // set price if overide set
                if ($R_OrderProduct['price_overide']) {
                    $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SetItemPrice($Index, $R_OrderProduct['products_price']);
                }
                // non-inventory item
            } else {
                $_POST['products_model'] = $R_OrderProduct['products_model'];
                $_POST['products_name'] = $R_OrderProduct['products_name'];
                $_POST['products_price'] = $R_OrderProduct['products_price'];
                $_POST['tax_rate'] = $R_OrderProduct['tax_rate'];
                $_POST['products_quantity'] = $R_OrderProduct['products_quantity'];
                $_POST['order_type'] = "non_inventory";
                $Index = $_SESSION['Orders'][$CurrentOrderIndex]->AddItem($R_OrderProduct['products_id'], $R_OrderProduct['products_quantity']);

                if ($R_OrderProduct['price_overide']) {
                    $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SetItemPrice($Index, $R_OrderProduct['products_price']);
                }
            }
        }

        $_SESSION['Orders'][$CurrentOrderIndex]->Update();

        mysql_query("DELETE FROM " . POS_ORDERS . " WHERE pos_orders_id='" . $OrderID . "'");
        mysql_query("DELETE FROM " . POS_ORDERS_PRODUCTS . " WHERE pos_orders_id='" . $OrderID . "'");
        if (use_attribs)
            mysql_query("DELETE FROM " . POS_ORDERS_PRODUCTS_ATTRIBUTES . " WHERE pos_orders_id='" . $OrderID . "'");
    }
}

// end function GetArchivedOrder

function GetStoreZoneID() {
    $store_zone_sql = "SELECT configuration_value FROM " . CONFIGURATION . " WHERE configuration_key = 'STORE_ZONE'";
    $store_zone_query = mysql_query($store_zone_sql) or die("SQL Error.  Store zone lookup failure.  <br><br>SQL=$store_zone_sql");
    $store_lookup_result = mysql_fetch_array($store_zone_query);

    return $store_lookup_result['configuration_value'];
}

// end function GetStoreZoneID

function GetProductTaxClassID($item_id) {
    $prod_tax_id_sql = "SELECT p.products_tax_class_id FROM " . PRODUCTS . " p WHERE p.products_id=$item_id";
    $prod_tax_id_query = mysql_query($prod_tax_id_sql) or die("SQL Error. Tax class lookup failure.  <br><br>SQL=$prod_tax_id_sql");
    $prod_tax_id_result = mysql_fetch_array($prod_tax_id_query);
    return $prod_tax_id_result['tax_class_id'];
}

// end function GetProductTaxClassID

function GetTax($product_id) {

# This function needs to return a product's tax rate(s) and description(s).
# For tax totaling reasons (think HST/GST/PST), we need to return an array
#   with each tax_rates.tax_rate and tax_rates.tax_description that apply to this product
#   sort taxes by priority

    $store_zone_id = GetStoreZoneID();

    if ($product_id > 1000000000) {
        $tax_lookup_sql = "SELECT tr.tax_rate, tr.tax_description, tax_priority
                 FROM " . TAX_RATES . " tr
                 JOIN " . ZONES_TO_GEO_ZONES . " z2g on z2g.geo_zone_id=tr.tax_zone_id
                 JOIN " . ZONES . " z on z.zone_id=z2g.zone_id
                 WHERE z.zone_id = '" . $store_zone_id . "'
                 ORDER BY tax_priority ASC";
    } else {
        $tax_lookup_sql = "SELECT tr.tax_rate, tr.tax_description, tax_priority
                 FROM " . PRODUCTS . " p
                 JOIN " . TAX_RATES . " tr on p.products_tax_class_id=tr.tax_class_id
                 JOIN " . ZONES_TO_GEO_ZONES . " z2g on z2g.geo_zone_id=tr.tax_zone_id
                 JOIN " . ZONES . " z on z.zone_id=z2g.zone_id
                 WHERE z.zone_id = '" . $store_zone_id . "' and p.products_id = '" . $product_id . "'
                 ORDER BY tax_priority ASC";
    }

    $tax_lookup_query = oc_query($tax_lookup_sql, 'SQL Error. Tax detail lookup failure.');

    $i = 0;
    $tax_array = array();

    while ($tax_lookup_result = mysql_fetch_array($tax_lookup_query)) {
        $tax_array[$i]['tax_description'] = $tax_lookup_result['tax_description'];
        $tax_array[$i]['tax_rate'] = $tax_lookup_result['tax_rate'];
        $tax_array[$i]['tax_priority'] = $tax_lookup_result['tax_priority'];
        // $tax_array[$i]['tax_total'] = 0;
        $i++;
    }

    return $tax_array;
}

// end function GetTax

function GetSortOrder($order_total_type = '') {
    $order_total_sql = "select configuration_value from " . CONFIGURATION . " where configuration_key = '" . $order_total_type . "'";
    $order_total_query = mysql_query($order_total_sql) or die("SQL Error. Sort Order lookup failure.  <br><br>SQL=$order_total_sql");
    $order_total = mysql_fetch_array($order_total_query);
    return $order_total['configuration_value'];
}

// end function GetSortOrder

function ProcessOrder() {
    if ($_SESSION['CurrentOrderIndex'] != -1) {
        $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Process();
    }
}

// end function ProcessOrder
// Order class

class Order {

    var $PostTime;
    var $OrderID;
    var $CustomerID;
    var $BillingID;
    var $ShippingID;
    var $Items;
    var $NextItemIndex;
    var $SubTotal;
    var $Total;
    var $Tax;
    var $TaxExempt;
    var $Cash;
    var $Check;
    var $cc_type;
    var $cc_number;
    var $cc_last4;
    var $cc_expires;
    var $Comments;
    var $PersonalID;
    var $DiscountMethod;
    var $DiscountValue;
    var $CalculatedPercentDiscount;
    var $PaymentMethod;
    var $PaymentTransactionID;
    var $ReturnOrder;
    var $ShippingMethod;
    var $ShippingValue;
    var $RestockMethod;
    var $RestockValue;
    var $SplitPayments;

    function Order($CustomerID) {
        $this->PostTime = time();
        $this->OrderID = NULL;
        $this->CustomerID = $CustomerID;
        $this->BillingID = '';
        $this->ShippingID = '';
        $this->Items = array();
        $this->NextItemIndex = 0;
        $this->SubTotal = 0;
        $this->Total = 0;
        $this->Tax = 0;
        $this->TaxExempt = false;
        $this->Cash = 0;
        $this->Check = 0;
        $this->cc_type = NULL;
        $this->cc_number = NULL;
        $this->cc_last4 = '';
        $this->cc_expires = 0;
        $this->Comments = "";
        $this->DiscountMethod = '';
        $this->DiscountValue = 0;
        $this->CalculatedPercentDiscount = 0;
        $this->PaymentMethod = '';
        $this->PaymentTransactionID = '';
        $this->ReturnOrder = 0;
        $this->ShippingMethod = '';
        $this->ShippingValue = '';
        $this->RestockMethod = '';
        $this->RestockValue = '';
        $this->SplitPayments = array();
    }

// end function Order

    function SetDiscount($discount_method, $discount_value) {
        $this->DiscountMethod = $discount_method;
        $this->DiscountValue = $discount_value;
        $this->Update();
    }

// end function SetDiscount

    function AddItem($ProductID, $Quantity, $in_store_pricing = 0, $attribute_str = '', $order_id = 0) {
        reset($this->Items);
        $QUANTITY_EXCEED = false;
        if (!$attribute_str)
            $attribute_str = $_REQUEST['product_stock_attributes'];

        global $language_id;  // If this value is not present, the attribute string values will not load correctly
        $language_id = get_default_lang();

        //  Load attributes for specified product (and order, if provided) to order item
        //   -- QTP-only: Also loads per-attribute stock values here
        if (OSC_ATTRIBUTES_MODE != 'NONE') {
            $R_Attribs = new attributes($ProductID, $language_id, $attribute_str, $order_id);
            $R_Products_Attributes = $R_Attribs->add_item();
            if (is_attrib_mode("QTP")) {
                $R_Config_Stock = $R_Attribs->get_stock_attrib();
            }

            // if there are attributes, go to product page rather than adding a null-option item to the order
            if (count($R_Products_Attributes) > 0) {
                if ($this->ReturnOrder == 0) {
                    if (!isset($_POST['product_base_price']) && !isset($_GET['product_stock_attributes'])) {
                        header('Location: ' . 'product.php?ProductID=' . $ProductID);
                        continue;
                    }
                }
            }
        }
/* 
        $QuantityQ = "SELECT p.products_id, p.products_quantity, p.products_model, p.products_price, " .
                "pd.products_name, s.specials_new_products_price, " .
                "if ((s.specials_new_products_price is not NULL), s.specials_new_products_price, p.products_price) as sales_price " .
                " FROM " . PRODUCTS . " p " .
                " LEFT JOIN " . SPECIALS . " s ON (p.products_id = s.products_id) AND (s.status='1'), " .
                PRODUCTS_DESCRIPTION . " pd " .
                " WHERE p.products_id = '" . $ProductID . "' AND pd.products_id = p.products_id" .
                " AND ((s.expires_date >= '" . date("Y-m-d") . "') OR (s.expires_date LIKE '0001-01-01%') OR (s.expires_date LIKE '0000-00-00%') OR (s.expires_date IS NULL) ) AND pd.language_id = '" . $language_id . "' ";
*/
        $QuantityQ = "SELECT p.products_id, p.products_quantity, p.products_model, p.products_price, " .
                "pd.products_name, s.specials_new_products_price, " .
                "if ((s.specials_new_products_price is not NULL AND ((s.expires_date >= '" . date("Y-m-d") . "') OR (s.expires_date LIKE '0001-01-01%') OR (s.expires_date LIKE '0000-00-00%') OR (s.expires_date IS NULL) ) ), s.specials_new_products_price, p.products_price) as sales_price " .
                " FROM " . PRODUCTS . " p " .
                " LEFT JOIN " . SPECIALS . " s ON (p.products_id = s.products_id) AND (s.status='1'), " .
                PRODUCTS_DESCRIPTION . " pd " .
                " WHERE p.products_id = '" . $ProductID . "' AND pd.products_id = p.products_id" .
                "  AND pd.language_id = '" . $language_id . "' ";

        // if ALLOW_DISABLED_PRODUCTS == 0, then cannot add an item to the cart unless the in-stock quantity is over zero, BUT
        // need to allow return/exchange orders to return items even if the current in-stock quantity is zero
        // in other words, if this is a return order, require that the product be enabled (products_status = 1)
        if (ALLOW_DISABLED_PRODUCTS == '0' && $this->ReturnOrder == 0) {
            $QuantityQ .= " AND p.products_status = '1' ";
        }

        // if allow_soldout_products == 0, then cannot add an item to the cart unless the in-stock quantity is over zero, BUT
        // need to allow return/exchange orders to return items even if the current in-stock quantity is zero
        // in other words, if this is a return order, require that the product have stock(products_quantity > 1)
        if (ALLOW_SOLDOUT_PRODUCTS == '0' && $this->ReturnOrder == 0) {
            $QuantityQ .= " AND p.products_quantity > 0 ";
        }

        $QuantityQ .= " GROUP BY p.products_id";

        $Q_Product = mysql_query($QuantityQ) or die("SQL Error. Product lookup failure adding to cart.  <br><br>SQL=$QuantityQ");

        // ADD INVENTORY ITEM
        if ($R_Product = mysql_fetch_assoc($Q_Product)) {
            $INCART = false;
            // if regular item
            while (list ($key, $val) = each($this->Items)) {
                if (is_array($val) && $val['ProductID'] == $R_Product['products_id']) {

                    //  -- QTP-only: Where specific options have been selected,
                    //        reflect the stock quantity for that configuration
                    if (OSC_ATTRIBUTES_MODE != 'NONE' && $val['ProductAttributes'] == $R_Config_Stock) {
                        if (is_attrib_mode("QTP")) {
                            if (isset($_REQUEST['StockQuantity'])) {
                                $R_Product['products_quantity'] = $_REQUEST['StockQuantity'];
                            } else {
                                $R_Product['products_quantity'] = $R_Attribs->get_stock_quantity();
                            }
                        }
                    }

                    $R_Product['products_quantity'] -= $this->Items[$key]['Quantity'];
                    if ((ALLOW_DISABLED_PRODUCTS == '0') || (ALLOW_SOLDOUT_PRODUCTS == '0')) {
                        if (($Quantity > $R_Product['products_quantity']) && ($this->ReturnOrder == 0)) {
                            // quantity downsized
                            $Quantity = $R_Product['products_quantity'];
                            $QUANTITY_EXCEED = true;
                            $this->Items[$key]['QuantityExceed'] = $QUANTITY_EXCEED;
                        }
                    }
                    $this->Items[$key]['Quantity'] += $Quantity;
                    $INCART = true;
                    break;
                }
            }

            // if not in cart
            if (!$INCART) {

                //  -- QTP-only: Where specific options have been selected,
                //        reflect the stock quantity for that configuration
                if (OSC_ATTRIBUTES_MODE != 'NONE') {
                    if (is_attrib_mode('QTP')) {
                        if ($_REQUEST['StockQuantity']) {
                            $R_Product['products_quantity'] = $_REQUEST['StockQuantity'];
                        } else {
                            $R_Product['products_quantity'] = $R_Attribs->get_stock_quantity();
                        }
                    }
                }

                if ((ALLOW_DISABLED_PRODUCTS == '0') || (ALLOW_SOLDOUT_PRODUCTS == '0')) {
                    // if we're trying to add more to the cart than is available in stock,
                    // change the add-to-cart qty to the qty currently in stock
                    // (unless this is a return order)
                    if (($Quantity > $R_Product['products_quantity']) && ($this->ReturnOrder == 0)) {
                        // quantity downsized
                        $Quantity = $R_Product['products_quantity'];

                        // if negative quantity in stock, change qty adding to cart to 1
                        if ($Quantity < 0)
                            $Quantity = 1;

                        $QUANTITY_EXCEED = true;
                    }
                }

                $this->Items[$this->NextItemIndex] = array();
                $this->Items[$this->NextItemIndex]['ProductID'] = $R_Product['products_id'];
                $this->Items[$this->NextItemIndex]['ProductModel'] = $R_Product['products_model'];
                $this->Items[$this->NextItemIndex]['ProductName'] = $R_Product['products_name'];
                if ($in_store_pricing == 1) {
                    $R_Product['products_price'] += ($R_Product['products_price'] * IN_STORE_SURCHARGE);
                    $R_Product['specials_new_products_price'] += ($R_Product['specials_new_products_price'] * IN_STORE_SURCHARGE);
                    $R_Product['sales_price'] += ($R_Product['sales_price'] * IN_STORE_SURCHARGE);
                }

                $this->Items[$this->NextItemIndex]['ProductPrice'] = $R_Product['products_price'];
                $this->Items[$this->NextItemIndex]['SpecialPrice'] = $R_Product['specials_new_products_price'];
                $this->Items[$this->NextItemIndex]['Price'] = $R_Product['sales_price'];
                if (!$this->TaxExempt) {
                    $this->Items[$this->NextItemIndex]['Tax'] = GetTax($R_Product['products_id']);
                } else {
                    $this->Items[$this->NextItemIndex]['Tax'] = 0;
                }
                $this->Items[$this->NextItemIndex]['TaxClassId'] = GetProductTaxClassID($R_Product['products_id']);
                $this->Items[$this->NextItemIndex]['PriceOveride'] = false;
                $this->Items[$this->NextItemIndex]['NonInventory'] = false;
                $this->Items[$this->NextItemIndex]['QuantityExceed'] = $QUANTITY_EXCEED;
                $this->Items[$this->NextItemIndex]['Quantity'] = $Quantity;

                //  Attribute information is stored to order item object
                if (OSC_ATTRIBUTES_MODE != 'NONE') {
                    //  Product-specific configuration is added to order item object
                    $this->Items[$this->NextItemIndex]['Attributes'] = $R_Products_Attributes;

                    //  This opportunity is taken to adjust prices as per configuration
                    //if ( (isset($_REQUEST['Price']) && ($_REQUEST['Price']>0)) ) {
                    if (isset($_REQUEST['Price'])) {
                        //  Update price and stock attribute configuration
                        $this->Items[$this->NextItemIndex]['Price'] = $_REQUEST['Price'];
                        $this->Items[$this->NextItemIndex]['ProductAttributes'] = $attribute_str;
                    } else if ($R_Attribs->order_id > 0 || $this->ReturnOrder) {
                        $this->Items[$this->NextItemIndex]['ProductAttributes'] = $R_Attribs->attribute_str;
                        $this->Items[$this->NextItemIndex]['Price'] += $R_Attribs->get_price_adj();
                    }
                }

                $this->NextItemIndex++;
            }

        // ADD NON-INVENTORY ITEM
        // } elseif (($ProductID > 1000000000) || ($this->ReturnOrder)) {
        } elseif (($ProductID > 1000000000) || ($ProductID < -1000000000)) {
            // if this is not a product in a return order, OR it is a product in a return order, but not one created automatically from clicking the "Return/Exchange" button
            if (!($this->ReturnOrder) || (NON_INVENTORY_RETURNS_ALLOWED || $ProductID > 1000000000)) { // do not add non-inventory items if this is a return order unless option is enabled
                $INCART = false;
                while (list ($key, $val) = each($this->Items)) {
                    if (is_array($val) && $val['ProductID'] == $ProductID) {
                        $this->Items[$key]['Quantity'] += $Quantity;
                        $INCART = true;
                        break;
                    }
                }

                if ($ProductID < -1000000000) { // if non-inventory return, use rate from orders_products rather than construct tax array dynamically
                    $tax_array = array();
                    $i = 0;
                    $j = 0;
                    $k = 0;
                    foreach ($_POST as $key => $value) {
                        if (substr($key, 0, 8) == 'tax_rate' && strlen($key) <= 10) { //  add tax_rate0, tax_rate1, etc from product_noninventory.php form
                            //echo($key . ': ' . $value . '<br>');
                            $tax_array[$i]['tax_rate'] = $value;
                            $i++;
                        }
                        if (substr($key, 0, 15) == 'tax_description' && strlen($key) <= 17) { //  add tax_description0, tax_description1, etc from product_noninventory.php form
                            //echo($key . ': ' . $value . '<br>');
                            $tax_array[$j]['tax_description'] = $value;
                            $j++;
                        }
                        if (substr($key, 0, 12) == 'tax_priority' && strlen($key) <= 14) { //  add tax_priority0, tax_priority1, etc from product_noninventory.php form
                            //echo($key . ': ' . $value . '<br>');
                            $tax_array[$k]['tax_priority'] = $value;
                            $k++;
                        }
                    }
                } else {
                    $tax_array = GetTax(abs($ProductID));
                }

                if ($ProductID < -1000000000) {  // if we're returning a noninventory item, look up the price at which it was sold
                    $Q_NonInventory = mysql_query("SELECT products_model, products_name, products_price, products_tax, products_quantity FROM " . ORDERS_PRODUCTS . " WHERE orders_id = '" . $this->ReturnOrder . "' AND products_id = $ProductID");
                    $R_NonInventory = mysql_fetch_array($Q_NonInventory);
                    $non_inventory_model = $R_NonInventory['products_model'];
                    $non_inventory_name = $R_NonInventory['products_name'];
                    $non_inventory_price = $R_NonInventory['products_price'];
                    // $non_inventory_tax_rate = $R_NonInventory['products_tax'];
                    $non_inventory_quantity = -$R_NonInventory['products_quantity']; 

                // If $this->ReturnOrder = 1, then this is a brand new "return" order, not linked to a previous order.
                // We will get the price from the non-inventory form rather than from some previous order
                } elseif ($this->ReturnOrder == 1) {
                    $non_inventory_model = $_POST['products_model'];
                    $non_inventory_name = $_POST['products_name'];
                    $non_inventory_price = $_POST['products_price'];
                    $non_inventory_tax_rate = $tax_array;
                    $non_inventory_quantity = -$_POST['products_quantity'];
                } else {
                    $non_inventory_model = $_POST['products_model'];
                    $non_inventory_name = $_POST['products_name'];
                    $non_inventory_price = $_POST['products_price'];
                    $non_inventory_tax_rate = $tax_array;
                    $non_inventory_quantity = $_POST['products_quantity'];
                }

                if (!$INCART) {
                    $this->Items[$this->NextItemIndex] = array();
                    $this->Items[$this->NextItemIndex]['ProductID'] = $ProductID;
                    $this->Items[$this->NextItemIndex]['ProductModel'] = $non_inventory_model;
                    $this->Items[$this->NextItemIndex]['ProductName'] = $non_inventory_name;
                    $this->Items[$this->NextItemIndex]['ProductPrice'] = $non_inventory_price;
                    $this->Items[$this->NextItemIndex]['SpecialPrice'] = $R_Product['specials_new_products_price'];
                    $this->Items[$this->NextItemIndex]['Price'] = $non_inventory_price;
                    // $this->Items[$this->NextItemIndex]['Tax'] = $non_inventory_tax_rate;
                    $this->Items[$this->NextItemIndex]['Tax'] = $tax_array;
                    $this->Items[$this->NextItemIndex]['PriceOveride'] = true;
                    $this->Items[$this->NextItemIndex]['NonInventory'] = true;
                    $this->Items[$this->NextItemIndex]['QuantityExceed'] = $QUANTITY_EXCEED;
                    $this->Items[$this->NextItemIndex]['Quantity'] = $non_inventory_quantity;
                    $this->NextItemIndex++;
                }
            }
        } // end elseif 

        $this->Update();

        return $this->NextItemIndex - 1;
    }

// end function AddItem

    function AddCartItem($Index, $Quantity) {
        // check for negative index
        if ($Index < 0) {
            return 0;
        }
        // update quantity
        if (is_array($this->Items[$Index])) {
            $this->Items[$Index]['Quantity'] += $Quantity;

            if ($this->Items[$Index]['Quantity'] == 0) {
                $this->Items[$Index] = NULL;
            }
            // update
            $this->Update();
        }
    }

// end function AddCartItem

    function RemoveItem($Index, $Quantity) {
        // check for negative index
        if ($Index < 0) {
            return 0;
        }
        // update quantity
        if (is_array($this->Items[$Index])) {
            $this->Items[$Index]['Quantity'] -= $Quantity;

            if ($this->Items[$Index]['Quantity'] == 0) {
                $this->Items[$Index] = NULL;
            }
            // update
            $this->Update();
        }
    }

// end function RemoveItem

    function DeleteItem($Index) {
        // check for negative index
        if ($Index < 0) {
            return 0;
        }
        $this->Items[$Index] = NULL;
        // update
        $this->Update();
    }

// end function DeleteItem

    function SetItemQuantity($Index, $Quantity) {
        $QUANTITY_EXCEED = false;
        // check for negative index
        if ($Index < 0) {
            return 0;
        }
        // set price
        if (is_array($this->Items[$Index])) {
            $ProductID = $this->Items[$Index]['ProductID'];
            // check num available
            $Q_Product = mysql_query("SELECT products_quantity FROM " . PRODUCTS . " WHERE products_id = '" . $ProductID . "'");
            if (mysql_num_rows($Q_Product)) {
                $R_Product = mysql_fetch_assoc($Q_Product);
            }

            // if attributes and in QTP mode, use attribute quantity, not item quantity
            if (OSC_ATTRIBUTES_MODE != 'NONE') {
                global $language_id;  // If this value is not present, the attribute string values will not load correctly
                $language_id = get_default_lang();
                $R_Attribs = new attributes($ProductID, $language_id, $attribute_str, $order_id);
                if (is_attrib_mode("QTP")) {
                    $R_Product['products_quantity'] = $R_Attribs->get_stock_quantity();
                }
            }

            if ($Quantity > $R_Product['products_quantity']) {
                // quantity downsized
                if ($ProductID > 1000000000) { // is this a non-inventory item?
                    $QUANTITY_EXCEED = false;  // if so, assume sufficient quantity
                } elseif (ALLOW_SOLDOUT_PRODUCTS == '0') {
                    $Quantity = $R_Product['products_quantity'];
                    $QUANTITY_EXCEED = true;
                }
            }

            $this->Items[$Index]['Quantity'] = $Quantity;
            $this->Items[$Index]['QuantityExceed'] = $QUANTITY_EXCEED;
            // update
            $this->Update();
        }
    }

// end function SetItemQuantity

    function SetItemPrice($Index, $Price) {
        // check for negative index
        if ($Index < 0) {
            return 0;
        }
        // set price
        if (is_array($this->Items[$Index])) {
            $this->Items[$Index]['Price'] = $Price;
            $this->Items[$Index]['PriceOveride'] = true;
            // update
            $this->Update();
        }
    }

// end function SetItemPrice
    //PY: Support discount by percentage..
    function SetItemPriceByPercentDiscount($Index, $Percent) {
        // check for negative index
        if ($Index < 0) {
            return 0;
        }
        // set price
        if (is_array($this->Items[$Index])) {
            $this->Items[$Index]['Price'] = $this->Items[$Index]['Price'] * ((100 - $Percent) / 100);
            $this->Items[$Index]['PriceOveride'] = true;

            // update
            $this->Update();
        }
    }

// end function SetItemPriceByPercentDiscount

    function GetItemPrice($Index) {
        // check for negative index
        if ($Index < 0) {
            return 0;
        }
        // get price
        if (is_array($this->Items[$Index])) {
            return $this->Items[$Index]['Price'];
        }
        return 0;
    }

// end function GetItemPrice

    function GetItemQuantity($Index) {
        // check for negative index
        if ($Index < 0) {
            return 0;
        }
        // get price
        if (is_array($this->Items[$Index])) {
            return $this->Items[$Index]['Quantity'];
        }
        return 0;
    }

// end function GetItemQuantity

    function GetProductID($Index) {
        // check for negative index
        if ($Index < 0) {
            return 0;
        }
        // get product ID
        if (is_array($this->Items[$Index])) {
            return $this->Items[$Index]['ProductID'];
        }
        return 0;
    }

// end function GetProductID

    function Update() {
        reset($this->Items);
        $this->SubTotal = 0;
        $this->Total = 0;
        $this->Tax = 0;
        $lineitem_total = 0;
        $lineitem_tax_total = 0;

        // debug
        // echo('<pre>'); print_r($this->Items);
        // Loop through each item
        while (list ($key, $val) = each($this->Items)) {
            if (is_array($val)) {
                $numrows = count($val['Tax']);

                $lineitem_priceqty = ($val['Quantity'] * $val['Price']);
                // $product_tax_description = '';
                // $product_tax_rate = 0;

                for ($i = 0; $i < $numrows; $i++) {
                    // if ($i > 0) $product_tax_description .= ' + ';
                    // $product_tax_description .= $val['Tax'][$i]['tax_description'];

                    if (is_array($val['Tax'])) { // if this product still exists in database, tax will be an array
                        $lineitem_tax_rate[$i] = $val['Tax'][$i]['tax_rate'] * .01;
                    } else { // if product was a non-inventory product or has since been removed from database, tax will be a single value
                        $lineitem_tax_rate[$i] = $val['Tax'] * .01;
                    }
                    // $lineitem_tax_rate[$i] = $val['Tax'][$i]['tax_rate'] * .01;

                    if ($i > 0) { // we have more than one tax rate
                        // if same priority as last tax rate, just add to previous product tax rate
                        if ($val['Tax'][$i]['tax_priority'] == $val['Tax'][$i - 1]['tax_priority']) {
                            $tax_rate_value = $lineitem_priceqty * $lineitem_tax_rate[$i];
                            $lineitem_tax_total += $tax_rate_value;
                        } else { // else different priority, need to compound taxes
                            $tax_rate_value = ( ($lineitem_priceqty + $lineitem_tax_total) * $lineitem_tax_rate[$i] );
                            $lineitem_tax_total += $tax_rate_value;
                        }
                    } else { // just one tax rate
                        $lineitem_tax_total = $lineitem_priceqty * $lineitem_tax_rate[$i];
                    }
                    // $val['Tax'][$i]['tax_total'] = $lineitem_tax_total;
                }

                if ($this->TaxExempt) {
                    $lineitem_tax_total = 0;
                }

                $lineitem_total += $val['Quantity'] * $val['Price'];  // calculate subtotal for entire order
                $this->Items[$key]['ProductTax'] = $lineitem_tax_total;
                $this->Tax += $lineitem_tax_total;
            }
        }

        // Calculate pre-tax total
        $this->SubTotal = $lineitem_total;

        // Apply discount
        if (isset($this->DiscountMethod)) {
            if ($this->DiscountMethod == 'absolute') { // normal discount
                if ($this->SubTotal != 0) {
                    $percentdiscount = ($this->DiscountValue / $this->SubTotal); // calculate the discount percentage so that we can correctly deduct from tax
                } else {
                    $percentdiscount = ($this->DiscountValue); // calculate the discount percentage so that we can correctly deduct from tax
                }
                $this->SubTotal = $this->SubTotal - $this->DiscountValue;  // take $x off the pre-tax total
                $this->Tax = $this->Tax - ($this->Tax * $percentdiscount);  // take x% off the tax
                $this->CalculatedPercentDiscount = $percentdiscount * 100;
            } else { // percentage discount
                $this->SubTotal = $this->SubTotal - ($this->SubTotal * ($this->DiscountValue * .01)); // take x% off the pre-tax total
                $this->Tax = $this->Tax - ($this->Tax * ($this->DiscountValue * .01));  // take x% off the tax
                $this->CalculatedPercentDiscount = $this->DiscountValue;
            }
        }
        $this->CalculatedPercentDiscount = abs($this->CalculatedPercentDiscount);

        // total = subtotal after pre-tax discounts, prior to fees
        $this->Total = $this->SubTotal;

        // Remove tax if exempt
        if ($this->TaxExempt) {
            $this->Tax = 0;
        }

        // Final order total
        //      $this->Total += $this->Tax;
        // subtotal only -- total + tax calculated at order insert time.
        $this->Total = $this->SubTotal + Tax;

        // Apply restocking fee
        // POST-TAX -- gets adjusted out when displaying subtotal
        if ($this->RestockMethod == 'absolute') { // normal fee
            $percentfee = ($this->RestockValue / $this->Total); // calculate the fee percentage
            $this->Total = $this->Total + $this->RestockValue;  // add $x to the pre-tax total
        } else { // percentage fee
            $this->Total = $this->Total + abs($this->SubTotal * ($this->RestockValue) * .01); // add x% to the pre-tax total
        }

        // Apply shipping fee
        // POST-TAX -- gets adjusted out when displaying subtotal
        $this->Total = $this->Total + $this->ShippingValue;  // add $x to the pre-tax total
    }

// end function Update

    function SetShipping($ship_method, $ship_value) {
        $this->ShippingMethod = sanitize($ship_method);
        $this->ShippingValue = sanitize($ship_value);
        $this->Update();
    }

// end function SetShipping

    function SetRestock($restock_fee_method, $restock_fee) {
        $this->RestockMethod = $restock_fee_method;
        // if ($this->ReturnOrder) {
        // $restock_fee = change_pol($restock_fee);
        // }
        $this->RestockValue = $restock_fee;
        $this->Update();
    }

// end function SetRestock

    function ApplySplitPayment($split_payment_method, $split_payment_amount) {
        $key = sizeof($this->SplitPayments);
        $this->SplitPayments[$key]['PaymentMethod'] = $split_payment_method;
        $this->SplitPayments[$key]['PaymentAmount'] = $split_payment_amount;
    }

// end function ApplySplitPayment

    function ClearSplitPayment() {
        unset($this->SplitPayments);
        $this->SplitPayments = array();
    }

// end function ClearSplitPayment

    function GetNumItems() {
        reset($this->Items);
        $NumberItems = 0;
        while (list ($key, $val) = each($this->Items)) {
            if (is_array($val)) {
                $NumberItems += abs($val['Quantity']);
            }
        }
        return $NumberItems;
    }

// end function GetNumItems

    function TaxExempt() {
        if ($this->TaxExempt) {
            $this->TaxExempt = false;
        } else {
            $this->TaxExempt = true;
        }
        $this->Update();
    }

// end function TaxExempt

    function PrintHeader() {

        // get default currency code
        $default_currency_query_raw = "select configuration_value from " . CONFIGURATION . " where configuration_key = 'DEFAULT_CURRENCY'";
        $default_currency_query = mysql_query($default_currency_query_raw);
        $default_currency_results = mysql_fetch_array($default_currency_query);
        $default_currency_code = $default_currency_results['configuration_value'];

        // get default currency symbol that is associated with the default currency code
        $default_currency_symbol_query_raw = "select symbol_left from " . CURRENCIES . " where code = '" . $default_currency_code . "'";

        $default_currency_symbol_query = mysql_query($default_currency_symbol_query_raw);
        $default_currency_symbol_results = mysql_fetch_array($default_currency_symbol_query);
        $default_currency_symbol = $default_currency_symbol_results['symbol_left'];

        echo(date("m-d H:i", $this->PostTime) . " - ");
        if ($this->TaxExempt) {
            echo($default_currency_symbol . number_format($this->Total, 2, '.', ''));
        } else {
            echo($default_currency_symbol . number_format($this->Total + $this->Tax, 2, '.', ''));
        }
        if ($this->CustomerID) {
            $Q_Customer = mysql_query("SELECT * FROM " . CUSTOMERS . " WHERE customers_id = '" . $this->CustomerID . "' LIMIT 1");
            if (mysql_num_rows($Q_Customer)) {
                $R_Customer = mysql_fetch_assoc($Q_Customer);
                echo(" - " . $R_Customer['customers_firstname'] . " " . $R_Customer['customers_lastname']);
            } else {
                $this->CustomerID = NULL;
            }
        } else {
            echo(' - <b>' . NOBODY . '</b></font>');
        }
    }

// end function PrintHeader

    function PrintReceipt() {
        global $StoreName;
        global $StoreAddress;
        global $StoreWebsite;
        ?>
        <table border="0" width="100%" cellpadding="2" cellspacing="1" align="center">
            <tr>
                <td class="tdReceipt" width="100%" colspan="3" align="center">
                    <b><?php echo($StoreName); ?></b><br><br>
                    <?php echo($StoreAddress); ?><br><br>
                </td>
            </tr>
            <tr><td class="tdReceipt" width="100%" colspan="3">
                    <?php echo INVOICE; ?><?php
            if ($this->OrderID) {
                echo("#$this->OrderID");
            } else {
                echo NOT_PROCESSED;
            }
                    ?><br>
        <?php echo(date("m-d-Y h:i:s A")); ?><br>
                </td>
            </tr>
            <tr><td class="tdReceipt" width="100%" colspan="3" align="center"><?php echo SEPARATOR; ?></td></tr>
            <?php
            $tax_descriptions = array();
            $tax_amounts = array();
            $tax_type_counter = 0;

            while (list ($key, $val) = each($this->Items)) {
                if (is_array($val)) {

                    // if there is an order discount, adjust prices prior to calculating tax
                    if ($this->CalculatedPercentDiscount) {
                        $adjusted_price = $val['Price'] - ($val['Price'] * ($this->CalculatedPercentDiscount * .01));
                    } else {
                        $adjusted_price = $val['Price'];
                    }

                    $tax_types_this_lineitem = count($val['Tax']);
                    for ($i = 0; $i < $tax_types_this_lineitem; $i++) {
                        $lineitem_priceqty = $val['Quantity'] * $adjusted_price;
                        $lineitem_tax_rate[$i] = $val['Tax'][$i]['tax_rate'] * .01;
                        if ($i > 0) { // we have more than one tax rate
                            // if same priority as last tax rate, just add to previous product tax rate
                            if ($val['Tax'][$i]['tax_priority'] == $val['Tax'][$i - 1]['tax_priority']) {
                                $tax_rate_value = $lineitem_priceqty * $lineitem_tax_rate[$i]; //jared
                                $lineitem_tax_total += $tax_rate_value;
                            } else { // else different priority, need to compound taxes
                                $tax_rate_value = ( ($lineitem_priceqty + $lineitem_tax_total) * $lineitem_tax_rate[$i] );
                                $lineitem_tax_total += $tax_rate_value;
                            }
                        } else { // just one tax rate
                            $tax_rate_value = $lineitem_priceqty * $lineitem_tax_rate[$i];
                            $lineitem_tax_total = $tax_rate_value;
                        }

                        if (!in_array($val['Tax'][$i]['tax_description'], $tax_descriptions)) { // this tax type is not already in the array of tax types - create new entry
                            $tax_descriptions[$tax_type_counter] = $val['Tax'][$i]['tax_description'];

                            if (!$this->TaxExempt) {
                                $tax_amounts[$tax_type_counter] += $tax_rate_value;
                            } else {
                                $tax_amounts[$tax_type_counter] = 0;
                            }
                            $tax_type_counter++;
                        } else {  // already adding up tax amount for this tax description -- add to the existing tax lineitem
                            $tax_array_key = array_search($val['Tax'][$i]['tax_description'], $tax_descriptions);

                            if (!$this->TaxExempt) {
                                $tax_amounts[$tax_array_key] += $tax_rate_value;
                            } else {
                                $tax_amounts[$tax_array_key] = 0;
                            }
                        }
                    }
                    ?>
                    <tr><td class="tdReceipt" width="100%" colspan="3"><?php echo ($val['ProductModel'] . '  ' . $val['ProductName']); ?>
                            <?php
                            //   show selected options for this product
                            if (OSC_ATTRIBUTES_MODE != 'NONE') {
                                if (isset($val['Attributes']) && (count($val['Attributes']) > 0)) {
                                    echo "<ul>";
                                    $value_text = '';
                                    foreach ($val['Attributes'] as $opt_name => $avalue) {
                                        if (isset($avalue)) {
                                            foreach ($avalue as $kkey => $vval) {
                                                if ($kkey == 'value_text') {
                                                    $value_text = $vval;
                                                }
                                            }
                                        } else {
                                            $value_text = NOT_SET;
                                        }
                                        echo "<li><b>" . $opt_name . "</b>: <i>" . $value_text . "</i></li>";
                                    }
                                    echo "</ul>";
                                }
                            }
                            ?>
                        </td></tr>
                    <tr>
                        <td class="tdReceipt" width="50%"><?php echo($val['Quantity']); ?> &nbsp; @</td>
                        <td class="tdReceipt" width="25%" align="right">
                <?php echo(number_format($val['Price'], 2, '.', '')); ?>
                        </td>
                        <td class="tdReceipt" width="25%" align="right"><?php echo(number_format(($val['Price'] * $val['Quantity']), 2, '.', '')); ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            <tr><td width="100%" colspan="3" align="center"><?php echo SEPARATOR; ?></td></tr>

        <?php if ((is_numeric($this->DiscountValue)) && ($this->DiscountValue != 0)) { ?>
                <tr>
                    <td class="tdReceipt" width="75%" colspan="2"><?php echo DISCOUNT; ?></td>
                    <td class="tdReceipt" width="25%" align="right">
                        <?php
                        if ($this->DiscountMethod == 'absolute') { // normal discount
                            echo number_format(change_pol($this->DiscountValue), 2, '.', '');
                        } else { // percentage discount
                            echo number_format(change_pol($this->DiscountValue), 2, '.', '') . '&nbsp; &#37;';
                        }
                        ?>
                    </td>
                </tr>
        <?php } ?>

            <tr>
                <td class="tdReceipt" width="75%" colspan="2"><?php echo SUBTOTAL; ?></td>
                <td class="tdReceipt" width="25%" align="right"><?php echo(number_format($this->SubTotal, 2, '.', '')); ?></td>
            </tr>

            <?php
            for ($i = 0; $i < $tax_type_counter; $i++) {
                ?>
                <tr>
                    <td class="tdReceipt" width="75%" colspan="2"><?php echo $tax_descriptions[$i]; ?></td>
                    <td class="tdReceipt" width="25%" align="right"><?php echo(number_format($tax_amounts[$i], 2, '.', '')); ?></td>
                </tr>
                <?php
            }
            ?>

        <?php if (is_numeric($this->RestockValue)) { ?>
                <tr>
                    <td class="tdReceipt" width="75%" colspan="2"><?php echo RESTOCK_FEE; ?></td>
                    <td class="tdReceipt" width="25%" align="right">
                        <?php
                        if ($this->RestockMethod == 'absolute') { // absolute fee
                            echo number_format($this->RestockValue, 2, '.', '');
                        } else { // percentage fee
                            echo number_format($this->RestockValue, 2, '.', '') . '&nbsp; &#37;';
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>

        <?php if ((is_numeric($this->ShippingValue)) && ($this->ShippingMethod <> 'remove')) { ?>
                <tr>
                    <td class="tdReceipt" width="75%" colspan="2"><?php echo prep_output_string($this->ShippingMethod); ?></td>
                    <td class="tdReceipt" width="25%" align="right"><?php echo number_format($this->ShippingValue, 2, '.', ''); ?></td>
                </tr>
        <?php } ?>

            <tr>
                <td class="tdReceipt" width="75%" colspan="2"><b><?php echo TOTAL; ?></b></td>
                <td class="tdReceipt" width="25%" align="right"><?php echo(number_format($this->Total + $this->Tax, 2, '.', '')); ?></td>
            </tr>

            <tr><td class="tdReceipt" width="100%" colspan="3">
                    <br>
                <?php printf(THANK_YOU, $StoreName); ?><br><br><br><br>
            <center>
                <?php
                if ($StoreWebsite) {
                    printf(VISIT_ONLINE, $StoreWebsite);
                }
                ?>
            </center><br><br>
            </td></tr>
        </table>
        <?php
    }

// end function PrintReceipt

    function PrintFull($Checkout) {

        if ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ReturnOrder) {
            echo('<div class="alert alert-info text-center" role="alert">' . RETURN_EXCHANGE . '</div>');
        }
        ?>
        <script type="text/javascript">
            <!--
            function archive_confirm(non_inventory_count) {
                if (non_inventory_count) {
                    var answer = confirm ("<?php echo ARCHIVE_MESSAGE; ?>");
                    if (answer)
                        window.location.href='action.php?Action=ArchiveOrder';
                } else {
                    window.location.href='action.php?Action=ArchiveOrder';
                }
            }
            // -->
        </script>
        
            <div class="panel panel-primary">
					 <div class="panel-heading text-center"><?php $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->PrintHeader(); ?></div>
						<div class="panel-body">
		<table class="table table-striped">
        <?php if (!$Checkout) { ?>
			
                <tr height="45px">
                <form name="AddProductOrder" action="action.php" method="get">
                    <input type="hidden" name="Action" value="AddItem">
                    <input type="hidden" name="Quantity" value="1">
                    <td width="100%" colspan="6" class="tdHeader" align="center">
                        <input type="text" name="ProductQuery" size="20">
						<a href="#" class="btn btn-default" role="button" onclick="this.blur(); document.AddProductOrder.submit();"><?php echo ADD_ITEM; ?><input type="hidden" value="<?php echo ADD_ITEM; ?>"></a>
                        <a href="#" class="btn btn-default" role="button" onclick="this.blur(); window.location.href='product_noninventory.php'"><?php echo NON_INVENTORY; ?></span><input type="hidden" value="<?php echo NON_INVENTORY; ?>"></a>
                        <a href="#" class="btn btn-danger" role="button" onclick="this.blur(); window.location.href='order_discount.php?method=<?php echo ($this->DiscountMethod); ?>&value=<?php echo ($this->DiscountValue); ?>'"><?php echo DISCOUNT; ?></a>
                    </td>
                </form>
            </tr>
        <?php } ?>
        <tr>
            <td width="100" class="tdBlue" align="center"><b><?php echo MODEL; ?></b></td>
            <td width="360" class="tdBlue" align="center"><b><?php echo NAME; ?></b></td>
            <td width="100" class="tdBlue" align="center"><b><?php echo PRICE; ?></b></td>
            <td width="100" class="tdBlue" align="center"><b><?php echo QUANTITY; ?></b></td>
            <!--
                      <td width="100" class="tdBlue" align="center"><b><?php /* echo TAX; */ ?></b></td>
                      <td width="100" class="tdBlue" align="center"><b><?php /* echo TAX_RATE; */ ?></b></td>
            -->
            <td width="100" class="tdBlue" align="right"><b><?php echo TOTAL; ?></b></td>
        </tr>
        <?php
        reset($this->Items);
        if (REVERSE_PRODUCT_LISTING) {
            $item_listing = array_reverse($this->Items, true);
        } else {
            $item_listing = $this->Items;
        }

        $tax_descriptions = array();
        $tax_amounts = array();
        $tax_type_counter = 0;
        $non_inventory_count = 0;
        $lineitem_tax_total = 0;
        //debug
        //echo('<pre>'); print_r($item_listing);

        while (list ($key, $val) = each($item_listing)) {
            if (is_array($val)) {

// put this into a separate function?
                // if there is an order discount, adjust prices prior to calculating tax
                if ($this->CalculatedPercentDiscount) {
                    $adjusted_price = $val['Price'] - ($val['Price'] * ($this->CalculatedPercentDiscount * .01));
                } else {
                    $adjusted_price = $val['Price'];
                }

                $tax_types_this_lineitem = count($val['Tax']);
                for ($i = 0; $i < $tax_types_this_lineitem; $i++) {


                    $lineitem_priceqty = $val['Quantity'] * $adjusted_price;
                    $lineitem_tax_rate[$i] = $val['Tax'][$i]['tax_rate'] * .01;

                    if ($i > 0) { // we have more than one tax rate
                        // if same priority as last tax rate, just add to previous product tax rate
                        if ($val['Tax'][$i]['tax_priority'] == $val['Tax'][$i - 1]['tax_priority']) {
                            $tax_rate_value = $lineitem_priceqty * $lineitem_tax_rate[$i];
                            $lineitem_tax_total += $tax_rate_value;
                        } else { // else different priority, need to compound taxes
                            $tax_rate_value = ( ($lineitem_priceqty + $lineitem_tax_total) * $lineitem_tax_rate[$i] );
                            $lineitem_tax_total += $tax_rate_value;
                        }
                    } else { // just one tax rate
                        $tax_rate_value = $lineitem_priceqty * $lineitem_tax_rate[$i];
                        $lineitem_tax_total = $tax_rate_value;
                    }

                    if (!in_array($val['Tax'][$i]['tax_description'], $tax_descriptions)) { // this tax type is not already in the array of tax types - create new entry
                        $tax_descriptions[$tax_type_counter] = $val['Tax'][$i]['tax_description'];

                        if (!$this->TaxExempt) {
                            $tax_amounts[$tax_type_counter] += $tax_rate_value;
                        } else {
                            $tax_amounts[$tax_type_counter] = 0;
                        }
                        $tax_type_counter++;
                    } else {  // already adding up tax amount for this tax description -- add to the existing tax lineitem
                        $tax_array_key = array_search($val['Tax'][$i]['tax_description'], $tax_descriptions);

                        if (!$this->TaxExempt) {
                            $tax_amounts[$tax_array_key] += $tax_rate_value;
                        } else {
                            $tax_amounts[$tax_array_key] = 0;
                        }
                    }
                }
                ?>

                <tr>
                    <td width="100" align="center"><?php echo($val['ProductModel']); ?></td>
                    <td width="360">
                        <?php
                        if ($val['NonInventory']) {
                            echo($val['ProductName'] . NON_INVENTORY);
                            $non_inventory_count++;
                        } else {
                            ?>
                            <a href="product.php?ProductID=<?php echo($val['ProductID']); ?>"><?php echo($val['ProductName']); ?></a>
                            <?php
                            //   show selected attribute options for this product
                            if (OSC_ATTRIBUTES_MODE != 'NONE' && (isset($val['Attributes'])) && (count($val['Attributes']) > 0)) {
                                echo "<ul>";
                                $value_text = '';
                                foreach ($val['Attributes'] as $opt_name => $avalue) {
                                    if (isset($avalue)) {
                                        foreach ($avalue as $kkey => $vval) {
                                            if ($kkey == 'value_text') {
                                                $value_text = $vval;
                                            }
                                        }
                                    } else {
                                        $value_text = NOT_SET;
                                    }
                                    echo "<li><b>" . $opt_name . "</b>: <i>" . $value_text . "</i></li>";
                                }
                                echo "</ul>";
                            }
                        }
                        ?>
                    </td>
                    <td width="100" align="right">
                        <?php
                        echo(number_format($val['Price'], 2, '.', ''));
                        // debug
                        // print_r($this->Items[$key]);
                        if (!$Checkout) {
                            ?> &nbsp; &nbsp;
                            <a href="javascript:popupWindow('product_adjust_price.php?Index=<?php echo($key); ?>',500,160)"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                    <?php } ?>
                    </td>
                    <td width="100" align="right"<?php
                    if ($val['QuantityExceed']) {
                        echo(" style=\"background-color: #F6BBBB\"");
                    }
                    ?>>
                            <?php
                            $qty_exceeded = QUANTITY_EXCEEDED;
                            if ($val['QuantityExceed']) {
                                echo("<a style=\"font-weight: normal\" href=\"javascript:alert('$qty_exceeded')\">" . $val['Quantity'] . "</a>");
                            } else {
                                echo($val['Quantity']);
                            }
                            if (!$Checkout) {
                                ?> &nbsp; &nbsp;
                            <a href="action.php?Action=AddCartItem&Index=<?php echo($key); ?>&Quantity=1"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
                            <a href="action.php?Action=RemoveItem&Index=<?php echo($key); ?>&Quantity=1"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></a>
                            <a href="javascript:popupWindow('product_popup.php?Index=<?php echo($key); ?>',400,100)"><span class="glyphicon glyphicon-wrench" aria-hidden="true"></span></a>
                            <a href="action.php?Action=DeleteItem&Index=<?php echo($key); ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                <?php } ?>
                    </td>
                    <!--
                              <td width="100" align="center"><?php /* echo number_format($val['ProductTax'], 2, '.', ''); */ ?></td>
                    -->
                    <td width="100" align="right"><?php echo(number_format(($val['Price'] * $val['Quantity']), 2, '.', '')); ?></td>
                </tr>
                <?php
            }
        }
        ?>

                <?php if ((is_numeric($this->DiscountValue)) && ($this->DiscountValue != 0)) { ?>
            <tr>
                <td width="560" class="tdBlue" colspan="3"></td>
                <td width="100" class="tdBlue" align="left" style="font-weight: bold; color: #EF5400";><?php echo DISCOUNT; ?></td>
                <td width="100" align="right">
                    <?php
                    if ($this->DiscountMethod == 'absolute') { // normal discount
                        echo number_format(change_pol($this->DiscountValue), 2, '.', '') . '&nbsp;&nbsp;';
                    } else { // percentage discount
                        echo number_format(change_pol($this->DiscountValue), 2, '.', '') . '&nbsp; &#37;';
                    }
                    ?></td>
            </tr>
        <?php } ?>

        </td>
        </tr>

        <tr>
            <td class="tdBlue" colspan="3"></td>
            <td class="tdBlue" align="left"><b><?php echo SUBTOTAL; ?></b></td>
            <td align="right"><?php echo(number_format($this->SubTotal, 2, '.', '')); ?></td>
        </tr>
        <?php
        for ($i = 0; $i < $tax_type_counter; $i++) {
            ?>
            <tr>
                <td class="tdBlue" colspan="3"></td>
                <td class="tdBlue" align="left"><b><?php echo $tax_descriptions[$i]; ?></b></td>
                <td align="right"><?php echo(number_format($tax_amounts[$i], 2, '.', '')); ?></td>
            </tr>
            <?php
        }
        ?>
                <?php if (is_numeric($this->RestockValue)) { ?>
            <tr>
                <td class="tdBlue" colspan="3"></td>
                <td class="tdBlue" align="left" style="font-weight: bold; color: #EF5400";><?php echo RESTOCK_FEE; ?></td>
                <td align="right">
                    <?php
                    if ($this->RestockMethod == 'absolute') { // absolute fee
                        echo number_format($this->RestockValue, 2, '.', '');
                    } else { // percentage fee
                        echo '&nbsp;&nbsp;' . number_format($this->RestockValue, 2, '.', '') . '&#37;';
                    }
                    ?></td>
            </tr>
        <?php } ?>

        <?php if ((is_numeric($this->ShippingValue)) && ($this->ShippingMethod <> 'remove')) { ?>
            <tr>
                <td width="560" class="tdBlue" colspan="3"></td>
                <td width="100" class="tdBlue" align="left" style="font-weight: bold; color: #EF5400";><?php echo prep_output_string($this->ShippingMethod); ?></td>
                <td width="100" align="right"><?php echo number_format($this->ShippingValue, 2, '.', ''); ?></td>
            </tr>
        <?php } ?>

        <tr>
            <td class="tdBlue" colspan="3"></td>
            <td class="tdBlue" align="left"><b><?php echo ORDER_TOTAL; ?></b></td>
            <td align="right"><b><?php echo(number_format($this->Total + $this->Tax, 2, '.', '')); ?></b></td>
        </tr>

        <?php
        $display_total = $this->Total;
// if any partial payments exist, show them
        if (isset($this->SplitPayments[0]['PaymentMethod'])) {
            while (list ($key, $val) = each($this->SplitPayments)) {
                ?>
                <tr>
                    <td class="tdBlue" colspan="3"></td>
                    <td class="tdAqua" align="left"><b><?php echo $this->SplitPayments[$key]['PaymentMethod'] . ' ' . PARTIAL_PAYMENT; ?></b></td>
                    <td align="right"><b><?php echo(number_format($this->SplitPayments[$key]['PaymentAmount'], 2, '.', '')); ?></b></td>
                </tr>
                <?php
                $display_total -= $this->SplitPayments[$key]['PaymentAmount'];
            }
            ?>
            <tr>
                <td class="tdBlue" colspan="3"></td>
                <td class="tdAqua" align="left"><b><?php echo TOTAL_REMAINING; ?></b></td>
                <td align="right"><b><?php echo(number_format($display_total + $this->Tax, 2, '.', '')); ?></b></td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td colspan="3" class="tdHeader" align="center"></td>
            <td class="tdHeader" align="left"><b><?php echo ITEM_COUNT; ?></b></td>
            <td class="tdHeader" align="right"><b><?php echo($this->GetNumItems()); ?></b></td>
        </tr>
        <?php
        if (!$Checkout) {
            ?>
            <tr height="80px">
                <td width="100%" colspan="6" class="tdHeader" align="center"><br>

                    <a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur(); window.location.href='action.php?Action=RemoveOrder'"><?php echo REMOVE_ORDER; ?><input type="hidden" value="<?php echo REMOVE_ORDER; ?>"></a>
					<!--<a class="button" title="<?php //echo REMOVE_ORDER_BUTTON_TITLE; ?>" style="color: #993333" href="#" onclick="this.blur(); window.location.href='action.php?Action=RemoveOrder'"><span><?php //echo REMOVE_ORDER; ?></span><input type="hidden" value="<?php //echo REMOVE_ORDER; ?>"></a>-->

                    <?php if (!$this->CustomerID) { ?>
                        <a href="#" class="btn btn-default btn-sm disabled" role="button" onclick="this.blur();"><?php echo DROP_ASSIGNED_CUST; ?></span><input type="hidden" value="<?php echo DROP_ASSIGNED_CUST; ?>"></a>
						<!--<a class="button-disabled" title="<?php //echo DROP_ASSIGNED_CUST_BUTTON_TITLE; ?>" href="#" onclick="this.blur();"><span><?php //echo DROP_ASSIGNED_CUST; ?></span><input type="hidden" value="<?php //echo DROP_ASSIGNED_CUST; ?>"></a>-->
            <?php } else { ?>
                        <a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur();"><?php echo DROP_ASSIGNED_CUST; ?><input type="hidden" value="<?php echo DROP_ASSIGNED_CUST; ?>"></a>
						<!--<a class="button" title="<?php //echo DROP_ASSIGNED_CUST_BUTTON_TITLE; ?>" style="color: #993333" href="#" onclick="this.blur(); window.location.href='action.php?Action=RemoveCustomer'"><span><?php //echo DROP_ASSIGNED_CUST; ?></span><input type="hidden" value="<?php //echo DROP_ASSIGNED_CUST; ?>"></a>-->
                            <?php } ?>

                    <a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur(); popupStaticWindow('comments','comments_add.php',450,250)"><?php echo ADD_COMMENTS; ?><input type="hidden" value="<?php echo ADD_COMMENTS; ?>"></a>
					<!--<a class="button" title="<?php //echo ADD_COMMENTS_BUTTON_TITLE; ?>" style="color: #993333" href="#" onclick="this.blur(); popupStaticWindow('comments','comments_add.php',450,250)"><span><?php //echo ADD_COMMENTS; ?></span><input type="hidden" value="<?php //echo ADD_COMMENTS; ?>"></a>-->

                    <a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur(); window.location.href='action.php?Action=TaxExempt'"><?php
                if ($this->TaxExempt) {
                    echo APPLY_TAX;
                } else {
                    echo TAX_EXEMPT;
                }
                ?></span><input type="hidden" value="<?php
                if ($this->TaxExempt) {
                    echo APPLY_TAX;
                } else {
                    echo TAX_EXEMPT;
                }
                            ?>"></a>

                    <a class="btn btn-default btn-sm" href="#" onclick="this.blur(); archive_confirm(<?php echo $non_inventory_count; ?>);"><?php echo ARCHIVE_ORDER; ?><input type="hidden" value="<?php echo ARCHIVE_ORDER; ?>"></a>
                    <br /><br />

                    <?php if ($this->ReturnOrder) { ?>
                        <a class="btn btn-default" href="#" onclick="this.blur(); window.location.href='restock_fee.php?method=<?php echo ($this->RestockMethod); ?>&value=<?php echo ($this->RestockValue); ?>'"><?php echo RESTOCK_FEE; ?></a>
            <?php } ?>

                    <a class="btn btn-primary" href="#" onclick="this.blur(); window.location.href='shipping.php?method=<?php echo ($this->ShippingMethod); ?>&value=<?php echo ($this->ShippingValue); ?>'"><?php echo SHIPPING_FEE; ?></a>
            <?php if ($this->GetNumItems() == 0) { ?>
                        <a class="btn btn-success disabled" href="#" onclick="this.blur();"><?php echo COMPLETE_ORDER; ?><input type="hidden" value="<?php echo COMPLETE_ORDER; ?>"></a>
            <?php } else { ?>
                        <a class="btn btn-success" href="#" onclick="this.blur(); window.location.href='checkout.php'"><?php echo COMPLETE_ORDER; ?><input type="hidden" value="<?php echo COMPLETE_ORDER; ?>"></a>
            <?php } ?>

                </td>
            </tr>
        <?php } ?>
        </table>
		</div> <!--end of panel-->

        <?php if (strlen($this->Comments) > 0) { // if there are comments ?>
            <div class="alert alert-warning text-center" role="alert"><h4><?php echo COMMENTS; ?></h4>
                <?php echo(nl2br($this->Comments)); ?></div>
        <?php } ?>
        <?php
    }

// end function PrintFull

    function Process($session) {
        // get default currency code
        $default_currency_query_raw = "select configuration_value from " . CONFIGURATION . " where configuration_key = 'DEFAULT_CURRENCY'";
        $default_currency_query = mysql_query($default_currency_query_raw);
        $default_currency_results = mysql_fetch_array($default_currency_query);
        $default_currency_code = $default_currency_results[0];

        // get default currency symbol that is associated with the default currency code
        $default_currency_symbol_query_raw = "select symbol_left from " . CURRENCIES . " where code = '" . $default_currency_code . "'";

        $default_currency_symbol_query = mysql_query($default_currency_symbol_query_raw);
        $default_currency_symbol_results = mysql_fetch_array($default_currency_symbol_query);
        $default_currency_symbol = $default_currency_symbol_results['symbol_left'];

        $default_lang = get_default_lang();

        // get finished order status number
        $ShippedOrderStatus_query = mysql_query("select orders_status_id from " . ORDERS_STATUS . " where orders_status_name = '" . COMPLETED_ORDER_STATUS . "' AND language_id = '" . $default_lang . "'");
        $ShippedOrderStatus_result = mysql_fetch_array($ShippedOrderStatus_query);
        $ShippedOrderStatus = $ShippedOrderStatus_result['orders_status_id'];


        // get default customerID
        $default_customer_query_raw = "select customers_id from " . CUSTOMERS . " where customers_firstname= '" . DEFAULT_CUSTOMER_FIRST_NAME . "' and customers_lastname = '" . DEFAULT_CUSTOMER_LAST_NAME . "'";
        $default_customer_query = mysql_query($default_customer_query_raw);
        $default_customer_results = mysql_fetch_array($default_customer_query);
        $default_customer_id = $default_customer_results['customers_id'];

        $this->Update();
        reset($this->Items);

        // string for date/time
        $DateTime = date("Y-m-d H:i:s");


        // If there is no associated CustomerID, use the default in-store CustomerID
        if (!$this->CustomerID) {
            if ($default_customer_id) {
                $this->CustomerID = $default_customer_id;
            }
        }

        // Get Customer Info
        if ($this->CustomerID == $default_customer_id) {   // only need name if default in-store customer
            $Q_Customer = mysql_query("SELECT c.* FROM
				" . CUSTOMERS . " c WHERE
				c.customers_id = '" . $this->CustomerID . "' LIMIT 1");
            $R_Customer = mysql_fetch_assoc($Q_Customer);
            $R_Customer_Billing = $R_Customer; // these are the same for default in-store customer
        } else {
            $Q_Customer_zonecheck = mysql_query("SELECT entry_zone_id FROM " . ADDRESS_BOOK . "
				WHERE customers_id = '" . $this->CustomerID . "'");
            $R_Customer_zonecheck = mysql_fetch_assoc($Q_Customer_zonecheck);
            if ($R_Customer_zonecheck['entry_zone_id'] == 0) {
                $use_zone = 0;
            } else {
                $use_zone = 1;
            }

            $default_address_query_raw = "SELECT customers_default_address_id FROM " . CUSTOMERS . " WHERE customers_id = '" . $this->CustomerID . "'";
            $default_address_query = mysql_query($default_address_query_raw);
            $default_address_results = mysql_fetch_array($default_address_query);
            $default_address_id = $default_address_results['customers_default_address_id'];

            $Q_Customer_sql = "SELECT c.*,ab.*,co.*,z.zone_name FROM
					" . CUSTOMERS . " c, " . ADDRESS_BOOK . " ab, " . COUNTRIES . "  co, " . ZONES . " z WHERE
					c.customers_id = '" . $this->CustomerID . "' AND
					ab.customers_id = c.customers_id AND
                    ab.address_book_id = '" . $default_address_id . "' AND
					co.countries_id = ab.entry_country_id";

            $Q_Customer_Billing_sql = "SELECT c.*,ab.*,co.*,z.zone_name FROM
                " . CUSTOMERS . " c, " . ADDRESS_BOOK . " ab, " . COUNTRIES . "  co, " . ZONES . " z WHERE
                c.customers_id = '" . $this->CustomerID . "' AND
                ab.customers_id = c.customers_id AND
                ab.address_book_id = '" . $this->BillingID . "' AND
                co.countries_id = ab.entry_country_id";

            $Q_Customer_Shipping_sql = "SELECT c.*,ab.*,co.*,z.zone_name FROM
                " . CUSTOMERS . " c, " . ADDRESS_BOOK . " ab, " . COUNTRIES . "  co, " . ZONES . " z WHERE
                c.customers_id = '" . $this->CustomerID . "' AND
                ab.customers_id = c.customers_id AND
                ab.address_book_id = '" . $this->ShippingID . "' AND
                co.countries_id = ab.entry_country_id";

            if ($use_zone == 1) { // in some countries, it is less common to use the zone field
                $Q_Customer_sql .= " AND zone_id = ab.entry_zone_id LIMIT 1";
                $Q_Customer_Billing_sql .= " AND zone_id = ab.entry_zone_id LIMIT 1";
                $Q_Customer_Shipping_sql .= " AND zone_id = ab.entry_zone_id LIMIT 1";
            } else {
                $Q_Customer_sql .= " LIMIT 1";
                $Q_Customer_Billing_sql .= " LIMIT 1";
                $Q_Customer_Shipping_sql .= " LIMIT 1";
            }

            $Q_Customer = mysql_query($Q_Customer_sql);
            $Q_Customer_Billing = mysql_query($Q_Customer_Billing_sql);
            $Q_Customer_Shipping = mysql_query($Q_Customer_Shipping_sql);

            $R_Customer = mysql_fetch_assoc($Q_Customer);
            $R_Customer['customers_firstname'] = str_replace("'", "\'", $R_Customer['customers_firstname']);
            $R_Customer['customers_lastname'] = str_replace("'", "\'", $R_Customer['customers_lastname']);
            $R_Customer['entry_street_address'] = str_replace("'", "\'", $R_Customer['entry_street_address']);
            $R_Customer['entry_city'] = str_replace("'", "\'", $R_Customer['entry_city']);

            $R_Customer_Billing = mysql_fetch_assoc($Q_Customer_Billing);
            $R_Customer_Billing['entry_firstname'] = str_replace("'", "\'", $R_Customer_Billing['entry_firstname']);
            $R_Customer_Billing['entry_lastname'] = str_replace("'", "\'", $R_Customer_Billing['entry_lastname']);
            $R_Customer_Billing['entry_street_address'] = str_replace("'", "\'", $R_Customer_Billing['entry_street_address']);
            $R_Customer_Billing['entry_city'] = str_replace("'", "\'", $R_Customer_Billing['entry_city']);

            $R_Customer_Shipping = mysql_fetch_assoc($Q_Customer_Shipping);
            $R_Customer_Shipping['entry_firstname'] = str_replace("'", "\'", $R_Customer_Shipping['entry_firstname']);
            $R_Customer_Shipping['entry_lastname'] = str_replace("'", "\'", $R_Customer_Shipping['entry_lastname']);
            $R_Customer_Shipping['entry_street_address'] = str_replace("'", "\'", $R_Customer_Shipping['entry_street_address']);
            $R_Customer_Shipping['entry_city'] = str_replace("'", "\'", $R_Customer_Shipping['entry_city']);
        }
        // if no zone name found, make blank instead of Alabama
        if ($R_Customer_Billing['entry_zone_id'] == 0)
            $R_Customer_Billing['zone_name'] = '';
        if ($R_Customer_Shipping['entry_zone_id'] == 0)
            $R_Customer_Shipping['zone_name'] = '';

        // determine payment method(s)
        if (isset($this->SplitPayments[0]['PaymentMethod'])) {
            reset($this->SplitPayments);

            $final_payment_method_amount = $OrderTotal;
            while (list ($key, $val) = each($this->SplitPayments)) {
                $payment_method_string = $payment_method_string . ',' . $this->SplitPayments[$key]['PaymentMethod'];
                $payment_method_string = $payment_method_string . '(' . $this->SplitPayments[$key]['PaymentAmount'] . ') ';
                $final_payment_method_amount -= $this->SplitPayments[$key]['PaymentAmount'];
            }
            $this->PaymentMethod = $this->PaymentMethod . '(' . $final_payment_method_amount . ') ' . $payment_method_string;
        }

        if ($this->cc_last4) {
            $ccnumber = 'XXXXXXXXXXXX' . "$this->cc_last4";
        } elseif ($this->PaymentMethod == 'Authorize.Net AIM') {
            $ShippedOrderStatus = 1; // if using Authorize.Net, set the order status to "Order Processed"
            // FIXME:  this is not (yet) looking up the correct order status nor is it multi-lang friendly
            $trans_details = 'Transaction ID|' . "$this->PaymentTransactionID";
        } else {
            $trans_details = '';
        }

        if ($this->SubTotal < 0)
            $ShippedOrderStatus = '5'; // if order total is negative, set the order status to "Completed In Store"


// FIXME:  this is not (yet) looking up the correct order status nor is it multi-lang friendly

        if ($this->CustomerID == $default_customer_id) {
            $order_insertion_sql = "INSERT INTO " . ORDERS . " SET
				customers_id ='" . $this->CustomerID . "',
				customers_name='" . $R_Customer['customers_firstname'] . " " . $R_Customer['customers_lastname'] . "',
				delivery_address_format_id='" . $R_Customer['address_format_id'] . "',
				billing_address_format_id='" . $R_Customer['address_format_id'] . "',
				payment_method='" . $this->PaymentMethod . "',
				cc_type='" . $this->cc_type . "',
				cc_number = '" . $ccnumber . "',
                cc_expires = '" . $this->cc_expires . "',
                transaction_details = '" . $trans_details . "',
				last_modified='" . $DateTime . "',
				date_purchased='" . $DateTime . "',
				orders_status=$ShippedOrderStatus,
				currency='" . $default_currency_code . "',
				currency_value='1.000000',
				in_store_purchase='1',
				customers_address_format_id = '" . $R_Customer['address_format_id'] . "',
				delivery_name='" . $R_Customer['customers_firstname'] . " " . $R_Customer['customers_lastname'] . "',
				billing_name = '" . $R_Customer_Billing['customers_firstname'] . " " . $R_Customer_Billing['customers_lastname'] . "',
                return_exchange = '" . $this->ReturnOrder . "',
                pos_username = '" . $session->username . "'";

            $order_insertion_query = oc_query($order_insertion_sql, 'Order insertion failure');
        } else {
            $order_insertion_sql = "INSERT INTO " . ORDERS . " SET
				customers_id ='" . $this->CustomerID . "',
				customers_name='" . $R_Customer['customers_firstname'] . " " . $R_Customer['customers_lastname'] . "',
				customers_street_address='" . $R_Customer['entry_street_address'] . "',
				customers_city='" . $R_Customer['entry_city'] . "',
				customers_postcode='" . $R_Customer['entry_postcode'] . "',
				customers_state='" . $R_Customer['zone_name'] . "',
				customers_country='" . $R_Customer['countries_name'] . "',
				customers_telephone='" . $R_Customer['customers_telephone'] . "',
				customers_email_address='" . $R_Customer['customers_email_address'] . "',
				customers_address_format_id='" . $R_Customer['address_format_id'] . "',
				payment_method='" . $this->PaymentMethod . "',
				cc_type='" . $this->cc_type . "',
				cc_number = '" . $ccnumber . "',
                cc_expires = '" . $this->cc_expires . "',
                transaction_details = '" . $trans_details . "',
				last_modified='" . $DateTime . "',
				date_purchased='" . $DateTime . "',
				orders_status=$ShippedOrderStatus,
				currency='" . $default_currency_code . "',
				currency_value='1.000000',
				in_store_purchase='1',
				delivery_address_format_id='" . $R_Customer_Shipping['address_format_id'] . "',
                delivery_company='" . $R_Customer_Shipping['entry_company'] . "',
				delivery_name='" . $R_Customer_Shipping['entry_firstname'] . " " . $R_Customer_Shipping['entry_lastname'] . "',
				delivery_street_address='" . $R_Customer_Shipping['entry_street_address'] . "',
				delivery_city='" . $R_Customer_Shipping['entry_city'] . "',
				delivery_postcode='" . $R_Customer_Shipping['entry_postcode'] . "',
				delivery_state='" . $R_Customer_Shipping['zone_name'] . "',
				delivery_country='" . $R_Customer_Shipping['countries_name'] . "',
				billing_address_format_id='" . $R_Customer_Billing['address_format_id'] . "',
                billing_company='" . $R_Customer_Billing['entry_company'] . "',
				billing_name='" . $R_Customer_Billing['entry_firstname'] . " " . $R_Customer_Billing['entry_lastname'] . "',
				billing_street_address='" . $R_Customer_Billing['entry_street_address'] . "',
				billing_city='" . $R_Customer_Billing['entry_city'] . "',
				billing_postcode='" . $R_Customer_Billing['entry_postcode'] . "',
				billing_state='" . $R_Customer_Billing['zone_name'] . "',
				billing_country='" . $R_Customer_Billing['countries_name'] . "',
                return_exchange = '" . $this->ReturnOrder . "'";
            $order_insertion_query = oc_query($order_insertion_sql, 'Order insertion failure');
        }

        // get order number for the order we just created
        // not using mysql_insert_id() because if there is an issue with the auto_increment, then that function is not reliable
        $order_query = mysql_query("SELECT orders_id FROM " . ORDERS . " WHERE customers_id = '" . $this->CustomerID . "' AND in_store_purchase = '1' AND date_purchased = '" . $DateTime . "'");
        $order_query_results = mysql_fetch_array($order_query);
        $this->OrderID = $order_query_results['orders_id'];
        //$this->OrderId = mysql_insert_id();
        // insert order status history
        if (strlen($this->Comments) > 0) {
            $comments = IN_STORE_ORDER . '\r\n\r\n' . $this->Comments; // add complete comments into orders_status_history
        } else {
            $comments = IN_STORE_ORDER; // add complete comments into orders_status_history
        }
        if ($this->PersonalID) {
            $comments .= PERSONAL_ID . $this->PersonalID;
        }
        if (!$this->Cash) {
            $this->Cash = $this->Check;
        }

        mysql_query("INSERT INTO " . ORDERS_STATUS_HISTORY . " SET orders_id ='" . $this->OrderID . "', orders_status_id=$ShippedOrderStatus, date_added='" . $DateTime . "', customer_notified='0', comments='" . $comments . "'");

        // if this is a return/exchange order, add to comments in the other order to order status history and order comments
        if ($this->ReturnOrder) {
            // add comments to orders_status_history on the previous order
            mysql_query("INSERT INTO " . ORDERS_STATUS_HISTORY . " SET orders_id ='" . $this->ReturnOrder . "', orders_status_id=$ShippedOrderStatus, date_added='" . $DateTime . "', customer_notified='0', comments = '" . sprintf(ITEMS_RETURNED_TO_ORDER, $this->OrderID, $this->OrderID) . "'");
        }

        // insert order products

        $tax_descriptions = array();
        $tax_amounts = array();
        $tax_type_counter = 0;

        while (list ($key, $val) = each($this->Items)) {
            if (is_array($val)) {
                // if there is an order discount, adjust prices prior to calculating tax
                if ($this->CalculatedPercentDiscount) {
                    $adjusted_price = $val['Price'] - ($val['Price'] * ($this->CalculatedPercentDiscount * .01));
                } else {
                    $adjusted_price = $val['Price'];
                }

                $tax_types_this_lineitem = count($val['Tax']);
                for ($i = 0; $i < $tax_types_this_lineitem; $i++) {

                    $lineitem_priceqty = $val['Quantity'] * $adjusted_price;
                    $lineitem_tax_rate[$i] = $val['Tax'][$i]['tax_rate'] * .01;

                    if ($i > 0) { // we have more than one tax rate
                        // if same priority as last tax rate, just add to previous product tax rate
                        if ($val['Tax'][$i]['tax_priority'] == $val['Tax'][$i - 1]['tax_priority']) {
                            $tax_rate_value = $lineitem_priceqty * $lineitem_tax_rate[$i];
                            $lineitem_tax_total += $tax_rate_value;
                        } else { // else different priority, need to compound taxes
                            $tax_rate_value = ( ($lineitem_priceqty + $lineitem_tax_total) * $lineitem_tax_rate[$i] );
                            $lineitem_tax_total += $tax_rate_value;
                        }
                    } else { // just one tax rate
                        $tax_rate_value = $lineitem_priceqty * $lineitem_tax_rate[$i];
                        $lineitem_tax_total = $tax_rate_value;
                    }

                    if (!in_array($val['Tax'][$i]['tax_description'], $tax_descriptions)) { // this tax type is not already in the array of tax types - create new entry
                        $tax_descriptions[$tax_type_counter] = $val['Tax'][$i]['tax_description'];

                        if (!$this->TaxExempt) {
                            $tax_amounts[$tax_type_counter] += $tax_rate_value;
                        } else {
                            $tax_amounts[$tax_type_counter] = 0;
                        }
                        $tax_type_counter++;
                    } else {  // already adding up tax amount for this tax description -- add to the existing tax lineitem
                        $tax_array_key = array_search($val['Tax'][$i]['tax_description'], $tax_descriptions);

                        if (!$this->TaxExempt) {
                            $tax_amounts[$tax_array_key] += $tax_rate_value;
                        } else {
                            $tax_amounts[$tax_array_key] = 0;
                        }
                    }
                }

                if ($val['NonInventory']) {
                    // optionally make products_id negative so that it is easy to identify as a non-inventory product after the order is placed
                    $val['ProductID'] = -$val['ProductID'];
                } else {
                    // decrement product quantities
                    // ----------------------------
                    //  Load up attribute object
                    if (OSC_ATTRIBUTES_MODE != 'NONE') {
                        $R_Attribs = new attributes($val['ProductID'], $default_lang);

                        //  -- QTP-only: Update stock table
                        if (is_attrib_mode('QTP') && $R_Attribs->use_attrib_stock()) {
                            // update products stock table
                            mysql_query("UPDATE " . PRODUCTS_STOCK . " SET
                          products_stock_quantity = products_stock_quantity - '" . $val['Quantity'] . "'
                          WHERE products_id = '" . $val['ProductID'] . "' AND products_stock_attributes = '" . $val['ProductAttributes'] . "'");
                        }
                    }

                    mysql_query("UPDATE " . PRODUCTS . " SET
						products_quantity = products_quantity - '" . $val['Quantity'] . "'
						WHERE products_id = '" . $val['ProductID'] . "'");
                    $out_of_stock_id = $val['ProductID'];
                    $out_of_stock_model = $val['ProductModel'];
                    $out_of_stock_name = $val['ProductName'];
                    mysql_query("UPDATE " . PRODUCTS . " SET
						products_ordered = products_ordered + '" . $val['Quantity'] . "'
						WHERE products_id = '" . $val['ProductID'] . "'");
                }

                if ($this->TaxExempt) {
                    $ProductTax = 0;
                } else {
                    $ProductTax = 0;
                    for ($i = 0; $i < $tax_types_this_lineitem; $i++) {
                        $ProductTax += ($val['Tax'][$i]['tax_rate']);
                    }
                }

                $ProductTotal = $val['Price'] * $val['Quantity'];
                $val['ProductName'] = str_replace("\'", "'", $val['ProductName']);
                $val['ProductName'] = str_replace("'", "\'", $val['ProductName']);
                $val['ProductName'] = str_replace('"', '\"', $val['ProductName']);

                mysql_query("INSERT INTO " . ORDERS_PRODUCTS . " SET
					orders_id ='" . $this->OrderID . "',
					products_id = '" . $val['ProductID'] . "',
					products_model = '" . $val['ProductModel'] . "',
					products_name = '" . $val['ProductName'] . "',
					products_price = '" . $val['ProductPrice'] . "',
					final_price = '" . $val['Price'] . "',
					products_tax = '" . $ProductTax . "',
					products_quantity = '" . $val['Quantity'] . "'," .
                        //  Insert product attributes into orders products table
                        ((OSC_ATTRIBUTES_MODE != 'NONE' && is_attrib_mode("QTP") ) ? "products_stock_attributes='" . $val['ProductAttributes'] . "', " : "" )
                        . "products_prid = '" . $val['ProductID'] . "'
				");

                // ----------------------------
                // Store product attributes to order tables
                $orders_products_id = mysql_insert_id();
                if (OSC_ATTRIBUTES_MODE != 'NONE' && $val['ProductID'] > 0) {
                    $R_Attribs->process($this->OrderID, $orders_products_id, $val);
                }
            }

            // if out of stock, AND web store is configured to disable product when it runs out of stock, THEN disable the product
            $prodname_query_sql = "SELECT p.products_model, p.products_quantity, pd.products_name FROM " . PRODUCTS_DESCRIPTION . " pd, " . PRODUCTS . " p WHERE pd.products_id = p.products_id AND p.products_id = '" . $out_of_stock_id . "' AND pd.language_id = $default_lang ";
            $prodname_query = mysql_query($prodname_query_sql) or die("SQL Error.  Product lookup failure processing order.  <br><br>SQL=$prodname_query_sql");
            $prodname = mysql_fetch_array($prodname_query);

            $allow_checkout_query = mysql_query("SELECT configuration_value FROM " . CONFIGURATION . " WHERE configuration_key = 'STOCK_ALLOW_CHECKOUT'");
            $allow_checkout_results = mysql_fetch_array($allow_checkout_query);
            $allow_checkout = $allow_checkout_results['configuration_value'];

            if (($prodname['products_quantity'] < 1) && ($allow_checkout == 'false') && ( $val['NonInventory'] != true)) {
                mysql_query("UPDATE " . PRODUCTS . " SET
								products_status = 0
								WHERE products_id = '" . $val['ProductID'] . "'");

                if (OUT_OF_STOCK_EMAIL == 1) {
                    // email store owner if item is now out of stock
                    // get store administrator email address for out of stock emails
                    // use database settings for email sender and recipient
                    // $StoreOwner_query = mysql_query("select configuration_value from " . CONFIGURATION . " where configuration_key = 'STORE_OWNER_EMAIL_ADDRESS'");
                    // $StoreOwner_results = mysql_fetch_array($StoreOwner_query);
                    // $StoreOwner = $StoreOwner_results['configuration_value'];
                    // $headers  = "From: \"OllaCart Point of Sale\"<" . $StoreOwner . ">" . "\r\n";
                    // $to = $StoreOwner;
                    // use db.php settings for email sender and recipient
                    $headers = "From: \"OllaCart Point of Sale\"<" . OUT_OF_STOCK_EMAIL_SENDER . ">" . "\r\n";
                    $headers = "From: " . OUT_OF_STOCK_EMAIL_SENDER . "\r\n";
                    $to = STORE_EMAIL;

                    $subject = OUT_OF_STOCK_EMAIL_SUBJECT . $prodname['products_name'];
                    $message = OUT_OF_STOCK_EMAIL_MSG1 . $this->OrderID . OUT_OF_STOCK_EMAIL_MSG2 . $out_of_stock_name . OUT_OF_STOCK_EMAIL_MSG3 . $out_of_stock_model . '.';
                    mail($to, $subject, $message, $headers);
                }
            }
        }

        // check to see if there is an order discount
        if ((is_numeric($this->DiscountValue)) && ($this->DiscountValue != 0)) {
            if ($this->DiscountMethod == 'absolute') { // normal discount
                $Discount_value = $this->DiscountValue;
            } else { // percentage discount
                // have to get original total to know how much the discount amount is
                $original_total = $this->SubTotal / (1 - ($this->DiscountValue * .01));
                $discount_value = ($original_total * ($this->DiscountValue * .01));
            }
            // insert order discount
            $discount_value = number_format($discount_value, 2, '.', '');
            $discount_text = $default_currency_symbol . $discount_value;
            $sort_order = GetSortOrder('MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER') - 1; // put discount right before subtotal

            mysql_query("INSERT INTO " . ORDERS_TOTAL . " SET
				orders_id  = '" . $this->OrderID . "',
				title = '" . OT_TITLE_DISCOUNT . "',
				text = '-" . $discount_text . "',
				value = '-" . $discount_value . "',
				class = 'ot_discount',
				sort_order = '" . $sort_order . "'
			");
        }

        // insert order subtotal
        $SubTotal_value = number_format($this->SubTotal, 2, '.', '');
        $SubTotal_text = $default_currency_symbol . $SubTotal_value;

        $sort_order = GetSortOrder('MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER');

        mysql_query("INSERT INTO " . ORDERS_TOTAL . " SET
			orders_id  = '" . $this->OrderID . "',
			title = '" . OT_TITLE_SUBTOTAL . "',
			text = '" . $SubTotal_text . "',
			value = '" . $SubTotal_value . "',
			class = 'ot_subtotal',
			sort_order = '" . $sort_order . "'
		");

        // insert order tax
        $sort_order = GetSortOrder('MODULE_ORDER_TOTAL_TAX_SORT_ORDER');

        for ($i = 0; $i < $tax_type_counter; $i++) {
            $tax_value = $tax_amounts[$i];

            if ($this->TaxExempt) {
                $tax_value = 0;
            } else {
                $tax_value = $tax_amounts[$i];
            }

            $tax_value = number_format($tax_value, 2, '.', '');
            $tax_description = $tax_descriptions[$i];
            $tax_text = $default_currency_symbol . $tax_value;

            mysql_query("INSERT INTO " . ORDERS_TOTAL . " SET
                orders_id  ='" . $this->OrderID . "',
                title = '" . $tax_description . "',
                text = '" . $tax_text . "',
                value = '" . $tax_value . "',
                class = 'ot_tax',
                sort_order = '" . $sort_order . "'
            ");
        } // end for($i=0; $i < $tax_type_counter; $i++)
        // alter sort order to put this just after tax
        $sort_order += 1;

        // check to see if there is a shipping charge
        if ((is_numeric($this->ShippingValue)) && ($this->ShippingMethod <> 'remove')) {
            $shipping_value = $this->ShippingValue;
            // insert order discount
            $shipping_value = number_format($shipping_value, 2, '.', '');
            $shipping_text = $default_currency_symbol . $shipping_value;


            mysql_query("INSERT INTO " . ORDERS_TOTAL . " SET
                orders_id  ='" . $this->OrderID . "',
                title='" . $this->ShippingMethod . "',
                text='" . $shipping_text . "',
                value='" . $shipping_value . "',
                class='ot_shipping',
                sort_order='60'
            ");
        }

        // alter sort order to put this just after any shipping fees
        $sort_order += 1;

        // check to see if there is a restocking fee
        if (is_numeric($this->RestockValue)) {
            if ($this->RestockMethod == 'absolute') { // normal discount
                $restock_value = $this->RestockValue;
            } else { // percentage discount
                // have to get original total to know how much the discount amount is
                //$original_total = $this->Total / (1 - ($this->RestockValue * .01));
                //$restock_value = ($original_total * ($this->RestockValue * .01));
                $restock_value = abs($this->SubTotal * ($this->RestockValue * .01));
            }

            // insert order discount
            $restock_value = number_format($restock_value, 2, '.', '');
            $restock_text = $default_currency_symbol . $restock_value;


            mysql_query("INSERT INTO " . ORDERS_TOTAL . " SET
				orders_id  ='" . $this->OrderID . "',
				title='" . OT_TITLE_RESTOCK . "',
				text='" . $restock_text . "',
				value='" . $restock_value . "',
				class='ot_restock_fee',
				sort_order='50'
			");
        }


        // insert order total
        $this->Total = number_format(($this->Total + $this->Tax), 2, '.', '');
        $sort_order = GetSortOrder('MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER');

        mysql_query("INSERT INTO " . ORDERS_TOTAL . " SET
			orders_id ='" . $this->OrderID . "',
			title = '" . OT_TITLE_TOTAL . "',
			text = '<b> " . $default_currency_symbol . " " . $this->Total . "</b>',
			value = '" . $this->Total . "',
			cash = '" . $this->Cash . "',
			class = 'ot_total',
			sort_order = '" . $sort_order . "'
		");

        // send email order confirmation if customer has an email address
        if ($R_Customer['customers_email_address'] != '') {

            $result = MailCustomerReceipt($this->OrderID, $R_Customer['customers_email_address']);
            // debug
            // echo $result ? "<h1> Mail Sent</h1>" : "<h1> Mail NOT Sent</h1>"; die();
        }
    }

// end function Process

    function Archive() {
        //  This value is needed to correctly load attribute values
        global $language_id;

        // get default customerID
        $default_customer_query_raw = "select customers_id from " . CUSTOMERS . " where customers_firstname= '" . DEFAULT_CUSTOMER_FIRST_NAME . "' and customers_lastname = '" . DEFAULT_CUSTOMER_LAST_NAME . "'";
        $default_customer_query = mysql_query($default_customer_query_raw);
        $default_customer_results = mysql_fetch_array($default_customer_query);
        $default_customer_id = $default_customer_results['customers_id'];

        // If there is no associated CustomerID, use the default in-store CustomerID
        if (!$this->CustomerID) {
            if ($default_customer_id) {
                $this->CustomerID = $default_customer_id;
            }
        }

        if (!$this->TaxExempt) {
            $this->TaxExempt = 0;
        }

        // insert main order
        mysql_query("INSERT INTO " . POS_ORDERS . " SET
			customers_id ='" . $this->CustomerID . "',
			comments ='" . $this->Comments . "',
            total ='" . number_format($this->Total, 2, '.', '') . "',
			tax_exempt ='" . $this->TaxExempt . "',
			post_time ='" . $this->PostTime . "'
		");
        $ArchivedOrderID = mysql_insert_id();

        $running_order_total = 0;

        // insert order products
        while (list ($key, $val) = each($this->Items)) {
            if (is_array($val)) {
                if ($val['NonInventory']) {
                    // $val['ProductID'] = -$val['ProductID'];
                    // do not archive non-inventory products.
                    // since multiple tax rates can be altered while adding non-inventory items to the order,
                    // we can't run additem() on a non-inventory product and get the same tax values.
                    continue;
                }
                $val['ProductName'] = str_replace("\'", "'", $val['ProductName']);
                $val['ProductName'] = str_replace("'", "\'", $val['ProductName']);
                $val['ProductName'] = str_replace('"', '\"', $val['ProductName']);

                // ----------------------------
                //  Store attributes to pos orders products table
                mysql_query("INSERT INTO " . POS_ORDERS_PRODUCTS . " SET
					pos_orders_id  ='" . $ArchivedOrderID . "',
					products_id='" . $val['ProductID'] . "',
					products_model='" . $val['ProductModel'] . "',
					products_name='" . $val['ProductName'] . "',
					products_price='" . $val['Price'] . "',
					price_overide='" . ($val['PriceOveride'] ? 1 : 0 ) . "',
					non_inventory ='" . ($val['NonInventory'] ? 1 : 0 ) . "',
					products_quantity='" . $val['Quantity'] . "'" .
                        ((OSC_ATTRIBUTES_MODE != 'NONE' && isset($val['Attributes'])) ? ", products_stock_attributes='" . $val['ProductAttributes'] . "'" : "")
                );
                //  Store attribute values to pos orders products attributes table
                if (OSC_ATTRIBUTES_MODE != 'NONE' && isset($val['Attributes'])) {
                    $language_id = get_default_lang();
                    $R_Attribs = new attributes($val['ProductID'], $language_id);
                    $R_Attribs->archive($ArchivedOrderID, $val['Attributes']);
                }
                $running_order_total += $val['Price'];
            }
        }
        // update order total in case any non-inventory items needed to be ignored during archival
        mysql_query("UPDATE " . POS_ORDERS . " SET total = '" . $running_order_total . "' WHERE pos_orders_id = '" . $ArchivedOrderID . "'");
    }

// end function Archive

    function AssignCustomer($CustomerID) {
        $this->CustomerID = $CustomerID;
    }

// end function AssignCustomer
}

// end class Order

function sanitize($input) {
    $input = strip_tags($input);
    $input = htmlspecialchars($input);
    $input = trim($input);
    $input = stripslashes($input);
    $input = mysql_real_escape_string($input);
    $input = str_replace("\\", "", $input);
    $input = str_replace("'", "\'", $input);

    return ($input);
}

// end function sanitize

function prep_output_string($input) {
    $output = stripslashes($input);

    return $output;
}

// end function prep_output_string


include('functions_values.php');
?>
