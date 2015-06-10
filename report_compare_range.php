<?php
// report_compare_range.php


// include("includes/db.php");
// include("includes/functions.php");

// if($_REQUEST['RangeType']!="Through"){
	// include("report_compare.php");
	// exit();
// }

include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}

include("includes/diagram.php");
include("includes/header.php");

// Cost and Gross variables throughout are only useful if a contribution like Stocktaking Cost has been installed that keeps track of the wholesale purchase price of inventory
// add switch to turn on if stocktaking contrib is installed -- enable lines if installed

//$T_TotalCost = array();
$T_TotalPrice = array();
$T_TotalTax = array();
$T_GrandTotal = array();
//$T_Gross = array();

//$Cash_TotalCost = array();
$Cash_TotalPrice = array();
$Cash_TotalTax = array();
$Cash_GrandTotal = array();
//$Cash_Gross = array();
$Cash_Num = array();

//$CC_TotalCost = array();
$CC_TotalPrice = array();
$CC_TotalTax = array();
$CC_GrandTotal = array();
//$CC_Gross = array();
$CC_Num = array();

//$Check_TotalCost = array();
$Check_TotalPrice = array();
$Check_TotalTax = array();
$Check_GrandTotal = array();
//$Check_Gross = array();
$Check_Num = array();

// if posonly box is checked, only show POS orders
$posonly = "";
if ($_GET['posonly'] == 1) {
	$posonly = "o.in_store_purchase = '1' AND ";
}

$i = 0;

if ($_REQUEST['CompareType']=="Day") {
	$subquery = "o.date_purchased >=  '" . $_REQUEST['Day1_Year'] . "-" . $_REQUEST['Day1_Month'] . "-" . $_REQUEST['Day1_Day'] . " 00:00:00' AND
				o.date_purchased <= '" . $_REQUEST['Day2_Year'] . "-" . $_REQUEST['Day2_Month'] . "-" . $_REQUEST['Day2_Day'] . " 23:59:59' AND ";
} elseif ($_REQUEST['CompareType']=="Month") {
	$subquery = "o.date_purchased >= '" . $_REQUEST['Month1_Year'] . "-" . $_REQUEST['Month1_Month'] . "-01 00:00:00' AND
				o.date_purchased <= '" . $_REQUEST['Month2_Year'] . "-" . $_REQUEST['Month2_Month'] . "-31 23:59:59' AND ";
} else {
	$subquery = "o.date_purchased >= '" . $_REQUEST['Year1_Year'] . "-01-01 00:00:00' AND
				o.date_purchased <= '" . $_REQUEST['Year2_Year'] . "-12-31 23:59:59' AND ";
}

$Q_Order = mysql_query("SELECT o.orders_id, o.payment_method, o.date_purchased FROM " . ORDERS . " o, " . ORDERS_TOTAL . " ot
							WHERE " . $posonly . "
							" . $subquery . "
							ot.orders_id = o.orders_id AND
							o.void = '0' AND
							ot.class = 'ot_total'");
							
while($R_Order = mysql_fetch_assoc($Q_Order)){

	// get array index
	$DateExp = explode(" ",$R_Order['date_purchased']);
	if($_REQUEST['CompareType']=="Day"){
		$i = $DateExp[0];
	}elseif($_REQUEST['CompareType']=="Month"){
		$DateExp = explode("-",$DateExp[0]);
		$i = "$DateExp[1]-$DateExp[0]";
	}else{
		$DateExp = explode("-",$DateExp[0]);
		$i = $DateExp[0];
	}
	// ---------------

	$tax_query = mysql_query("SELECT value FROM " . ORDERS_TOTAL . " 
						WHERE class = 'ot_tax' and orders_id = '" . $R_Order['orders_id'] . "'");
	$tax_results = mysql_fetch_array($tax_query);
	$subtotal_query = mysql_query("SELECT value FROM " . ORDERS_TOTAL . " 
						WHERE class = 'ot_subtotal' and orders_id = '" . $R_Order['orders_id'] . "'");
	$subtotal_results = mysql_fetch_array($subtotal_query);
	$total_query = mysql_query("SELECT value FROM " . ORDERS_TOTAL . " 
						WHERE class = 'ot_total' and orders_id = '" . $R_Order['orders_id'] . "'");
	$total_results = mysql_fetch_array($total_query);

	if( substr_count($R_Order['payment_method'],CC) > 0  ) { // "CC" is defined in the language file
		$R_Order['payment_method'] = CC; 
	}

	$TotalPrice = $subtotal_results['value'];
	$TotalTax = $tax_results['value'];
	$GrandTotal = $total_results['value'];
	
	//$Gross = $TotalPrice - $TotalCost;
	//$Gross = $TotalPrice;
	
	// Full totals
	//$T_TotalCost[$i] += $TotalCost;
	$T_TotalPrice[$i] += $TotalPrice;
	$T_TotalTax[$i] += $TotalTax;
	$T_GrandTotal[$i] += $GrandTotal;
	//$T_Gross[$i] += $Gross;
	$T_Num[$i]++;
	
	if($R_Order['payment_method'] == CASH){
		// method totals
		//$Cash_TotalCost[$i] += $TotalCost;
		$Cash_TotalPrice[$i] += $TotalPrice;
		$Cash_TotalTax[$i] += $TotalTax;
		$Cash_GrandTotal[$i] += $GrandTotal;
		//$Cash_Gross[$i] += $Gross;
		$Cash_Num[$i]++;
	}elseif($R_Order['payment_method'] == CC){
		// method totals
		//$CC_TotalCost[$i] += $TotalCost;
		$CC_TotalPrice[$i] += $TotalPrice;
		$CC_TotalTax[$i] += $TotalTax;
		$CC_GrandTotal[$i] += $GrandTotal;
		//$CC_Gross[$i] += $Gross;
		$CC_Num[$i]++;
	}elseif($R_Order['payment_method'] == CHECK){
		// method totals
		//$Check_TotalCost[$i] += $TotalCost;
		$Check_TotalPrice[$i] += $TotalPrice;
		$Check_TotalTax[$i] += $TotalTax;
		$Check_GrandTotal[$i] += $GrandTotal;
		//$Check_Gross[$i] += $Gross;
		$Check_Num[$i]++;
	}
}




reset($T_TotalPrice);
while(list ($key, $val) = each ($T_TotalPrice)){
	// Create Diagram ---------------------------------------------
	
	$HighestTotal = $T_GrandTotal[$key];
	$HighestTotal = round($HighestTotal,-2) + 100;
	
	$D=new Diagram();
	$D->Img=@ImageCreate(360, 260) or die(GD_ERROR); 
	ImageColorAllocate($D->Img, 255, 255, 255); //background color
	
	$D->SetFrame(80, 40, 350, 240);
	$D->SetBorder(-1, 14, 0, $HighestTotal);
	$D->SetText("","", "$key");
	$D->XScale=0;
	$D->Draw("#D4E3F1", "#004080", false);
	$y=750;
	
	$i=1;
	//$j=$D->ScreenX($i);
	//$D->Bar($j-15, $D->ScreenY($T_TotalCost[$key]), $j+15, $D->ScreenY(0), "#D5553F", "Cost", "#000000");
	//$i+=2.8;
	$j=$D->ScreenX($i);
	$D->Bar($j-15, $D->ScreenY($T_TotalPrice[$key]), $j+15, $D->ScreenY(0), "#507FD5", "Price", "#000000");
	$i+=2.8;
	$j=$D->ScreenX($i);
	$D->Bar($j-15, $D->ScreenY($T_TotalTax[$key]), $j+15, $D->ScreenY(0), "#C4C4C4","Tax", "#000000");
	$i+=2.8;
	$j=$D->ScreenX($i);
	$D->Bar($j-15, $D->ScreenY($T_GrandTotal[$key]), $j+15, $D->ScreenY(0), "#5BC558", "Total", "#000000");
	//$i+=2.8;
	//$j=$D->ScreenX($i);
	//$D->Bar($j-15, $D->ScreenY($T_Gross[$key]), $j+15, $D->ScreenY(0), "#FFB400", "Gross", "#000000");
	
	
	ImagePng($D->Img, "graphs/bar_compare_".str_replace("-","",$key).".png");
	ImageDestroy($D->Img);
	
	// ------------------------------------------------------------
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

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
  
  <?php include("includes/report_compare_select.php"); ?><br>
  
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <tr>
 <td width="100%" class="tdBlue" colspan="7" align="center">
  <b>
  <?php echo COMPARISON_REPORT2; ?> 
  </b>
 </td>
 </tr>
 <tr>
 <td width="100%" colspan="8" align="center">
<?php
reset($T_TotalPrice);
while(list ($key, $val) = each ($T_TotalPrice)){
	echo("<img src=\"graphs/bar_compare_".str_replace("-","",$key).".png?rand=".rand()."\" border=\"0\"> ");
}
?>
 </td>
 </tr>
 <tr>
 <td width="16%" class="tdBlue" align="center"><b><?php echo DATE_PURCHASED; ?></b></td>
 <td width="14%" class="tdBlue" align="center"><b><?php echo ORDER_COUNT; ?></b></td>
 <!-- <td width="14%" class="tdBlue" style="background-Color:#D5553F" align="center"><b><?php echo TOTAL_COST; ?></b></td> -->
 <td width="14%" class="tdBlue" style="background-Color:#507FD5" align="center"><b><?php echo PRICE; ?></b></td>
 <td width="14%" class="tdBlue" style="background-Color:#C4C4C4" align="center"><b><?php echo TAX; ?></b></td>
 <td width="14%" class="tdBlue" style="background-Color:#5BC558" align="center"><b><?php echo TOTAL; ?></b></td>
 <!-- <td width="14%" class="tdBlue" style="background-Color:#FFB400" align="center"><b><?php echo GROSS; ?></b></td> -->
 </tr>
<?php
reset($T_TotalPrice);
while(list ($key, $val) = each ($T_TotalPrice)){
?>
 <tr>
 <td width="16%" class="tdBlue" align="center"><b><?php echo("$key"); ?></b></td>
 <td width="14%" align="center"><?php echo($T_Num[$key]); ?></td>
 <!-- <td width="14%" align="right"><?php  echo $default_currency_symbol . (number_format($T_TotalCost[$key], 2, '.', '')); ?></td> -->
 <td width="14%" align="right"><?php  echo $default_currency_symbol . (number_format($T_TotalPrice[$key], 2, '.', '')); ?></td>
 <td width="14%" align="right"><?php  echo $default_currency_symbol . (number_format($T_TotalTax[$key], 2, '.', '')); ?></td>
 <td width="14%" align="right"><?php  echo $default_currency_symbol . (number_format($T_GrandTotal[$key], 2, '.', '')); ?></td>
 <!-- <td width="14%" align="right"><?php  echo $default_currency_symbol . (number_format($T_Gross[$key], 2, '.', '')); ?></td> -->
 </tr>
<?php
}
?>
 </table><br>
 

<?php
reset($T_TotalPrice);
while(list ($key, $val) = each ($T_TotalPrice)){

?>
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <tr>
 <td width="100%" class="tdBlue" colspan="8" align="center">
  <b>
  <?php echo PAYMENT_TYPE_REPORT; ?> <?php echo($key); ?>
  </b>
 </td>
 </tr>
 <tr>
 <td width="16%" class="tdBlue" align="center"><b><?php echo PAYMENT_METHOD; ?></b></td>
 <td width="12%" class="tdBlue" align="center"><b><?php echo ORDER_COUNT; ?></b></td>
 <td width="12%" class="tdBlue" align="center"><b><?php echo PERCENT; ?></b></td>
 <!-- <td width="12%" class="tdBlue" align="center"><b><?php echo TOTAL_COST; ?></b></td>-->
 <td width="12%" class="tdBlue" align="right"><b><?php echo PRICE; ?></b></td>
 <td width="12%" class="tdBlue" align="right"><b><?php echo TAX; ?></b></td>
 <td width="12%" class="tdBlue" align="right"><b><?php echo TOTAL; ?></b></td>
 <!-- <td width="12%" class="tdBlue" align="center"><b><?php echo GROSS; ?></b></td> -->
 </tr>
 <?php
 $Total_Num[$key] = $Cash_Num[$key] + $CC_Num[$key] + $Check_Num[$key];
 if(!$Total_Num[$key]) $Total_Num[$key] = 1;
 ?>
 <tr>
 <td width="16%" style="background-color: #D7ECA0" align="center"><b><?php echo CASH; ?></b></td>
 <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo($Cash_Num[$key]); ?></td>
 <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo(round($Cash_Num[$key]/$Total_Num[$key] * 100)); ?>%</td>
 <!--<td width="12%" style="background-color: #D7ECA0" align="right"><?php  echo $default_currency_symbol . (number_format($Cash_TotalCost[$key], 2, '.', '')); ?></td>-->
 <td width="12%" style="background-color: #D7ECA0" align="right"><?php  echo $default_currency_symbol . (number_format($Cash_TotalPrice[$key], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #D7ECA0" align="right"><?php  echo $default_currency_symbol . (number_format($Cash_TotalTax[$key], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #D7ECA0" align="right"><?php  echo $default_currency_symbol . (number_format($Cash_GrandTotal[$key], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #D7ECA0" align="right"><?php  echo $default_currency_symbol . (number_format($Cash_Gross[$key], 2, '.', '')); ?></td> -->
 </tr>
 <?php if($Cash_Num[$key]){ ?>
 <tr>
 <td width="16%" style="background-color: #D7ECA0" align="center"></td>
 <td width="12%" style="background-color: #D7ECA0" align="center"></td>
 <td width="12%" style="background-color: #D7ECA0" align="center"><b><?php echo AVERAGE; ?></b></td>
 <!--<td width="12%" style="background-color: #D7ECA0" align="right"><?php  echo $default_currency_symbol . (number_format($Cash_TotalCost[$key]/$Cash_Num[$key], 2, '.', '')); ?></td>-->
 <td width="12%" style="background-color: #D7ECA0" align="right"><?php  echo $default_currency_symbol . (number_format($Cash_TotalPrice[$key]/$Cash_Num[$key], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #D7ECA0" align="right"><?php  echo $default_currency_symbol . (number_format($Cash_TotalTax[$key]/$Cash_Num[$key], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #D7ECA0" align="right"><?php  echo $default_currency_symbol . (number_format($Cash_GrandTotal[$key]/$Cash_Num[$key], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #D7ECA0" align="right"><?php  echo $default_currency_symbol . (number_format($Cash_Gross[$key]/$Cash_Num[$key], 2, '.', '')); ?></td> -->
 </tr>
 <?php } ?>
 <tr><td width="100%" class="tdBlue" colspan="8"><br></td></tr>
 <tr>
 <td width="16%" style="background-color: #AAF3ED" align="center"><b><?php echo CC; ?></b></td>
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo($CC_Num[$key]); ?></td>
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo(round($CC_Num[$key]/$Total_Num[$key] * 100)); ?>%</td>
 <!--<td width="12%" style="background-color: #AAF3ED" align="right"><?php  echo $default_currency_symbol . (number_format($CC_TotalCost[$key], 2, '.', '')); ?></td>-->
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php  echo $default_currency_symbol . (number_format($CC_TotalPrice[$key], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php  echo $default_currency_symbol . (number_format($CC_TotalTax[$key], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php  echo $default_currency_symbol . (number_format($CC_GrandTotal[$key], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #AAF3ED" align="right"><?php  echo $default_currency_symbol . (number_format($CC_Gross[$key], 2, '.', '')); ?></td> -->
 </tr>
 <?php if($CC_Num[$key]){ ?>
 <tr>
 <td width="16%" style="background-color: #AAF3ED" align="center"></td>
 <td width="12%" style="background-color: #AAF3ED" align="center"></td>
 <td width="12%" style="background-color: #AAF3ED" align="center"><b><?php echo AVERAGE; ?></b></td>
 <!--<td width="12%" style="background-color: #AAF3ED" align="right"><?php  echo $default_currency_symbol . (number_format($CC_TotalCost[$key]/$CC_Num[$key], 2, '.', '')); ?></td>-->
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php  echo $default_currency_symbol . (number_format($CC_TotalPrice[$key]/$CC_Num[$key], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php  echo $default_currency_symbol . (number_format($CC_TotalTax[$key]/$CC_Num[$key], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php  echo $default_currency_symbol . (number_format($CC_GrandTotal[$key]/$CC_Num[$key], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #AAF3ED" align="right"><?php  echo $default_currency_symbol . (number_format($CC_Gross[$key]/$CC_Num[$key], 2, '.', '')); ?></td> -->
 </tr>
 <?php } ?>
 <tr><td width="100%" class="tdBlue" colspan="8"><br></td></tr>
 <tr>
 <td width="16%" style="background-color: #CC99FF" align="center"><b><?php echo CHECK; ?></b></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo($Check_Num[$key]); ?></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo(round($Check_Num[$key]/$Total_Num[$key] * 100)); ?>%</td>
 <!--<td width="12%" style="background-color: #CC99FF" align="right"><?php  echo $default_currency_symbol . (number_format($Check_TotalCost[$key], 2, '.', '')); ?></td>-->
 <td width="12%" style="background-color: #CC99FF" align="right"><?php  echo $default_currency_symbol . (number_format($Check_TotalPrice[$key], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php  echo $default_currency_symbol . (number_format($Check_TotalTax[$key], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php  echo $default_currency_symbol . (number_format($Check_GrandTotal[$key], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #CC99FF" align="right"><?php  echo $default_currency_symbol . (number_format($Check_Gross[$key], 2, '.', '')); ?></td> -->
 </tr>
 <?php if($Check_Num[$key]){ ?>
 <tr>
 <td width="16%" style="background-color: #CC99FF" align="center"></td>
 <td width="12%" style="background-color: #CC99FF" align="center"></td>
 <td width="12%" style="background-color: #CC99FF" align="center"><b><?php echo AVERAGE; ?></b></td>
 <!--<td width="12%" style="background-color: #CC99FF" align="right"><?php  echo $default_currency_symbol . (number_format($Check_TotalCost[$key]/$Check_Num[$key], 2, '.', '')); ?></td>-->
 <td width="12%" style="background-color: #CC99FF" align="right"><?php  echo $default_currency_symbol . (number_format($Check_TotalPrice[$key]/$Check_Num[$key], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php  echo $default_currency_symbol . (number_format($Check_TotalTax[$key]/$Check_Num[$key], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php  echo $default_currency_symbol . (number_format($Check_GrandTotal[$key]/$Check_Num[$key], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #CC99FF" align="right"><?php  echo $default_currency_symbol . (number_format($Check_Gross[$key]/$Check_Num[$key], 2, '.', '')); ?></td> -->
 </tr>
 <?php } ?>
  <tr><td width="100%" class="tdBlue" colspan= "8"><br></td></tr>
 <tr>
 <td width="20%" class="tdBlue" align="center"><b><?php echo GRAND_TOTAL; ?></b></td>
 <td width="16%" class="tdBlue" align="right"><b><?php echo($Check_Num[$key] + $CC_Num[$key] + $Cash_Num[$key]); ?></b></td>
 <td width="16%" class="tdBlue" align="right"><b>100%</b></td>
 <td width="16%" class="tdBlue" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_TotalPrice[$key] + $Cash_TotalPrice[$key] + $CC_TotalPrice[$key]), 2, '.', '')); ?></b></td>
 <td width="16%" class="tdBlue" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_TotalTax[$key] + $Cash_TotalTax[$key] + $CC_TotalTax[$key]), 2, '.', '')); ?></b></td>
 <td width="16%" class="tdBlue" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_GrandTotal[$key] + $Cash_GrandTotal[$key] + $CC_GrandTotal[$key]), 2, '.', '')); ?></b></td>
  <!-- <td width="16%" class="tdBlue" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_Gross[$key] + $Check_Gross[$key] + $Check_Gross[$key]), 2, '.', '')); ?></b></td> -->
 </tr>
 </table><br>
<?php } ?>
 
 
  </td>
 </tr>
</table>


<?php include("includes/footer.php"); ?>
</body>
</html>
