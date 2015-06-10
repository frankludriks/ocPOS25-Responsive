<?php
// order.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}

$error = '';

// get order ID
$Q_Order = mysql_query("SELECT * FROM " . ORDERS . " WHERE orders_id='" . $_REQUEST['OrderID'] . "' LIMIT 1");

if(mysql_num_rows($Q_Order)){
    $R_Order = mysql_fetch_assoc($Q_Order);
// if old order is found, create new return order
    NewOrder($R_Order['customers_id']);
    $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ReturnOrder = $_REQUEST['OrderID'];
} else {
	$error = "Order not Found";
	exit();
}

// get all product IDs in the old order
if (use_attribs() && is_attrib_mode("QTP")) {
    $Q_Orders_Products = mysql_query("SELECT products_id, final_price, products_tax, products_quantity, products_stock_attributes FROM " . ORDERS_PRODUCTS . " WHERE orders_id='" . $_REQUEST['OrderID'] . "'");
} else {
    $Q_Orders_Products = mysql_query("SELECT products_id, final_price, products_tax, products_quantity FROM " . ORDERS_PRODUCTS . " WHERE orders_id='" . $_REQUEST['OrderID'] . "'");
}

if (mysql_num_rows($Q_Orders_Products)) {
	while($R_Orders_Products = mysql_fetch_assoc($Q_Orders_Products)) {    
        // check to make sure that product is still a valid product or a non-inventory item
/*        $Q_Product_query = "SELECT products_id FROM " . PRODUCTS . " WHERE products_id = '" . $R_Orders_Products['products_id'] . "'";
        $Q_Product = mysql_query($Q_Product_query);
        if(mysql_num_rows($Q_Product) == 1) {
*/
        if (use_attribs() && is_attrib_mode("QTP")) {
            $Index = $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->AddItem($R_Orders_Products['products_id'], -$R_Orders_Products['products_quantity'], IN_STORE_PRICING, $R_Orders_Products['products_stock_attributes'], 0);
        } else {
            $Index = $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->AddItem($R_Orders_Products['products_id'], -$R_Orders_Products['products_quantity'], IN_STORE_PRICING, '', 0);
        }
/*        } else { // item is no longer in the database -- add as a non-inventory item
            $Index = $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->AddItem($R_Orders_Products['products_id'], -$R_Orders_Products['products_quantity'], IN_STORE_PRICING, '', 0);
        }
*/
        // set item price to price at time of original purchase
        $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SetItemPrice($Index, $R_Orders_Products['final_price']);
    }
    $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Comments = sprintf(CONTAINS_RETURNS, $_REQUEST['OrderID'], $_REQUEST['OrderID']);
} else {
	$error = NO_PRODUCTS;
	// exit();
}

// get tax from old order.  If zero tax, make the return order tax exempt.  Otherwise, the new order will auto-calculate tax.
$Q_Tax = mysql_query("SELECT value FROM " . ORDERS_TOTAL . " where orders_id = '" . $_REQUEST['OrderID'] . "' AND class='ot_tax'");
if (mysql_num_rows($Q_Tax)) {
    $R_Tax = mysql_fetch_array($Q_Tax);
    if ($R_Tax['value'] == 0.0000) { // found tax, it's zero.
         $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->TaxExempt = true;
         $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Tax = 0;
    }
} else { // didn't find tax, therefore there was no tax
    $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->TaxExempt = true;
    $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Tax = 0;
}

// get In-Store Discount from old order
$Q_Discount = mysql_query("SELECT title, value FROM " . ORDERS_TOTAL . " where orders_id = '" . $_REQUEST['OrderID'] . "' AND class='ot_discount'");
if (mysql_num_rows($Q_Discount)) {
    $R_Discount = mysql_fetch_array($Q_Discount);
    $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SetDiscount('absolute' , -abs($R_Discount['value']));
}

// get osC Discount Coupon from old order
$Q_DiscountCoupon = mysql_query("SELECT title, value FROM " . ORDERS_TOTAL . " where orders_id = '" . $_REQUEST['OrderID'] . "' AND class='ot_discount_coupon'");
if (mysql_num_rows($Q_DiscountCoupon)) {
    $R_DiscountCoupon = mysql_fetch_array($Q_DiscountCoupon);
    $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SetDiscountCoupon($R_DiscountCoupon['title'] , -abs($R_DiscountCoupon['value']));
}

/*
// get shipping from old order
$Q_Shipping = mysql_query("SELECT title, value FROM " . ORDERS_TOTAL . " where orders_id = '" . $_REQUEST['OrderID'] . "' AND class='ot_shipping'");
if (mysql_num_rows($Q_Shipping)) {
    $R_Shipping = mysql_fetch_array($Q_Shipping);
    $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SetShipping($R_Shipping['title'], -$R_Shipping['value']);
}
*/



header("Location: index.php?PHPSESSID=".session_id());

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title><?php echo($POSName) . ': ' . TITLE; ?></title>
       <link rel="Stylesheet" href="css/style.css">
       <script language="JavaScript" src="javascript.js" type="text/javascript"></script>
</head>
<body>
<?php
include("includes/header.php");
echo $error;
?>
</body>
</html>