<?php
// report.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title><?php echo($POSName) . ': ' . TITLE; ?></title>
       <link rel="Stylesheet" href="css/style.css">
       <script language="JavaScript" src="javascript.js" type="text/javascript"></script>
       <meta http-equiv="Content-Type" content="text/html; charset=us-ascii">
	   <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
</head>
<body>

<?php include("includes/header.php"); ?>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
  
  <?php include("includes/report_select.php"); ?><br>
  
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <tr>
 <td width="100%" class="tdBlue" colspan="8" align="center">
  <b>
  <?php echo ORDER_REPORT; ?> 
  <?php
  echo($_REQUEST['Start_Month'] . "-" . $_REQUEST['Start_Day'] . "-" . $_REQUEST['Start_Year'] . " " . $_REQUEST['Start_Hour'] . ":" . $_REQUEST['Start_Min'] . " &nbsp;&raquo;&nbsp; ");
  echo($_REQUEST['End_Month'] . "-" . $_REQUEST['End_Day'] . "-" . $_REQUEST['End_Year'] . " " . $_REQUEST['End_Hour'] . ":" . $_REQUEST['End_Min'] . "");
  ?>
  </b>
 </td>
 </tr>
 <tr>
 <td width="8%" class="tdBlue" align="center"><b><?php echo INVOICE; ?></b></td>
 <td width="18%" class="tdBlue" align="center"><b><?php echo DATE_PURCHASED; ?></b></td>
 <td width="16%" class="tdBlue" align="center"><b><?php echo PAYMENT_METHOD; ?></b></td>
 <td width="10%" class="tdBlue" align="center"><b><?php echo PRICE; ?></b></td>
 <td width="14%" class="tdBlue" align="center"><b><?php echo IN_STORE_DISCOUNT; ?></b></td>
<?php if ($_POST['posonly'] == 0) { ?>
 <td width="10%" class="tdBlue" align="center"><b><?php echo SHIPPING; ?></b></td>
<?php } ?>
 <td width="10%" class="tdBlue" align="center"><b><?php echo TAX; ?></b></td>
 <td width="14%" class="tdBlue" align="center"><b><?php echo TOTAL; ?></b></td>
 </tr>
<?php
$T_TotalPrice = 0;
$T_TotalTax = 0;
$T_TotalDiscount = 0;
$T_TotalShipping = 0;
$T_GrandTotal = 0;


$Cash_TotalPrice = 0;
$Cash_TotalTax = 0;
$Cash_GrandTotal = 0;
$Cash_Num = 0;

$CC_TotalPrice = 0;
$CC_TotalTax = 0;
$CC_GrandTotal = 0;
$CC_Num = 0;

$Check_TotalPrice = 0;
$Check_TotalTax = 0;
$Check_GrandTotal = 0;
$Check_Num = 0;


// if posonly box is checked, only show POS orders
$posonly = "";
if ($_POST['posonly'] == 1) {
	$posonly = "o.in_store_purchase = '1' AND ";
}
						
$Q_Order = mysql_query("SELECT o.orders_id, o.payment_method, o.date_purchased, o.in_store_purchase, o.void FROM " . ORDERS . " o, " . ORDERS_TOTAL . " ot
							WHERE " . $posonly . "
							o.date_purchased > '" . $_REQUEST['Start_Year'] . "-" . $_REQUEST['Start_Month'] . "-" . $_REQUEST['Start_Day'] . " " . $_REQUEST['Start_Hour'] . ":" . $_REQUEST['Start_Min'] . ":00' AND
							o.date_purchased <= '" . $_REQUEST['End_Year'] . "-" . $_REQUEST['End_Month'] . "-" . $_REQUEST['End_Day'] . " " . $_REQUEST['End_Hour'] . ":" . $_REQUEST['End_Min'] . ":59' AND
							ot.orders_id = o.orders_id AND
							ot.class = 'ot_total' ORDER BY date_purchased");
while($R_Order = mysql_fetch_assoc($Q_Order)){
	$tax_query = mysql_query("SELECT ot.value FROM " . ORDERS_TOTAL . " ot 
                        WHERE ot.class = 'ot_tax' and ot.orders_id = '" . $R_Order['orders_id'] . "'");
	$tax_results = mysql_fetch_array($tax_query);
	$discount_query = mysql_query("SELECT ot.value FROM " . ORDERS_TOTAL . " ot 
                        WHERE ot.class = 'ot_discount' and ot.orders_id = '" . $R_Order['orders_id'] . "'");
	$discount_results = mysql_fetch_array($discount_query);
	$shipping_query = mysql_query("SELECT ot.value FROM " . ORDERS_TOTAL . " ot 
                        WHERE ot.class = 'ot_shipping' and ot.orders_id = '" . $R_Order['orders_id'] . "'");
	$shipping_results = mysql_fetch_array($shipping_query);
	$subtotal_query = mysql_query("SELECT ot.value FROM " . ORDERS_TOTAL . " ot 
                        WHERE class = 'ot_subtotal' and ot.orders_id = '" . $R_Order['orders_id'] . "'");
	$subtotal_results = mysql_fetch_array($subtotal_query);
	$total_query = mysql_query("SELECT ot.value, o.void FROM " . ORDERS_TOTAL . " ot, " . ORDERS . " o 
						WHERE ot.orders_id = o.orders_id
                        AND ot.class = 'ot_total' and ot.orders_id = '" . $R_Order['orders_id'] . "'");
	$total_results = mysql_fetch_array($total_query);
	
	$TotalPrice = $subtotal_results['value'];
	$TotalTax = $tax_results['value'];
	$TotalDiscount = $discount_results['value'];
	$TotalShipping = $shipping_results['value'];
	$GrandTotal = $TotalPrice + $TotalShipping + $TotalTax;
   
   // Due to the horizontal display layout, it seems more natural to show the order subtotal before discounts.
   $TotalPrice -= $TotalDiscount; // have to subtract it since discounts are negative

	if( substr_count($R_Order['payment_method'],CC) > 0  ) { // "CC" is defined in the language file
		$R_Order['payment_method'] = CC; 
	}
	
    // only add this order's values to the report totals if the order has not been voided
    if ($total_results['void'] == '0') {
	// Full totals
	$T_TotalPrice += $TotalPrice;
	$T_TotalTax += $TotalTax;
	$T_TotalDiscount += $TotalDiscount;
	$T_TotalShipping += $TotalShipping;
	$T_GrandTotal += $GrandTotal;

	
	if($R_Order['payment_method'] == CASH){
		$TdColor = "D7ECA0";
		// method totals
		$Cash_TotalPrice += $TotalPrice;
		$Cash_TotalTax += $TotalTax;
		$Cash_GrandTotal += $GrandTotal;
		$Cash_Num++;
	}elseif($R_Order['payment_method'] == CC){
		$TdColor = "AAF3ED";
		// method totals
		$CC_TotalPrice += $TotalPrice;
		$CC_TotalTax += $TotalTax;
		$CC_GrandTotal += $GrandTotal;
		$CC_Num++;
	}elseif($R_Order['payment_method'] == CHECK){
		$TdColor = "CC99FF";
		// method totals
		$Check_TotalPrice += $TotalPrice;
		$Check_TotalTax += $TotalTax;
		$Check_GrandTotal += $GrandTotal;
		$Check_Num++;
	}
        $tdclass = 'tdBlue';
	} else {
        $tdclass = 'tdRed';
    }
	
	
	$year = (int)substr($R_Order['date_purchased'], 0, 4);
    $month = (int)substr($R_Order['date_purchased'], 5, 2);
    $day = (int)substr($R_Order['date_purchased'], 8, 2);
    $hr = (int)substr($R_Order['date_purchased'], 11, 2);
    $min = (int)substr($R_Order['date_purchased'], 14, 2);
    $sec = (int)substr($R_Order['date_purchased'], 17, 2);
	
	if (strlen($month) == 1) {$month = '0' . $month;}
	if (strlen($day) == 1) {$day = '0' . $day;}
	if (strlen($hr) == 1) {$hr = '0' . $hr;}
	if (strlen($min) == 1) {$min = '0' . $min;}
	if (strlen($sec) == 1) {$sec = '0' . $sec;}
	
	$date_purchased = $month . '-' . $day . '-' . $year . ' ' . $hr . ':' . $min . ':' . $sec;
?>
 <tr>
 <td width="8%"  class="<?php echo $tdclass; ?>" align="center"><?php echo("<a href=\"order.php?OrderID=" . $R_Order['orders_id'] . "\">" . $R_Order['orders_id'] . "</a>"); ?></td>
 <td width="18%" align="center"><?php echo($date_purchased); ?></td>
 <td width="16%" style="background-color: #<?php echo($TdColor); ?>" align="center"><?php echo($R_Order['payment_method']); ?></td>
 <td width="10%" align="right"><?php echo $default_currency_symbol . (number_format($TotalPrice, 2, '.', '')); ?></td>
 <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($TotalDiscount, 2, '.', '')); ?></td>
<?php if ($_POST['posonly'] == 0) { ?>
 <td width="10%" align="right"><?php echo $default_currency_symbol . (number_format($TotalShipping, 2, '.', '')); ?></td>
<?php } ?>
 <td width="10%" align="right"><?php echo $default_currency_symbol . (number_format($TotalTax, 2, '.', '')); ?></td>
 <td width="14%" class="<?php echo $tdclass; ?>" align="right"><?php echo $default_currency_symbol . (number_format($GrandTotal, 2, '.', '')); ?></td>
 </tr>
<?php } ?>
 <tr><td width="100%" class="tdBlue" colspan="8"><br></td></tr>
 <tr>
 <td width="8%"  class="tdBlue" align="center"><b><?php echo TOTAL; ?></b></td>
 <td width="18%" align="center"></td>
 <td width="16%" align="center"></td>
 <td width="10%" align="right"><?php echo $default_currency_symbol . (number_format($T_TotalPrice, 2, '.', '')); ?></td>
 <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($T_TotalDiscount, 2, '.', '')); ?></td>
<?php if ($_POST['posonly'] == 0) { ?>
 <td width="10%" align="right"><?php echo $default_currency_symbol . (number_format($T_TotalShipping, 2, '.', '')); ?></td>
<?php } ?>
 <td width="10%" align="right"><?php echo $default_currency_symbol . (number_format($T_TotalTax, 2, '.', '')); ?></td>
 <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($T_GrandTotal, 2, '.', '')); ?></td>
 </tr>
 </table><br /><br />
 
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <tr>
 <td width="100%" class="tdBlue" colspan="6" align="center">
  <b>
  <?php echo PAYMENT_TYPE_REPORT; ?>
  <?php
  echo($_REQUEST['Start_Month'] . "-" . $_REQUEST['Start_Day'] . "-" . $_REQUEST['Start_Year'] . " " . $_REQUEST['Start_Hour'] . ":" . $_REQUEST['Start_Min'] . " &nbsp;&raquo;&nbsp; ");
  echo($_REQUEST['End_Month'] . "-" . $_REQUEST['End_Day'] . "-" . $_REQUEST['End_Year'] . " " . $_REQUEST['End_Hour'] . ":" . $_REQUEST['End_Min'] . "");
  ?>
  </b>
 </td>
 </tr>
 <tr>
 <td width="20%" class="tdBlue" align="center"><b><?php echo PAYMENT_METHOD; ?></b></td>
 <td width="16%" class="tdBlue" align="center"><b><?php echo ORDER_COUNT; ?></b></td>
 <td width="16%" class="tdBlue" align="center"><b><?php echo PERCENT; ?></b></td>
 <td width="16%" class="tdBlue" align="right"><b><?php echo PRICE; ?></b></td>
 <td width="16%" class="tdBlue" align="right"><b><?php echo TAX; ?></b></td>
 <td width="16%" class="tdBlue" align="right"><b><?php echo TOTAL; ?></b></td>
 </tr>
 <?php
 $Total_Num = $Cash_Num + $CC_Num + $Check_Num;
 if(!$Total_Num) $Total_Num = 1;
 ?>
 <tr>
 <td width="20%" style="background-color: #D7ECA0" align="center"><b><?php echo CASH; ?></b></td>
 <td width="16%" style="background-color: #D7ECA0" align="right"><?php echo($Cash_Num); ?></td>
 <td width="16%" style="background-color: #D7ECA0" align="right"><?php echo(round($Cash_Num/$Total_Num * 100)); ?>%</td>
 <td width="16%" style="background-color: #D7ECA0" align="right"><?php  echo $default_currency_symbol . (number_format($Cash_TotalPrice, 2, '.', '')); ?></td>
 <td width="16%" style="background-color: #D7ECA0" align="right"><?php  echo $default_currency_symbol . (number_format($Cash_TotalTax, 2, '.', '')); ?></td>
 <td width="16%" style="background-color: #D7ECA0" align="right"><?php  echo $default_currency_symbol . (number_format($Cash_GrandTotal, 2, '.', '')); ?></td>
 </tr>
 <?php if($Cash_Num){ ?>
 <tr>
 <td width="20%" style="background-color: #D7ECA0" align="center"></td>
 <td width="16%" style="background-color: #D7ECA0" align="center"></td>
 <td width="16%" style="background-color: #D7ECA0" align="center"><b><?php echo AVERAGE; ?></b></td>
 <td width="16%" style="background-color: #D7ECA0" align="right"><?php  echo $default_currency_symbol . (number_format($Cash_TotalPrice/$Cash_Num, 2, '.', '')); ?></td>
 <td width="16%" style="background-color: #D7ECA0" align="right"><?php  echo $default_currency_symbol . (number_format($Cash_TotalTax/$Cash_Num, 2, '.', '')); ?></td>
 <td width="16%" style="background-color: #D7ECA0" align="right"><?php  echo $default_currency_symbol . (number_format($Cash_GrandTotal/$Cash_Num, 2, '.', '')); ?></td>
 </tr>
 <?php } ?>
 <tr><td width="100%" class="tdBlue" colspan="8"><br></td></tr>
 <tr>
 <td width="20%" style="background-color: #AAF3ED" align="center"><b><?php echo CC; ?></b></td>
 <td width="16%" style="background-color: #AAF3ED" align="right"><?php echo($CC_Num); ?></td>
 <td width="16%" style="background-color: #AAF3ED" align="right"><?php echo(round($CC_Num/$Total_Num * 100)); ?>%</td>
 <td width="16%" style="background-color: #AAF3ED" align="right"><?php  echo $default_currency_symbol . (number_format($CC_TotalPrice, 2, '.', '')); ?></td>
 <td width="16%" style="background-color: #AAF3ED" align="right"><?php  echo $default_currency_symbol . (number_format($CC_TotalTax, 2, '.', '')); ?></td>
 <td width="16%" style="background-color: #AAF3ED" align="right"><?php  echo $default_currency_symbol . (number_format($CC_GrandTotal, 2, '.', '')); ?></td>
 </tr>
 <?php if($CC_Num){ ?>
 <tr>
 <td width="20%" style="background-color: #AAF3ED" align="center"></td>
 <td width="16%" style="background-color: #AAF3ED" align="center"></td>
 <td width="16%" style="background-color: #AAF3ED" align="center"><b><?php echo AVERAGE; ?></b></td>
 <td width="16%" style="background-color: #AAF3ED" align="right"><?php  echo $default_currency_symbol . (number_format($CC_TotalPrice/$CC_Num, 2, '.', '')); ?></td>
 <td width="16%" style="background-color: #AAF3ED" align="right"><?php  echo $default_currency_symbol . (number_format($CC_TotalTax/$CC_Num, 2, '.', '')); ?></td>
 <td width="16%" style="background-color: #AAF3ED" align="right"><?php  echo $default_currency_symbol . (number_format($CC_GrandTotal/$CC_Num, 2, '.', '')); ?></td>
 </tr>
 <?php } ?>
 <?php if($Check_Num){ ?>
  <tr><td width="100%" class="tdBlue" colspan="8"><br></td></tr>
 <tr>
 <td width="20%" style="background-color: #CC99FF" align="center"><b><?php echo CHECK; ?></b></td>
 <td width="16%" style="background-color: #CC99FF" align="right"><?php echo($Check_Num); ?></td>
 <td width="16%" style="background-color: #CC99FF" align="right"><?php echo(round($Check_Num/$Total_Num * 100)); ?>%</td>
 <td width="16%" style="background-color: #CC99FF" align="right"><?php  echo $default_currency_symbol . (number_format($Check_TotalPrice, 2, '.', '')); ?></td>
 <td width="16%" style="background-color: #CC99FF" align="right"><?php  echo $default_currency_symbol . (number_format($Check_TotalTax, 2, '.', '')); ?></td>
 <td width="16%" style="background-color: #CC99FF" align="right"><?php  echo $default_currency_symbol . (number_format($Check_GrandTotal, 2, '.', '')); ?></td>
 </tr>
 <tr>
 <td width="20%" style="background-color: #CC99FF" align="center"></td>
 <td width="16%" style="background-color: #CC99FF" align="center"></td>
 <td width="16%" style="background-color: #CC99FF" align="center"><b><?php echo AVERAGE; ?></b></td>
 <td width="16%" style="background-color: #CC99FF" align="right"><?php  echo $default_currency_symbol . (number_format($Check_TotalPrice/$Check_Num, 2, '.', '')); ?></td>
 <td width="16%" style="background-color: #CC99FF" align="right"><?php  echo $default_currency_symbol . (number_format($Check_TotalTax/$Check_Num, 2, '.', '')); ?></td>
 <td width="16%" style="background-color: #CC99FF" align="right"><?php  echo $default_currency_symbol . (number_format($Check_GrandTotal/$Check_Num, 2, '.', '')); ?></td>
 </tr>
 <?php } ?>
 <tr><td width="100%" class="tdBlue" colspan="8"><br></td></tr>
 <tr>
 <td width="20%" style="background-color: #CC6699" align="center"><b><?php echo TILL_TOTAL; ?></b></td>
 <td width="16%" style="background-color: #CC6699" align="right"><b><?php echo($Check_Num + $Cash_Num); ?></b></td>
 <td width="16%" style="background-color: #CC6699" align="right"></td>
 <td width="16%" style="background-color: #CC6699" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_TotalPrice + $Cash_TotalPrice), 2, '.', '')); ?></b></td>
 <td width="16%" style="background-color: #CC6699" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_TotalTax + $Cash_TotalTax), 2, '.', '')); ?></b></td>
 <td width="16%" style="background-color: #CC6699" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_GrandTotal + $Cash_GrandTotal), 2, '.', '')); ?></b></td>
 </tr>
 <tr>
 <td width="20%" class="tdBlue" align="center"><b><?php echo GRAND_TOTAL; ?></b></td>
 <td width="16%" class="tdBlue" align="right"><b><?php echo($Check_Num + $CC_Num + $Cash_Num); ?></b></td>
 <td width="16%" class="tdBlue" align="right"><b>100%</b></td>
 <td width="16%" class="tdBlue" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_TotalPrice + $Cash_TotalPrice + $CC_TotalPrice), 2, '.', '')); ?></b></td>
 <td width="16%" class="tdBlue" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_TotalTax + $Cash_TotalTax + $CC_TotalTax), 2, '.', '')); ?></b></td>
 <td width="16%" class="tdBlue" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_GrandTotal + $Cash_GrandTotal + $CC_GrandTotal), 2, '.', '')); ?></b></td>
 </tr>
 </table>
 
  </td>
 </tr>
</table>


<?php include("includes/footer.php"); ?>
</body>
</html>
