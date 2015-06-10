<?php
// report_hours.php

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


// Cost and Gross variables throughout are only useful if a contribution like Stocktaking Cost has been installed that keeps track of the wholesale purchase price of inventory
// add switch to turn on if stocktaking contrib is installed -- enable lines if installed

$HighestTotal = 0;

//$T_TotalCost = array();
$T_TotalPrice = array();
$T_TotalTax = array();
$T_GrandTotal = array();
//$T_Gross = array();
$T_Num = array();

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

// limit selection to one weekday if a day was selected
if (isset($_GET['weekday']) && $_GET['weekday'] != 'Any Day') {
    $weekday = $_GET['weekday'];
    $dayofweek = "AND DAYNAME(o.date_purchased) = '" . $weekday . "'";
}

// if posonly box is checked, only show POS orders
$posonly = "";
if ($_GET['posonly'] == 1) {
	$posonly = "o.in_store_purchase = '1' AND ";
}

$Q_Order_sql = "SELECT o.orders_id,payment_method,o.date_purchased FROM " . ORDERS . " o, " . ORDERS_TOTAL . " ot 
							WHERE " . $posonly . "
							o.date_purchased >= '" . $_REQUEST['Day1_Year'] . "-" . $_REQUEST['Day1_Month'] . "-" . $_REQUEST['Day1_Day'] . " 00:00' AND
							o.date_purchased <= '" . $_REQUEST['Day2_Year'] . "-" . $_REQUEST['Day2_Month'] . "-" . $_REQUEST['Day2_Day'] . " 23:59' AND
							ot.orders_id = o.orders_id AND
							o.void = '0' AND
							ot.class = 'ot_total' 
                            $dayofweek
                            ORDER BY o.date_purchased";

$Q_Order = mysql_query($Q_Order_sql);

while($R_Order = mysql_fetch_assoc($Q_Order)){
	$Index = date("G",strtotime($R_Order['date_purchased']));

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
	//$T_TotalCost[$Index] += $TotalCost;
	$T_TotalPrice[$Index] += $TotalPrice;
	$T_TotalTax[$Index] += $TotalTax;
	$T_GrandTotal[$Index] += $GrandTotal;
	//$T_Gross[$Index] += $Gross;
	$T_Num[$Index]++;
	
	if($R_Order['payment_method'] == CASH){
		$TdColor = "D7ECA0";
		// method totals
		//$Cash_TotalCost[$Index] += $TotalCost;
		$Cash_TotalPrice[$Index] += $TotalPrice;
		$Cash_TotalTax[$Index] += $TotalTax;
		$Cash_GrandTotal[$Index] += $GrandTotal;
		//$Cash_Gross[$Index] += $Gross;
		$Cash_Num[$Index]++;
	}elseif($R_Order['payment_method'] == CC){
		$TdColor = "AAF3ED";
		// method totals
		//$CC_TotalCost[$Index] += $TotalCost;
		$CC_TotalPrice[$Index] += $TotalPrice;
		$CC_TotalTax[$Index] += $TotalTax;
		$CC_GrandTotal[$Index] += $GrandTotal;
		//$CC_Gross[$Index] += $Gross;
		$CC_Num[$Index]++;
	}elseif($R_Order['payment_method'] == CHECK){
		$TdColor = "CC99FF";
		// method totals
		//$Check_TotalCost[$Index] += $TotalCost;
		$Check_TotalPrice[$Index] += $TotalPrice;
		$Check_TotalTax[$Index] += $TotalTax;
		$Check_GrandTotal[$Index] += $GrandTotal;
		//$Check_Gross[$Index] += $Gross;
		$Check_Num[$Index]++;
	}
}



// Create Diagram ---------------------------------------------

for($i=0; $i < 24; $i++){
	if($T_GrandTotal[$i] > $HighestTotal){
		$HighestTotal = $T_GrandTotal[$i];
	}
}
$HighestTotal = round($HighestTotal,-2) + 100;

$D=new Diagram();
$D->Img=@ImageCreate(750, 260) or die(GD_ERROR); 
ImageColorAllocate($D->Img, 255, 255, 255); //background color

$title = HOUR_DAY_REPORT;
if (isset($_GET['weekday'])) {
    $title .= ": " . $_GET['weekday'];
}

$D->SetFrame(40, 40, 740, 240);
$D->SetBorder(-1, 14, 0, $HighestTotal);
$D->SetText("","", $title);
$D->XScale=0;
$D->Draw("#D4E3F1", "#004080", false);
$y=750;
for ($i=0; $i<17; $i++){
	$j=$D->ScreenX($i/1.2);
	$D->Bar($j-15, $D->ScreenY($T_GrandTotal[$i+6]), $j+15, $D->ScreenY(0), "#5BC558", date("ga",mktime($i+6,0,0,1,1,2000)), "#000000");
}

ImagePng($D->Img, "graphs/bar_hours.png");
ImageDestroy($D->Img);

// ------------------------------------------------------------


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
  
  
  <?php include("includes/report_hours_select.php"); ?><br>
  
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <tr>
 <td width="100%" class="tdBlue" colspan="8" align="center">
  <b>
  <?php echo HOUR_DAY_REPORT2; ?>
  </b>
 </td>
 </tr>
 <tr>
 <td width="100%" colspan="8" align="center">
 <img src="graphs/bar_hours.png?rand=<?php echo(rand()); ?>" border="0">
 </td>
 </tr>
 <tr>
 <td width="16%" class="tdBlue" align="center"><b><?php echo HOUR; ?></b></td>
 <td width="14%" class="tdBlue" align="center"><b><?php echo ORDER_COUNT; ?></b></td>
 <!-- <td width="14%" class="tdBlue" align="center"><b><?php echo TOTAL_COST; ?></b></td> -->
 <td width="14%" class="tdBlue" align="right"><b><?php echo PRICE; ?></b></td>
 <td width="14%" class="tdBlue" align="right"><b><?php echo TAX; ?></b></td>
 <td width="14%" class="tdBlue" style="background-Color:#5BC558" align="right"><b><?php echo TOTAL; ?></b></td>
 <!-- <td width="14%" class="tdBlue" style="background-Color:#FFB400" align="center"><b>?php echo GROSS; ?></b></td> -->
 </tr>
<?php
for($i = 0; $i <24; $i++){
?>
 <tr>
 <td width="16%" class="tdBlue" align="center"><b><?php echo(date("g:00a",mktime($i,0,0,1,1,2000))); ?></b></td>
 <td width="14%" align="center"><?php echo($T_Num[$i]); ?></td>
 <!-- <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($T_TotalCost[$i], 2, '.', '')); ?></td> -->
 <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($T_TotalPrice[$i], 2, '.', '')); ?></td>
 <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($T_TotalTax[$i], 2, '.', '')); ?></td>
 <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($T_GrandTotal[$i], 2, '.', '')); ?></td>
 <!-- <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($T_Gross[$i], 2, '.', '')); ?></td>-->
 </tr>
<?php } ?>
 </table><br>

<?php for($i = 0; $i < 24; $i++){ ?>
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <tr>
 <td width="100%" class="tdBlue" colspan="8" align="center">
  <b>
  Payment Type Report - <?php echo(date("g:00a",mktime($i,0,0,1,1,2000))); ?>
  </b>
 </td>
 </tr>
 <tr>
 <td width="16%" class="tdBlue" align="center"><b><?php echo PAYMENT_METHOD; ?></b></td>
 <td width="12%" class="tdBlue" align="center"><b><?php echo ORDER_COUNT; ?></b></td>
 <td width="12%" class="tdBlue" align="center"><b><?php echo PERCENT; ?></b></td>
 <!-- <td width="12%" class="tdBlue" align="center"><b><?php echo TOTAL_COST; ?></b></td> -->
 <td width="12%" class="tdBlue" align="right"><b><?php echo TOTAL_PRICE; ?></b></td>
 <td width="12%" class="tdBlue" align="right"><b><?php echo TAX; ?></b></td>
 <td width="12%" class="tdBlue" align="right"><b><?php echo TOTAL; ?></b></td>
 <!-- <td width="12%" class="tdBlue" align="center"><b><?php echo GROSS; ?></b></td> -->
 </tr>
 <?php
 $Total_Num[$i] = $Cash_Num[$i] + $CC_Num[$i] + $Check_Num[$i];
 if(!$Total_Num[$i]) $Total_Num[$i] = 1;
 ?>
 <tr>
 <td width="16%" style="background-color: #D7ECA0" align="center"><b><?php echo CASH; ?></b></td>
 <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo($Cash_Num[$i]); ?></td>
 <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo(round($Cash_Num[$i]/$Total_Num[$i] * 100)); ?>%</td>
 <!-- <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalCost[$i], 2, '.', '')); ?></td>-->
 <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalPrice[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalTax[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_GrandTotal[$i], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_Gross[$i], 2, '.', '')); ?></td> -->
 </tr>
 <?php if($Cash_Num[$i]){ ?>
 <tr>
 <td width="16%" style="background-color: #D7ECA0" align="center"></td>
 <td width="12%" style="background-color: #D7ECA0" align="center"></td>
 <td width="12%" style="background-color: #D7ECA0" align="center"><b><?php echo AVERAGE; ?></b></td>
 <!-- <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalCost[$i]/$Cash_Num[$i], 2, '.', '')); ?></td> -->
 <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalPrice[$i]/$Cash_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalTax[$i]/$Cash_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_GrandTotal[$i]/$Cash_Num[$i], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_Gross[$i]/$Cash_Num[$i], 2, '.', '')); ?></td> -->
 </tr>
 <?php } ?>
 <tr><td width="100%" class="tdBlue" colspan="8"><br></td></tr>
 <tr>
 <td width="16%" style="background-color: #AAF3ED" align="center"><b><?php echo CC; ?></b></td>
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo($CC_Num[$i]); ?></td>
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo(round($CC_Num[$i]/$Total_Num[$i] * 100)); ?>%</td>
 <!-- <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalCost[$i], 2, '.', '')); ?></td>-->
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalPrice[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalTax[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_GrandTotal[$i], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_Gross[$i], 2, '.', '')); ?></td> -->
 </tr>
 <?php if($CC_Num[$i]){ ?>
 <tr>
 <td width="16%" style="background-color: #AAF3ED" align="center"></td>
 <td width="12%" style="background-color: #AAF3ED" align="center"></td>
 <td width="12%" style="background-color: #AAF3ED" align="center"><b><?php echo AVERAGE; ?></b></td>
 <!--<td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalCost[$i]/$CC_Num[$i], 2, '.', '')); ?></td>-->
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalPrice[$i]/$CC_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalTax[$i]/$CC_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_GrandTotal[$i]/$CC_Num[$i], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_Gross[$i]/$CC_Num[$i], 2, '.', '')); ?></td> -->
 </tr>
 <?php } ?>
 <tr><td width="100%" class="tdBlue" colspan="8"><br></td></tr>
 <tr>
 <td width="16%" style="background-color: #CC99FF" align="center"><b><?php echo CHECK; ?></b></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo($Check_Num[$i]); ?></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo(round($Check_Num[$i]/$Total_Num[$i] * 100)); ?>%</td>
 <!--<td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_TotalCost[$i], 2, '.', '')); ?></td>-->
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_TotalPrice[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_TotalTax[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_GrandTotal[$i], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_Gross[$i], 2, '.', '')); ?></td> -->
 </tr>
 <?php if($Check_Num[$i]){ ?>
 <tr>
 <td width="16%" style="background-color: #CC99FF" align="center"></td>
 <td width="12%" style="background-color: #CC99FF" align="center"></td>
 <td width="12%" style="background-color: #CC99FF" align="center"><b><?php echo AVERAGE; ?></b></td>
 <!--<td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_TotalCost[$i]/$Check_Num[$i], 2, '.', '')); ?></td>-->
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_TotalPrice[$i]/$Check_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_TotalTax[$i]/$Check_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_GrandTotal[$i]/$Check_Num[$i], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_Gross[$i]/$Check_Num[$i], 2, '.', '')); ?></td> -->
 </tr>
 <?php } ?>
  <tr><td width="100%" class="tdBlue" colspan= "8"><br></td></tr>
 <tr>
 <td width="20%" class="tdBlue" align="center"><b><?php echo GRAND_TOTAL; ?></b></td>
 <td width="16%" class="tdBlue" align="right"><b><?php echo($Check_Num[$i] + $CC_Num[$i] + $Cash_Num[$i]); ?></b></td>
 <td width="16%" class="tdBlue" align="right"><b>100%</b></td>
 <td width="16%" class="tdBlue" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_TotalPrice[$i] + $Cash_TotalPrice[$i] + $CC_TotalPrice[$i]), 2, '.', '')); ?></b></td>
 <td width="16%" class="tdBlue" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_TotalTax[$i] + $Cash_TotalTax[$i] + $CC_TotalTax[$i]), 2, '.', '')); ?></b></td>
 <td width="16%" class="tdBlue" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_GrandTotal[$i] + $Cash_GrandTotal[$i] + $CC_GrandTotal[$i]), 2, '.', '')); ?></b></td>
  <!-- <td width="16%" class="tdBlue" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_Gross[$i] + $Check_Gross[$i] + $Check_Gross[$i]), 2, '.', '')); ?></b></td> -->
 </tr>
 </table><br>
<?php } ?>
 
  </td>
 </tr>
</table>


<?php include("includes/footer.php"); ?>
</body>
</html>
