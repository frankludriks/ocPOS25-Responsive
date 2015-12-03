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

// redirect to reporting
if($_REQUEST['OrderID']=="Report"){
	header("Location: reporting.php");
	exit();
}

// redirect to reporting
if($_REQUEST['OrderID']==""){
	header("Location: index.php");
	exit();
}

// Capture query without leading or trailing spaces, or leading zeroes
$Query = ltrim(trim($_GET["OrderID"]),"0");

// sanitize search term(s)
$Query = sanitize($Query); 

$ORDER_SEARCH = $Query;
$orders_id = $Query;


$Q_Order = mysql_query("SELECT * FROM " . ORDERS . " WHERE orders_id='" . $_REQUEST['OrderID'] . "' LIMIT 1");
if(mysql_num_rows($Q_Order)){
	$R_Order = mysql_fetch_assoc($Q_Order);
}else{
	header("Location: order_history.php");
	exit();
}

$Subtotal_Query = mysql_query("SELECT value FROM " . ORDERS_TOTAL . " WHERE orders_id='" . $orders_id . "' and class = 'ot_subtotal'");
$Subtotal = mysql_fetch_array($Subtotal_Query);
$Subtotal = $Subtotal[0];

$Tax_Query = mysql_query("SELECT title, value FROM " . ORDERS_TOTAL . " WHERE orders_id='" . $orders_id . "' and class = 'ot_tax'");

$tax_type_counter=0;
while ($Tax_Results = mysql_fetch_array($Tax_Query))  {
    $Tax[$tax_type_counter]['tax_description'] = $Tax_Results['title'];
    $Tax[$tax_type_counter]['tax_value'] = $Tax_Results['value'];
    $tax_type_counter++;
}   

$Total_Query = mysql_query("SELECT value FROM " . ORDERS_TOTAL . " WHERE orders_id='" . $orders_id . "' and class = 'ot_total'");
$Total = mysql_fetch_array($Total_Query);
$Total = $Total[0];

// get In-Store Discount 
$Q_Discount = mysql_query("SELECT title, value FROM " . ORDERS_TOTAL . " where orders_id = '" . $orders_id . "' AND class='ot_discount'");
if (mysql_num_rows($Q_Discount)) {
    $R_Discount = mysql_fetch_array($Q_Discount);
}
$DiscountValue = $R_Discount['value'];
// get shipping 
$Q_Shipping = mysql_query("SELECT title, value FROM " . ORDERS_TOTAL . " where orders_id = '" . $orders_id . "' AND class='ot_shipping'");
if (mysql_num_rows($Q_Shipping)) {
    $R_Shipping = mysql_fetch_array($Q_Shipping);
}
$ShippingValue = $R_Shipping['value'];
$ShippingMethod = $R_Shipping['title'];
// get Restocking fees 
$Q_Restock = mysql_query("SELECT title, value FROM " . ORDERS_TOTAL . " where orders_id = '" . $orders_id . "' AND class='ot_restock_fee'");
if (mysql_num_rows($Q_Restock)) {
    $R_Restock = mysql_fetch_array($Q_Restock);
}
$RestockValue = $R_Restock['value'];

// find count of non-inventory items
$non_inventory_count = 0;

$Q_NIProducts = mysql_query("SELECT products_id, products_name FROM " . ORDERS_PRODUCTS . " WHERE orders_id='" . $_REQUEST['OrderID'] . "'");
while($R_NIProducts = mysql_fetch_array($Q_NIProducts)) {
    
    if ($R_NIProducts['products_id'] < -1000000000) { // Non-inventory product
        $non_inventory_count++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <link rel="icon" href="favicon.ico">

    <title><?php echo($POSName) . ': ' . TITLE; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
	<link href="user.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="jumbotron-narrow.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script language="JavaScript" src="javascript.js" type="text/javascript"></script>
	    <script type="text/javascript">
        <!--
        function return_confirm(non_inventory_count) {
            if (non_inventory_count) {
                var answer = confirm ("<?php echo RESTOCK_MESSAGE; ?>");
                if (answer)
                    window.location.href='return_exchange.php?OrderID=<?php echo($R_Order['orders_id']); ?>';
            } else {
                window.location.href='return_exchange.php?OrderID=<?php echo($R_Order['orders_id']); ?>';
            }
        }
        // -->
    </script>
  </head>
<body onload="<?php
if($_REQUEST['PopReceipt']){
	echo("popupWindow('receipt.php?OrderID=" . $R_Order['orders_id'] . "&printme=1',280,550);");
}
?>">
  <div class="container">
    <?php include("includes/header.php"); ?>
      <div class="row marketing">
		<div class="well well-lg"><h1 class="text-center">Order #<?php echo($R_Order['orders_id']); ?></h1></div>
		 <?php 
		 if($_REQUEST['PopReceipt']){ 
		  if($_REQUEST['Change'] >= 0){ 
			echo '<div class="alert alert-success text-center" role="alert">'; 
		  } else { 
			echo '<div class="alert alert-info text-center" role="alert">';
		  } 
		  ?>
		  <?php
		  if($_REQUEST['Change']){
			echo("<br>" . CHANGE_DUE . "<br><span style=\"font-size:60px; font-weight: bold;\">" . $default_currency_symbol . number_format($_REQUEST['Change'], 2, '.', '')."</span>");
		  }
		  ?>
	   </div>
		<?php 
		} elseif ($R_Order['void']) { 
		?>
		 <div class="alert alert-danger text-center" role="alert"><h1><?php echo ORDER_VOIDED; ?></h1></div>
		<?php 
		} 
		?>

<table width="100%" class="table">
  <tr>
    <td width="100%">
	  <table width="100%" class="table table-striped table-hover table-condensed">
		 <tr>
		   <td width="20%" class="tdBlue"><b><?php echo ORDER_DATE; ?></b></td>
		   <td width="80%">
		   <?php
			$DateExp = explode(" ",$R_Order['date_purchased']);
			$Time = $DateExp[1];
			$DateExp = explode("-",$DateExp[0]);
			echo("$DateExp[1]-$DateExp[2]-$DateExp[0] $Time");
		   ?>
		   </td>
		 </tr>
		 <tr>
		   <td width="20%" class="tdBlue"><b><?php echo PAYMENT_METHOD; ?></b></td>
		   <td width="80%">
		   <?php echo($R_Order['payment_method']); ?></td>
		 </tr>
		 <tr>
			<td colspan="2">&nbsp;</td>
		 </tr>
	  </table>
	</td>
  </tr>
  <?php 
    if($R_Order['customers_id']) {  
	  if (ENABLE_BILLING_SHIPPING_ADDR) {
  ?>

	    <tr>
    <td width="50%">
		<table width="100%">
         <tr>
            <td width="100%" colspan="2" align="center">
             <b><font size="3"><?php echo BILLING_ADDRESS; ?></font></b>
            </td>
         </tr>
         <tr>
           <td width="30%"><b><?php echo COMPANY; ?></b></td>
           <td width="70%"><?php echo($R_Order['billing_company']); ?></td>
        </tr>
         <tr>
             <td><b><?php echo FULL_NAME; ?></b></td>
             <td><?php echo($R_Order['billing_name']); ?></td>
         </tr>
         <tr>
             <td><b><?php echo STREET_ADDRESS; ?></b></td>
             <td><?php echo($R_Order['billing_street_address']); ?></td>
         </tr>
         <tr>
             <td><b><?php echo CITY; ?></b></td>
             <td><?php echo($R_Order['billing_city']); ?></td>
         </tr>
         <tr>
             <td><b><?php echo STATE; ?></b></td>
             <td><?php echo($R_Order['billing_state']); ?></td>
         </tr>
         <tr>
             <td><b><?php echo POST_CODE; ?></b></td>
             <td><?php echo($R_Order['billing_postcode']); ?></td>
         </tr>
         <tr>
             <td><b><?php echo COUNTRY; ?></b></td>
             <td><?php echo($R_Order['billing_country']); ?></td>
         </tr>
     </table>
    </td>
    <td width="50%">
    <table width="100%">
         <tr>
            <td width="100%" colspan="2" align="center">
             <b><font size="3"><?php echo SHIPPING_ADDRESS; ?></font></b>
            </td>
         </tr>    
         <tr>
             <td><b><?php echo COMPANY; ?></b></td>
             <td><?php echo($R_Order['delivery_company']); ?></td>
         </tr>
         <tr>
             <td><b><?php echo FULL_NAME; ?></b></td>
             <td><?php echo($R_Order['delivery_name']); ?></td>
         </tr>
         <tr>
             <td><b><?php echo STREET_ADDRESS; ?></b></td>
             <td><?php echo($R_Order['delivery_street_address']); ?></td>
         </tr>
         <tr>
             <td><b><?php echo CITY; ?></b></td>
             <td><?php echo($R_Order['delivery_city']); ?></td>
         </tr>
         <tr>
             <td><b><?php echo STATE; ?></b></td>
             <td><?php echo($R_Order['delivery_state']); ?></td>
         </tr>
         <tr>
             <td><b><?php echo POST_CODE; ?></b></td>
             <td><?php echo($R_Order['delivery_postcode']); ?></td>
         </tr>
         <tr>
             <td><b><?php echo COUNTRY; ?></b></td>
             <td><?php echo($R_Order['delivery_country']); ?></td>
         </tr>
     </table>
    </td></tr>
 <?php 
        } else {
 ?>
 <tr>
   <td>
     <table class="table">
	   <tr>
 <td width="20%"><b><?php echo FULL_NAME; ?></b></td>
 <td width="80%"><?php echo($R_Order['customers_name']); ?></td>
 </tr>
 <tr>
     <td><b><?php echo STREET_ADDRESS; ?></b></td>
     <td><?php echo($R_Order['customers_street_address']); ?></td>
 </tr>
 <tr>
     <td><b><?php echo CITY; ?></b></td>
     <td><?php echo($R_Order['customers_city']); ?></td>
 </tr>
 <tr>
     <td><b><?php echo STATE; ?></b></td>
     <td><?php echo($R_Order['customers_state']); ?></td>
 </tr>
 <tr>
     <td><b><?php echo POST_CODE; ?></b></td>
     <td><?php echo($R_Order['customers_postcode']); ?></td>
 </tr>
 <tr>
     <td><b><?php echo COUNTRY; ?></b></td>
     <td><?php echo($R_Order['customers_country']); ?></td>
 </tr>
 </table>
 </td>
 </tr>
 <?php 
        } // end if (ENABLE_BILLING_SHIPPING_ADDR)
    } // end if($R_Order['customers_id'])
 ?>
 <tr height="45px">
 <td width="100%" colspan="2" align="center">  
<?php 
if (ENABLE_OSC_INVOICE_LINK) { 
  echo '<a href="' . OSC_INVOICE_PATH . '?oID=' . $R_Order['orders_id'] . '" target="_blank" title="Invoice" class="btn btn-success btn-default" role="button">' . ONLINE_INVOICE . '</a>';
} 
?>  
  <a href="#" title="<?php echo VIEW_RECEIPT_BUTTON_TITLE; ?>" onclick="this.blur(); popupWindow('receipt.php?OrderID=<?php echo($R_Order['orders_id']); ?>',280,550)" class="btn btn-success btn-default" role="button"><?php echo VIEW_RECEIPT; ?></a>
  
  <?php 
      if (DELETE_ORDERS == '1') {  // button says Delete Order 
         $confirm_message = DELETE_CONFIRM; 
	?>
      <?php if ($R_Order['void']=='1') {  ?>
               <a href="#" onclick="this.blur();" title="<?php echo DELETE_ORDER_BUTTON_TITLE; ?>" class="btn btn-danger btn-default disabled" role="button"><?php echo DELETE_ORDER; ?></a>
      <?php } else { ?>
            <a href="#" onclick="this.blur(); DeleteWarning('VoidOrder','order_void.php?OrderID=<?php echo($R_Order['orders_id']); ?>', '<?php echo $confirm_message; ?>');" title="<?php echo DELETE_ORDER_BUTTON_TITLE; ?>" class="btn btn-danger btn-default" role="button"><?php echo DELETE_ORDER; ?></a>
      <?php }
      ?>
   	  
  <?php } else {  // button says Void Order
      	  $confirm_message = VOID_CONFIRM;
      	  ?>
           <?php if($R_Order['void']=='1') { ?>
                  <a href="#" onclick="this.blur();" title="<?php echo VOID_ORDER_BUTTON_TITLE; ?>" class="btn btn-danger btn-default disabled" role="button"><?php echo VOID_ORDER; ?></a>
            <?php } else { ?>
                  <a href="#" onclick="this.blur(); DeleteWarning('VoidOrder','order_void.php?OrderID=<?php echo($R_Order['orders_id']); ?>', '<?php echo $confirm_message; ?>');" title="<?php echo VOID_ORDER_BUTTON_TITLE; ?>" class="btn btn-danger btn-default" role="button"><?php echo VOID_ORDER; ?></a>
             <?php  } 
            ?>

  <?php } ?>
                  
<?php if($R_Order['return_exchange'] || $R_Order['void']) { ?>

					<a href="#" onclick="this.blur();" title="<?php echo CREATE_RETURN_ORDER_BUTTON_TITLE; ?>" class="btn btn-primary btn-default disabled" role="button"><?php echo CREATE_RETURN_ORDER; ?></a>
                    
<?php } elseif (NON_INVENTORY_RETURNS_ALLOWED) { ?>
                    <a href="#" title="<?php echo CREATE_RETURN_ORDER_BUTTON_TITLE; ?>" onclick="this.blur(); window.location.href='return_exchange.php?OrderID=<?php echo($R_Order['orders_id']); ?>';" class="btn btn-primary btn-default" role="button"><?php echo CREATE_RETURN_ORDER; ?></a>
<?php } else { ?>
                    <a href="#" title="<?php echo CREATE_RETURN_ORDER_BUTTON_TITLE; ?>" onclick="this.blur(); return_confirm(<?php echo $non_inventory_count; ?>);" class="btn btn-primary btn-default" role="button"><?php echo CREATE_RETURN_ORDER; ?></a>
<?php } ?>
                     
 </td>
 </tr>
 </table>
 
 
 
 
 
 
 <br>
 
		 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
		 <tr>
                <td width="100%" colspan="5" class="tdHeader2" align="center">
		  <b><?php echo PRODUCTS_ORDERED; ?></b>
		 </td>
		 </tr>
		 <tr>
                <td width="15%" align="center"><b><?php echo MODEL; ?></b></td>
                <td width="53%" align="center"><b><?php echo NAME; ?></b></td>
                <td width="10%" align="center"><b><?php echo PRICE; ?></b></td>
                <td width="11%" align="center"><b><?php echo QUANTITY; ?></b></td>
                <td width="11%" align="right"><b><?php echo LINE_TOTAL; ?></b></td>
		 </tr>
<?php
$NumberItems = 0;

$Q_Products = mysql_query("SELECT * FROM " . ORDERS_PRODUCTS . " WHERE orders_id='" . $_REQUEST['OrderID'] . "' ORDER BY products_model");
while($R_Products = mysql_fetch_assoc($Q_Products)) {
	$NumberItems += $R_Products['products_quantity'];
?>
		 <tr>
              <td align="center"><?php echo($R_Products['products_model']); ?></td>
              <td>
		  <?php if($R_Products['products_id'] < -1000000000) { ?>
		  <?php echo($R_Products['products_name']) . ' ' . NON_INVENTORY; ?>
		  <?php } else { ?>
		  <a href="product.php?ProductID=<?php echo($R_Products['products_id']); ?>"><?php echo($R_Products['products_name']); ?></a>
		  <?php 
        if (use_attribs()) {
            $Q_Orders_Products_Attribs = mysql_query("SELECT orders_products_attributes_id FROM " . ORDERS_PRODUCTS_ATTRIBUTES . " WHERE orders_id='" . $_REQUEST['OrderID'] . "' and orders_products_id = '" . $R_Products['orders_products_id'] . "'");
            if(mysql_num_rows($Q_Orders_Products_Attribs)) {            
                $R_Attribs = new attributes($R_Products['products_id']);
                echo $R_Attribs->print_order_attribs($R_Products['orders_products_id']);
            }
        }
		  } ?>
		  </td>
              <td align="right">
		  <?php echo(number_format($R_Products['final_price'], 2, '.', '')); ?>
		  </td>
              <td align="center">
		  <?php echo($R_Products['products_quantity']); ?>
		  </td>
              <td align="right"><?php echo(number_format(($R_Products['final_price'] * $R_Products['products_quantity']), 2, '.', '')); ?></td>
		 </tr> 
<?php } 

	if (is_numeric($DiscountValue) && ($DiscountValue != 0)) { 
		?>
		  <tr>
            <td colspan="3"></td>
            <td align="center" style="font-weight: bold; color: #EF5400";><?php echo DISCOUNT; ?></td>
            <td align="right"><?php echo(number_format($DiscountValue, 2, '.', '')); ?></td>
          </tr>
<?php } ?>
		 <tr>
          <td colspan="3"></td>
          <td align="center"><b><?php echo SUBTOTAL; ?></b></td>
          <td align="right"><?php echo(number_format($Subtotal, 2, '.', '')); ?></td>
         </tr>

    <?php
        if (is_numeric($RestockValue)) { 
            ?>
              <tr>
                <td colspan="3"></td>
                <td align="center" style="font-weight: bold; color: #EF5400";><?php echo RESTOCK_FEE; ?></td>
                <td align="right"><?php echo(number_format($RestockValue, 2, '.', '')); ?></td>
              </tr>
    <?php } ?>

    <?php
        if (is_numeric($ShippingValue)) { 
            ?>
              <tr>
                <td colspan="3"></td>
                <td align="center" style="font-weight: bold; color: #EF5400";><?php echo ($ShippingMethod); ?></td>
                <td align="right"><?php echo(number_format($ShippingValue, 2, '.', '')); ?></td>
              </tr>
    <?php } ?>

<?php 
    for($i=0; $i < $tax_type_counter; $i++) {
?>
		 <tr>
		  <td colspan="3"></td>
		  <td align="center"><b><?php echo $Tax[$i]['tax_description']; ?></b></td>
		  <td align="right"><?php echo(number_format(round($Tax[$i]['tax_value'],2), 2, '.', '')); ?></td>
		 </tr>
<?php 
}
?>
		 <tr>
              <td colspan="3"></td>
              <td align="center"><b><?php echo ORDER_TOTAL; ?></b></td>
              <td align="right"><?php echo(number_format($Total, 2, '.', '')); ?></td>
		 </tr>
             
		 <tr>
             <td colspan="3" align="center"></td>
             <td align="center"><b><?php echo ITEM_COUNT; ?></b></td>
             <td align="right"><?php echo($NumberItems); ?></td>
		 </tr>
             
         <tr><td colspan="5" align="center">&nbsp;</td></tr>
         <tr><td width="100%" colspan="5" class="tdHeader2" align="center"><b><?php echo ORDER_HISTORY; ?></b></td></tr>
		 <tr>
                <td align="center"><b><?php echo DATE; ?></b></td>
                <td align="center" colspan="2"><b><?php echo COMMENTS; ?></b></td>
                <td align="center" colspan="2"><b><?php echo ORDER_STATUS; ?></b></td>
		 </tr>
             
<?php 
    $order_history_query = mysql_query("SELECT osh.date_added, osh.comments, os.orders_status_name FROM " . ORDERS_STATUS_HISTORY . " osh, " . ORDERS_STATUS . " os WHERE osh.orders_id = '" . $orders_id . "' AND osh.orders_status_id = os.orders_status_id");
    while ($order_history = mysql_fetch_array($order_history_query)) {
        echo('<tr>' . "\r\n");
        echo('  <td align="center">' . $order_history['date_added'] . '</td>' . "\r\n");
        echo('  <td colspan="2" align="left">&nbsp;' . $order_history['comments'] . '</td>' . "\r\n");
        echo('  <td colspan="2" align="center">' . $order_history['orders_status_name'] . '</td>' . "\r\n");
        echo('</tr>' . "\r\n");
}
?>   
		 </table>


      </div>

      <footer class="footer">
        <?php include("includes/footer.php"); ?>
      </footer>

    </div> <!-- /container -->
</body>
</html>
