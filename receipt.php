<?php
// receipt.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}


if (IN_STORE_ONLY == 1) {
    $instore_only = " o.in_store_purchase = '1' AND ";
} else {
    $instore_only = '';
}

$Q_Order = mysql_query("SELECT o.*,ot.cash FROM " . ORDERS . " o, " . ORDERS_TOTAL . " ot WHERE
	o.orders_id='" . $_REQUEST['OrderID'] . "' AND
	$instore_only 
	ot.orders_id = o.orders_id AND 
	ot.class = 'ot_total' LIMIT 1");
    
$SubTotal_Query = mysql_query("SELECT value FROM " . ORDERS_TOTAL . " WHERE orders_id='" . $_REQUEST['OrderID'] . "' and class = 'ot_subtotal'");
$SubTotal = mysql_fetch_array($SubTotal_Query);
$SubTotal = $SubTotal['value'];


$Tax_Query = mysql_query("SELECT title, value FROM " . ORDERS_TOTAL . " WHERE orders_id='" . $_REQUEST['OrderID'] . "' and class = 'ot_tax'");

$tax_type_counter=0;
while ($Tax_Results = mysql_fetch_array($Tax_Query))  {
    $Tax[$tax_type_counter]['tax_description'] = $Tax_Results['title'];
    $Tax[$tax_type_counter]['tax_value'] = $Tax_Results['value'];
    $tax_type_counter++;
}

$Total_Query = mysql_query("SELECT value FROM " . ORDERS_TOTAL . " WHERE orders_id='" . $_REQUEST['OrderID'] . "' and class = 'ot_total'");
$Total = mysql_fetch_array($Total_Query);
$Total = $Total['value'];

// get In-Store Discount 
$Q_Discount = mysql_query("SELECT title, value FROM " . ORDERS_TOTAL . " where orders_id = '" . $_REQUEST['OrderID'] . "' AND class='ot_discount'");
if (mysql_num_rows($Q_Discount)) {
    $R_Discount = mysql_fetch_array($Q_Discount);
}
$DiscountValue = $R_Discount['value'];

// get shipping 
$Q_Shipping = mysql_query("SELECT title, value FROM " . ORDERS_TOTAL . " where orders_id = '" . $_REQUEST['OrderID'] . "' AND class='ot_shipping'");
if (mysql_num_rows($Q_Shipping)) {
    $R_Shipping = mysql_fetch_array($Q_Shipping);
}
$ShippingValue = $R_Shipping['value'];
$ShippingMethod = $R_Shipping['title'];

// get Restocking fees 
$Q_Restock = mysql_query("SELECT title, value FROM " . ORDERS_TOTAL . " where orders_id = '" . $_REQUEST['OrderID'] . "' AND class='ot_restock_fee'");
if (mysql_num_rows($Q_Restock)) {
    $R_Restock = mysql_fetch_array($Q_Restock);
}
$RestockValue = $R_Restock['value'];

if($_SESSION['CurrentOrderIndex'] == -1 && !mysql_num_rows($Q_Order)) {
	$OnLoad = "window.close();";
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title><?php echo($POSName) . ': ' . TITLE; ?></title>
       <link rel="Stylesheet" href="css/style.css">
       <style>  body { background-image:none; }  </style>
       <script language="JavaScript" src="javascript.js" type="text/javascript"></script>
	   <!-- Bootstrap core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
	<link href="user.css" rel="stylesheet">
</head>
<!-- if this order was just finalized, print the receipt -->
  <?php if (substr_count($_SERVER['REQUEST_URI'], 'printme=1') > 0 ) { ?>
	  <body onload="<?php echo($OnLoad); ?>; window.print()">	  
  <?php } else { ?>
	  <body onload="<?php echo($OnLoad); ?>">
  <?php } ?>
<div class="container">
<table width="90%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
  
<?php
if(mysql_num_rows($Q_Order)) { // if this is a completed order, proceed, else skip and print from the function in includes/functions.php
	$R_Order = mysql_fetch_assoc($Q_Order);
?>
		 <table border="0" width="100%" cellpadding="2" cellspacing="1" align="center">
		 <tr>
		 <td width="100%" colspan="3" align="center">
		 <img src="../images/store_logo.png" width="200"><br><br>
		 <b><?php echo($StoreName); ?></b><br>
		 <?php echo($StoreAddress); ?><br>
		 ABN: 47 099 025 733<br><br>
		 </td>
		 </tr>
		 <tr><td width="100%" colspan="3">
		 <?php echo INVOICE . ($R_Order['orders_id']); ?><br>
		 Date: <?php
	 	$DateExp = explode(" ",$R_Order['date_purchased']);
		$Time = $DateExp[1];
		$TimeEx = explode(":",$DateExp[1]);
		$DateExp = explode("-",$DateExp[0]);
	 	echo("$DateExp[1]-$DateExp[2]-$DateExp[0] $Time");
	 	?><br>
	 	Payment Method: <?php echo($R_Order['payment_method'] . '<br>');?> 
		 </td>
		 </tr>
		 <tr><td width="100%" colspan="3" align="center"><?php echo SEPARATOR; ?></td></tr>
		 
		 
<?php
$Q_Products = mysql_query("SELECT * FROM " . ORDERS_PRODUCTS . " WHERE orders_id='" . $_REQUEST['OrderID'] . "' ORDER BY products_model");

while($R_Products = mysql_fetch_assoc($Q_Products)) {

?>
		 <tr><td width="100%" colspan="3"><?php echo($R_Products['products_name']);
         
      //   show selected options for this product
        if (use_attribs()) {
            $Q_Orders_Products_Attribs = mysql_query("SELECT orders_products_attributes_id FROM " . ORDERS_PRODUCTS_ATTRIBUTES . " WHERE orders_id='" . $_REQUEST['OrderID'] . "' and orders_products_id = '" . $R_Products['orders_products_id'] . "'");
            if(mysql_num_rows($Q_Orders_Products_Attribs)) {            
                $R_Attribs = new attributes($R_Products['products_id']);
                echo $R_Attribs->print_order_attribs($R_Products['orders_products_id']);
            }
        }

		 ?></td></tr>
		 <tr>
		  <td width="50%" align="right"><?php echo($R_Products['products_quantity']); ?> &nbsp; @</td>
		  <td width="25%" align="right">
		  <?php echo(number_format($R_Products['final_price'], 2, '.', '')); ?>
		  </td>
		  <td width="25%" align="right"><?php echo(number_format(($R_Products['final_price'] * $R_Products['products_quantity']), 2, '.', '')); ?></td>
		 </tr> 
<?php } ?>		 
		 <tr><td width="100%" colspan="3" align="center"><?php echo SEPARATOR; ?></td></tr>
<?php 
		if (is_numeric($DiscountValue)) {
		?>
		 <tr>
		  <td width="75%" colspan="2"><?php echo DISCOUNT; ?></td>
		  <td width="25%" align="right"><?php echo(number_format($DiscountValue, 2, '.', '')); ?></td>
<?php } ?>		 
		 
		 <tr>
		  <td width="75%" colspan="2"><?php echo SUBTOTAL; ?></td>
		  <td width="25%" align="right"><?php echo(number_format($SubTotal, 2, '.', '')); ?></td>
		 </tr>
<?php 
    for($i=0; $i < $tax_type_counter; $i++) {
?>
		 <tr>
		  <td width="75%" colspan="2"><?php echo $Tax[$i]['tax_description']; ?></td>
		  <td width="25%" align="right"><?php echo(number_format(round($Tax[$i]['tax_value'],2), 2, '.', '')); ?></td>
		 </tr>
<?php 
    }
?>
<?php
		if (is_numeric($RestockValue)) { 
		?>
		 <tr>
		  <td width="75%" colspan="2"><?php echo RESTOCK_FEE; ?></td>
		  <td width="25%" align="right"><?php echo(number_format($RestockValue, 2, '.', '')); ?></td>
<?php } ?>	

<?php
		if (is_numeric($ShippingValue)) {
		?>
		 <tr>
		  <td width="75%" colspan="2"><?php echo $ShippingMethod; ?></td>
		  <td width="25%" align="right"><?php echo(number_format($ShippingValue, 2, '.', '')); ?></td>
<?php } ?>	

		 <tr>
		  <td width="75%" colspan="2"><b><?php echo TOTAL; ?></b></td>
		  <td width="25%" align="right"><?php echo(number_format($Total, 2, '.', '')); ?></td>
		 </tr>
	<?php if($R_Order['payment_method']=="Cash") { ?>
		 <tr><td width="100%" colspan="3" align="center"><br></td></tr>
		 <tr>
		  <td width="75%" colspan="2"><?php echo CASH_TENDERED; ?></td>
		  <td width="25%" align="right"><?php echo(number_format($R_Order['cash'], 2, '.', '')); ?></td>
		 </tr>
		 <tr>
		  <td width="75%" colspan="2"><?php echo CHANGE; ?></td>
		  <td width="25%" align="right"><?php echo(number_format($R_Order['cash'] - $Total,2, '.' , '')); ?></td>
		 </tr>
	<?php } ?>
	<?php if($R_Order['payment_method']=="Check") { ?>
		 <tr><td width="100%" colspan="3" align="center"><br></td></tr>
		 <tr>
		  <td width="75%" colspan="2"><?php echo CHECK_TENDERED; ?></td>
		  <td width="25%" align="right"><?php echo(number_format($R_Order['cash'], 2, '.', '')); ?></td>
		 </tr>
		 <tr>
		  <td width="75%" colspan="2"><?php echo CHANGE; ?></td>
		  <td width="25%" align="right"><?php echo(number_format($R_Order['cash'] - $Total,2, '.' , '')); ?></td>
		 </tr>
	<?php } ?>
		 <tr><td width="100%" colspan="3">
		 <br><center><br>
		 <?php printf(THANK_YOU, $StoreName); ?><br><br>
		 <?php  if($StoreWebsite) {
		 		 	printf(VISIT_ONLINE, $StoreWebsite);
				}
		 ?>
		 </center>
		 <br>
		 </td></tr>
		 </table>
<?php
} else {
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->PrintReceipt();
}
?>
 
  </td>
 </tr>
</table>
</div> <!-- /container -->
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
	<!-- include jquery and bootstrap -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="bootstrap-3.3.4/js/bootstrap.min.js"></script>
</body>
</html>
