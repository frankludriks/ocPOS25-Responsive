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

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <title><?php echo($POSName) . ': ' . TITLE; ?></title>
    <link rel="Stylesheet" href="css/style.css">
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
<?php include("includes/header.php"); ?>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
  
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <tr>
 <td width="100%" class="tdBlue" colspan="2" align="center">
  <b>Order #<?php echo($R_Order['orders_id']); ?></b>
 </td>
 </tr>
 <?php if($_REQUEST['PopReceipt']){ ?>
 <tr>
 <td width="100%" style="background-color: #<?php if($_REQUEST['Change'] >= 0){ echo("77DDA0"); }else{ echo("FF9999"); } ?>;" colspan="2" align="center">
  <b><?php echo ORDER_PROCESSED_SUCESS; ?></b>
  <?php
  if($_REQUEST['Change']){
  	echo("<br><br>" . CHANGE_DUE . "<br><span style=\"font-size:60px; font-weight: bold;\">" . $default_currency_symbol . number_format($_REQUEST['Change'], 2, '.', '')."</span>");
  }
  ?>
 </td>
 </tr>
 <?php } elseif ($R_Order['void']){ ?>
 <tr>
 <td width="100%" style="background-color: #FF9999;" colspan="2" align="center">
  <b><?php echo ORDER_VOIDED; ?></b>
 </td>
 </tr>
 <?php } ?>
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo ORDER_DATE; ?></b></td>
 <td width="80%">
 <?php
 	$DateExp = explode(" ",$R_Order['date_purchased']);
	$Time = $DateExp[1];
	$DateExp = explode("-",$DateExp[0]);
 	echo("$DateExp[1]-$DateExp[2]-$DateExp[0] $Time");
 ?></td>
 </tr>
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo PAYMENT_METHOD; ?></b></td>
 <td width="80%">
 <?php echo($R_Order['payment_method']); ?></td>
 </tr>
 <tr>
    <td colspan="2">&nbsp;</td>
 </tr>
 <?php 
    if($R_Order['customers_id']) {  
        if (ENABLE_BILLING_SHIPPING_ADDR) {
 ?>
  <tr><td width="50%">
    <table width="100%">
         <tr>
            <td width="100%" colspan="2" class="tdBlue" align="center">
             <b><font size="3"><?php echo BILLING_ADDRESS; ?></font></b>
            </td>
         </tr>
         <tr>
           <td width="30%" class="tdBlue"><b><?php echo COMPANY; ?></b></td>
           <td width="70%"><?php echo($R_Order['billing_company']); ?></td>
        </tr>
         <tr>
             <td class="tdBlue"><b><?php echo FULL_NAME; ?></b></td>
             <td><?php echo($R_Order['billing_name']); ?></td>
         </tr>
         <tr>
             <td class="tdBlue"><b><?php echo STREET_ADDRESS; ?></b></td>
             <td><?php echo($R_Order['billing_street_address']); ?></td>
         </tr>
         <tr>
             <td class="tdBlue"><b><?php echo CITY; ?></b></td>
             <td><?php echo($R_Order['billing_city']); ?></td>
         </tr>
         <tr>
             <td class="tdBlue"><b><?php echo STATE; ?></b></td>
             <td><?php echo($R_Order['billing_state']); ?></td>
         </tr>
         <tr>
             <td class="tdBlue"><b><?php echo POST_CODE; ?></b></td>
             <td><?php echo($R_Order['billing_postcode']); ?></td>
         </tr>
         <tr>
             <td class="tdBlue"><b><?php echo COUNTRY; ?></b></td>
             <td><?php echo($R_Order['billing_country']); ?></td>
         </tr>
     </table>
    </td>
    <td width="50%">
    <table width="100%">
         <tr>
            <td width="100%" colspan="2" class="tdBlue" align="center">
             <b><font size="3"><?php echo SHIPPING_ADDRESS; ?></font></b>
            </td>
         </tr>    
         <tr>
             <td class="tdBlue"><b><?php echo COMPANY; ?></b></td>
             <td><?php echo($R_Order['delivery_company']); ?></td>
         </tr>
         <tr>
             <td class="tdBlue"><b><?php echo FULL_NAME; ?></b></td>
             <td><?php echo($R_Order['delivery_name']); ?></td>
         </tr>
         <tr>
             <td class="tdBlue"><b><?php echo STREET_ADDRESS; ?></b></td>
             <td><?php echo($R_Order['delivery_street_address']); ?></td>
         </tr>
         <tr>
             <td class="tdBlue"><b><?php echo CITY; ?></b></td>
             <td><?php echo($R_Order['delivery_city']); ?></td>
         </tr>
         <tr>
             <td class="tdBlue"><b><?php echo STATE; ?></b></td>
             <td><?php echo($R_Order['delivery_state']); ?></td>
         </tr>
         <tr>
             <td class="tdBlue"><b><?php echo POST_CODE; ?></b></td>
             <td><?php echo($R_Order['delivery_postcode']); ?></td>
         </tr>
         <tr>
             <td class="tdBlue"><b><?php echo COUNTRY; ?></b></td>
             <td><?php echo($R_Order['delivery_country']); ?></td>
         </tr>
     </table>
    </td></tr>
 <?php 
        } else {
 ?>
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo FULL_NAME; ?></b></td>
 <td width="80%"><?php echo($R_Order['customers_name']); ?></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo STREET_ADDRESS; ?></b></td>
     <td><?php echo($R_Order['customers_street_address']); ?></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo CITY; ?></b></td>
     <td><?php echo($R_Order['customers_city']); ?></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo STATE; ?></b></td>
     <td><?php echo($R_Order['customers_state']); ?></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo POST_CODE; ?></b></td>
     <td><?php echo($R_Order['customers_postcode']); ?></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo COUNTRY; ?></b></td>
     <td><?php echo($R_Order['customers_country']); ?></td>
 </tr>
 <?php 
        } // end if (ENABLE_BILLING_SHIPPING_ADDR)
    } // end if($R_Order['customers_id'])
 ?>
 <tr height="45px">
 <td width="100%" class="tdBlue" colspan="2" align="center">  
<?php 
if (ENABLE_OSC_INVOICE_LINK) { 
  echo '<a class="button" title="Invoice" href="' . OSC_INVOICE_PATH . '?oID=' . $R_Order['orders_id'] . '" target="_blank"><span>' . ONLINE_INVOICE . '</span></a>';
} 
?>  
  <a class="button" title="<?php echo VIEW_RECEIPT_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); popupWindow('receipt.php?OrderID=<?php echo($R_Order['orders_id']); ?>',280,550)"><span><?php echo VIEW_RECEIPT; ?></span></a>
  
  <?php 
      if (DELETE_ORDERS == '1') {  // button says Delete Order 
         $confirm_message = DELETE_CONFIRM; 
	?>
      <?php if ($R_Order['void']=='1') {  ?>
               <a class="button-disabled" title="<?php echo DELETE_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur();"><span><?php echo DELETE_ORDER; ?></span></a>
      <?php } else { ?>
            <a class="button" title="<?php echo DELETE_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); DeleteWarning('VoidOrder','order_void.php?OrderID=<?php echo($R_Order['orders_id']); ?>', '<?php echo $confirm_message; ?>');"  style="font-weight: bold; color: #FF0000"><span><?php echo DELETE_ORDER; ?></span></a>
      <?php }
      ?>
   	  
  <?php } else {  // button says Void Order
      	  $confirm_message = VOID_CONFIRM;
      	  ?>
           <?php if($R_Order['void']=='1') { ?>
                  <a class="button-disabled" title="<?php echo VOID_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur();"><span><?php echo VOID_ORDER; ?></span></a>
            <?php } else { ?>
                  <a class="button" title="<?php echo VOID_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); DeleteWarning('VoidOrder','order_void.php?OrderID=<?php echo($R_Order['orders_id']); ?>', '<?php echo $confirm_message; ?>');"  style="font-weight: bold; color: #FF0000"><span><?php echo VOID_ORDER; ?></span></a>
             <?php  } 
            ?>

  <?php } ?>
                  
<?php if($R_Order['return_exchange'] || $R_Order['void']) { ?>

                    <a class="button-disabled" title="<?php echo CREATE_RETURN_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur();"><span><?php echo CREATE_RETURN_ORDER; ?></span></a>
                    
<?php } elseif (NON_INVENTORY_RETURNS_ALLOWED) { ?>
                    <a class="button" title="<?php echo CREATE_RETURN_ORDER_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); window.location.href='return_exchange.php?OrderID=<?php echo($R_Order['orders_id']); ?>';" style="font-weight: bold; color: #229944"><span><?php echo CREATE_RETURN_ORDER; ?></span></a>
<?php } else { ?>
                    <a class="button" title="<?php echo CREATE_RETURN_ORDER_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); return_confirm(<?php echo $non_inventory_count; ?>);" style="font-weight: bold; color: #229944"><span><?php echo CREATE_RETURN_ORDER; ?></span></a>
<?php } ?>
                     
 </td>
 </tr>
 </table><br>
 
		 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
		 <tr>
                <td width="100%" colspan="5" class="tdHeader2" align="center">
		  <b><?php echo PRODUCTS_ORDERED; ?></b>
		 </td>
		 </tr>
		 <tr>
                <td width="15%" class="tdBlue" align="center"><b><?php echo MODEL; ?></b></td>
                <td width="53%" class="tdBlue" align="center"><b><?php echo NAME; ?></b></td>
                <td width="10%" class="tdBlue" align="center"><b><?php echo PRICE; ?></b></td>
                <td width="11%" class="tdBlue" align="center"><b><?php echo QUANTITY; ?></b></td>
                <td width="11%" class="tdBlue" align="right"><b><?php echo LINE_TOTAL; ?></b></td>
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
            <td class="tdBlue" colspan="3"></td>
            <td class="tdBlue" align="center" style="font-weight: bold; color: #EF5400";><?php echo DISCOUNT; ?></td>
            <td align="right"><?php echo(number_format($DiscountValue, 2, '.', '')); ?></td>
          </tr>
<?php } ?>
		 <tr>
          <td class="tdBlue" colspan="3"></td>
          <td class="tdBlue" align="center"><b><?php echo SUBTOTAL; ?></b></td>
          <td align="right"><?php echo(number_format($Subtotal, 2, '.', '')); ?></td>
         </tr>

    <?php
        if (is_numeric($RestockValue)) { 
            ?>
              <tr>
                <td class="tdBlue" colspan="3"></td>
                <td class="tdBlue" align="center" style="font-weight: bold; color: #EF5400";><?php echo RESTOCK_FEE; ?></td>
                <td align="right"><?php echo(number_format($RestockValue, 2, '.', '')); ?></td>
              </tr>
    <?php } ?>

    <?php
        if (is_numeric($ShippingValue)) { 
            ?>
              <tr>
                <td class="tdBlue" colspan="3"></td>
                <td class="tdBlue" align="center" style="font-weight: bold; color: #EF5400";><?php echo ($ShippingMethod); ?></td>
                <td align="right"><?php echo(number_format($ShippingValue, 2, '.', '')); ?></td>
              </tr>
    <?php } ?>

<?php 
    for($i=0; $i < $tax_type_counter; $i++) {
?>
		 <tr>
		  <td class="tdBlue" colspan="3"></td>
		  <td class="tdBlue" align="center"><b><?php echo $Tax[$i]['tax_description']; ?></b></td>
		  <td align="right"><?php echo(number_format(round($Tax[$i]['tax_value'],2), 2, '.', '')); ?></td>
		 </tr>
<?php 
}
?>
		 <tr>
              <td class="tdBlue" colspan="3"></td>
              <td class="tdBlue" align="center"><b><?php echo ORDER_TOTAL; ?></b></td>
              <td align="right"><?php echo(number_format($Total, 2, '.', '')); ?></td>
		 </tr>
             
		 <tr>
             <td colspan="3" class="tdBlue" align="center"></td>
             <td class="tdBlue" align="center"><b><?php echo ITEM_COUNT; ?></b></td>
             <td align="right"><?php echo($NumberItems); ?></td>
		 </tr>
             
         <tr><td colspan="5" class="tdBlue" align="center">&nbsp;</td></tr>
         <tr><td width="100%" colspan="5" class="tdHeader2" align="center"><b><?php echo ORDER_HISTORY; ?></b></td></tr>
		 <tr>
                <td class="tdBlue" align="center"><b><?php echo DATE; ?></b></td>
                <td class="tdBlue" align="center" colspan="2"><b><?php echo COMMENTS; ?></b></td>
                <td class="tdBlue" align="center" colspan="2"><b><?php echo ORDER_STATUS; ?></b></td>
		 </tr>
             
<?php 
    $order_history_query = mysql_query("SELECT osh.date_added, osh.comments, os.orders_status_name FROM " . ORDERS_STATUS_HISTORY . " osh, " . ORDERS_STATUS . " os WHERE osh.orders_id = '" . $orders_id . "' AND osh.orders_status_id = os.orders_status_id");
    while ($order_history = mysql_fetch_array($order_history_query)) {
        echo('<tr>' . "\r\n");
        echo('  <td class="tdBlue" align="center">' . $order_history['date_added'] . '</td>' . "\r\n");
        echo('  <td class="tdBlue" colspan="2" align="left">&nbsp;' . $order_history['comments'] . '</td>' . "\r\n");
        echo('  <td class="tdBlue" colspan="2" align="center">' . $order_history['orders_status_name'] . '</td>' . "\r\n");
        echo('</tr>' . "\r\n");
}
?>   
		 </table>
 
  </td>
 </tr>
</table>


<?php include("includes/footer.php"); ?>
</body>
</html>
