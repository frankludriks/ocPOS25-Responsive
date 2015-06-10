<?php
// order_void.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}


// restock products
$Q_Order = mysql_query("SELECT void FROM " . ORDERS . " WHERE orders_id='" . $_REQUEST['OrderID'] . "'");
$R_Order = mysql_fetch_array($Q_Order);
if ($R_Order['void'] == '0') { // if order has already been voided, do not restock products	
  if (use_attribs()) {
    $Q_Products = mysql_query("SELECT products_id, products_quantity, products_stock_attributes FROM " . ORDERS_PRODUCTS . " WHERE orders_id='" . $_REQUEST['OrderID'] . "'");
  } else {
    $Q_Products = mysql_query("SELECT products_id, products_quantity FROM " . ORDERS_PRODUCTS . " WHERE orders_id='" . $_REQUEST['OrderID'] . "'");
  }
  
	while($R_Products = mysql_fetch_assoc($Q_Products)) {
    if (use_attribs() && $R_Products['products_id'] > 0) {
      $R_Attribs = new attributes($R_Products['products_id']);
        if (is_attrib_mode('QTP') && $R_Attribs->use_attrib_stock()) {
          // update products stock table
          mysql_query("UPDATE ". PRODUCTS_STOCK. " SET
          products_stock_quantity = products_stock_quantity + '". $R_Products['products_quantity']. "'
          WHERE products_id = '". $R_Products['products_id']. "' AND products_stock_attributes = '". $R_Products['products_stock_attributes']."'");
        }
    }
		mysql_query("UPDATE " . PRODUCTS . " SET products_quantity = products_quantity + '" . $R_Products['products_quantity'] . "' WHERE products_id='" . $R_Products['products_id'] . "'");
	}
}

	if (DELETE_ORDERS == '1') {  // delete the order
		mysql_query("DELETE FROM " . ORDERS . " WHERE orders_id='" . $_REQUEST['OrderID'] . "'");
		mysql_query("DELETE FROM " . ORDERS_PRODUCTS . " WHERE orders_id='" . $_REQUEST['OrderID'] . "'");
		if (use_attribs()) mysql_query("DELETE FROM " . ORDERS_PRODUCTS_ATTRIBUTES . " WHERE orders_id='" . $_REQUEST['OrderID'] . "'");
		mysql_query("DELETE FROM " . ORDERS_STATUS_HISTORY . " WHERE orders_id='" . $_REQUEST['OrderID'] . "'");
		mysql_query("DELETE FROM " . ORDERS_TOTAL . " WHERE orders_id='" . $_REQUEST['OrderID'] . "'");
	} else {  // set order to Void status
		$DateTime = date("Y-m-d H:i:s");
		mysql_query("UPDATE " . ORDERS . " set orders_status = '" . $voided_order_status_id . "' WHERE orders_id='" . $_REQUEST['OrderID'] . "'");
		mysql_query("INSERT INTO " . ORDERS_STATUS_HISTORY . " SET
				orders_id  ='" . $_REQUEST['OrderID'] . "',
				orders_status_id='" . $voided_order_status_id . "',
				date_added='" . $DateTime . "',
				customer_notified='0',
				comments='" . VOID_COMMENTS . "'
			");
			
		// set void flag on order
		mysql_query("UPDATE " . ORDERS . " SET void='1' WHERE orders_id='" . $_REQUEST['OrderID'] . "' AND in_store_purchase = '1'");
	}

header("Location: order.php?OrderID=" . $_REQUEST['OrderID'] . "");

?>
