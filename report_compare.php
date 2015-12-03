<?php 
// report_compare.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}

if($_REQUEST['RangeType']=="Through"){
	include("report_compare_range.php");
	exit();
}

include("includes/diagram.php");


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
	$subquery = " o.date_purchased LIKE '" . $_REQUEST['Day1_Year'] . "-" . $_REQUEST['Day1_Month'] . "-" . $_REQUEST['Day1_Day'] . "%' AND ";
} elseif ($_REQUEST['CompareType']=="Month") {
	$subquery = " o.date_purchased LIKE '" . $_REQUEST['Month1_Year'] . "-" . $_REQUEST['Month1_Month'] . "%' AND ";
} else {
	$subquery = " o.date_purchased LIKE '" . $_REQUEST['Year1_Year'] . "%' AND ";
}

$Q_Order = mysql_query("SELECT o.orders_id, o.payment_method, o.date_purchased FROM " . ORDERS . " o, " . ORDERS_TOTAL . " ot
							WHERE " . $posonly . " 
							" . $subquery . "
							ot.orders_id = o.orders_id AND
							o.void = '0' AND
							ot.class = 'ot_total'");

while($R_Order = mysql_fetch_assoc($Q_Order)){
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


$i = 1;

if ($_REQUEST['CompareType']=="Day") {
	$subquery = " o.date_purchased LIKE '" . $_REQUEST['Day2_Year'] . "-" . $_REQUEST['Day2_Month'] . "-" . $_REQUEST['Day2_Day'] . "%' AND ";
} elseif ($_REQUEST['CompareType']=="Month") {
	$subquery = " o.date_purchased LIKE '" . $_REQUEST['Month2_Year'] . "-" . $_REQUEST['Month2_Month'] . "%' AND ";
} else {
	$subquery = " o.date_purchased LIKE '" . $_REQUEST['Year2_Year'] . "%' AND ";
}

$Q_Order = mysql_query("SELECT o.orders_id,payment_method,date_purchased FROM " . ORDERS . " o, " . ORDERS_TOTAL . " ot
							WHERE " . $posonly . " 
							" . $subquery . "							
							ot.orders_id = o.orders_id AND
							o.void = '0' AND
							ot.class = 'ot_total'");
							



// Create Diagram ---------------------------------------------

if($_REQUEST['CompareType']=="Day"){
	$Label1 = $_REQUEST['Day1_Month'] . '-' . $_REQUEST['Day1_Day'];
	$Label2 = $_REQUEST['Day2_Month'] . '-' . $_REQUEST['Day2_Day'];
}elseif($_REQUEST['CompareType']=="Month"){
	$Label1 = $_REQUEST['Month1_Month'] . '-' . substr($_REQUEST['Month1_Year'],2,2);
	$Label2 = $_REQUEST['Month2_Month'] . '-' . substr($_REQUEST['Month2_Year'],2,2);
}else{
	$Label1 = $_REQUEST['Year1_Year'];
	$Label2 = $_REQUEST['Year2_Year'];
}

if($T_GrandTotal[0] > $T_GrandTotal[1]){
	$HighestTotal = $T_GrandTotal[0];
}else{
	$HighestTotal = $T_GrandTotal[1];
}

$HighestTotal = round($HighestTotal,-2) + 100;

$D=new Diagram();
$D->Img=@ImageCreate(740, 260) or die(GD_ERROR); 
ImageColorAllocate($D->Img, 255, 255, 255); //background color

$D->SetFrame(80, 40, 700, 240);
$D->SetBorder(-1, 14, 0, $HighestTotal);
$D->SetText("","", COMPARISON_REPORT);
$D->XScale=0;
$D->Draw("#D4E3F1", "#004080", false);
$y=750;

$i=0;
//$j=$D->ScreenX($i);
//$D->Bar($j-15, $D->ScreenY($T_TotalCost[0]), $j+15, $D->ScreenY(0), "#D5553F", $Label1, "#000000");
//$i+=1.2;
$j=$D->ScreenX($i);
$D->Bar($j-15, $D->ScreenY($T_TotalPrice[0]), $j+15, $D->ScreenY(0), "#507FD5", $Label1, "#000000");
$i+=1.2;
$j=$D->ScreenX($i);
$D->Bar($j-15, $D->ScreenY($T_TotalTax[0]), $j+15, $D->ScreenY(0), "#C4C4C4", $Label1, "#000000");
$i+=1.2;
$j=$D->ScreenX($i);
$D->Bar($j-15, $D->ScreenY($T_GrandTotal[0]), $j+15, $D->ScreenY(0), "#5BC558", $Label1, "#000000");
//$i+=1.2;
//$j=$D->ScreenX($i);
//$D->Bar($j-15, $D->ScreenY($T_Gross[0]), $j+15, $D->ScreenY(0), "#FFB400", $Label1, "#000000");


$i+=3;
//$j=$D->ScreenX($i);
//$D->Bar($j-15, $D->ScreenY($T_TotalCost[1]), $j+15, $D->ScreenY(0), "#D5553F", $Label2, "#000000");
//$i+=1.2;
$j=$D->ScreenX($i);
$D->Bar($j-15, $D->ScreenY($T_TotalPrice[1]), $j+15, $D->ScreenY(0), "#507FD5", $Label2, "#000000");
$i+=1.2;
$j=$D->ScreenX($i);
$D->Bar($j-15, $D->ScreenY($T_TotalTax[1]), $j+15, $D->ScreenY(0), "#C4C4C4", $Label2, "#000000");
$i+=1.2;
$j=$D->ScreenX($i);
$D->Bar($j-15, $D->ScreenY($T_GrandTotal[1]), $j+15, $D->ScreenY(0), "#5BC558", $Label2, "#000000");
//$i+=1.2;
//$j=$D->ScreenX($i);
//$D->Bar($j-15, $D->ScreenY($T_Gross[1]), $j+15, $D->ScreenY(0), "#FFB400", $Label2, "#000000");


ImagePng($D->Img, "graphs/bar_compare.png");
ImageDestroy($D->Img);

// ------------------------------------------------------------


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
	<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">

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
  </head>
<body>
  <div class="container">
	<?php
	
	include("includes/header.php");
	?>

<table class="table">
 <tr>
  <td width="100%">
  
  
  <?php include("includes/report_compare_select.php"); ?><br>
  
 <table class="table">
 <tr>
 <td width="100%" colspan="7" align="center">
  <b>
  <?php echo COMPARISON_REPORT2; ?> 
  </b>
 </td>
 </tr>
 <tr>
 <td width="100%" colspan="8" align="center">
 <img src="graphs/bar_compare.png?rand=<?php echo(rand()); ?>" border="0">
 </td>
 </tr>
 <tr>
 <td width="16%" align="center"><b><?php echo DATE_PURCHASED; ?></b></td>
 <td width="14%" align="center"><b><?php echo ORDER_COUNT; ?></b></td>
 <!--<td width="14%" style="background-Color:#D5553F" align="center"><b><?php echo TOTAL_COST; ?></b></td>-->
 <td width="14%" style="background-Color:#507FD5" align="center"><b><?php echo PRICE; ?></b></td>
 <td width="14%" style="background-Color:#C4C4C4" align="center"><b><?php echo TAX; ?></b></td>
 <td width="14%" style="background-Color:#5BC558" align="center"><b><?php echo TOTAL; ?></b></td>
 <!-- <td width="14%" style="background-Color:#FFB400" align="center"><b><?php echo GROSS; ?></b></td> -->
 </tr>
 <tr>
 <td width="16%" align="center"><b>
<?php
if($_REQUEST['CompareType']=="Day"){
	echo($_REQUEST['Day1_Month'] . "-" . $_REQUEST['Day1_Day'] . "-" . $_REQUEST['Day1_Year']);
}elseif($_REQUEST['CompareType']=="Month"){
	echo($_REQUEST['Month1_Month'] . "-" . $_REQUEST['Month1_Year']);
}else{
	echo($_REQUEST['Year1_Year']);
}
?>
 </b></td>
 <td width="14%" align="center"><?php echo($T_Num[0]); ?></td>
 <!-- <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($T_TotalCost[0], 2, '.', '')); ?></td> -->
 <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($T_TotalPrice[0], 2, '.', '')); ?></td>
 <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($T_TotalTax[0], 2, '.', '')); ?></td>
 <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($T_GrandTotal[0], 2, '.', '')); ?></td>
 <!-- <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($T_Gross[0], 2, '.', '')); ?></td> -->
 </tr>
 <tr>
 <td width="16%" align="center"><b>
<?php
if($_REQUEST['CompareType']=="Day"){
	echo($_REQUEST['Day2_Month'] . "-" . $_REQUEST['Day2_Day'] . "-" . $_REQUEST['Day2_Year']);
}elseif($_REQUEST['CompareType']=="Month"){
	echo($_REQUEST['Month2_Month'] . "-" . $_REQUEST['Month2_Year']);
}else{
	echo($_REQUEST['Year2_Year']);
}
?>
 </b></td>
 <td width="14%" align="center"><?php echo($T_Num[1]); ?></td>
 <!-- <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($T_TotalCost[1], 2, '.', '')); ?></td> -->
 <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($T_TotalPrice[1], 2, '.', '')); ?></td>
 <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($T_TotalTax[1], 2, '.', '')); ?></td>
 <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($T_GrandTotal[1], 2, '.', '')); ?></td>
 <!-- <td width="14%" align="right"><?php echo $default_currency_symbol . (number_format($T_Gross[1], 2, '.', '')); ?></td> -->
 </tr>
 </table><br>
 
 <table class="table">
 <tr>
 <td width="100%" colspan="8" align="center">
  <b>
  <?php echo PAYMENT_TYPE_REPORT; ?>
<?php
$i=0;
if($_REQUEST['CompareType']=="Day"){
	echo($_REQUEST['Day1_Month'] . "-" . $_REQUEST['Day1_Day']-$_REQUEST['Day1_Year']);
}elseif($_REQUEST['CompareType']=="Month"){
	echo($_REQUEST['Month1_Month'] . "-" . $_REQUEST['Month1_Year']);
}else{
	echo($_REQUEST['Year1_Year']);
}
?>
  </b>
 </td>
 </tr>
 <tr>
 <td width="16%" align="center"><b><?php echo PAYMENT_METHOD; ?></b></td>
 <td width="12%" align="center"><b><?php echo ORDER_COUNT; ?></b></td>
 <td width="12%" align="center"><b><?php echo PERCENT; ?></b></td>
 <!-- <td width="12%" align="center"><b><?php echo TOTAL_COST; ?></b></td> -->
 <td width="12%" align="right"><b><?php echo PRICE; ?></b></td>
 <td width="12%" align="right"><b><?php echo TAX; ?></b></td>
 <td width="12%" align="right"><b><?php echo TOTAL; ?></b></td>
 <!-- <td width="12%" align="center"><b><?php echo GROSS; ?></b></td> -->
 </tr>
 <?php
 $Total_Num[$i] = $Cash_Num[$i] + $CC_Num[$i] + $Check_Num[$i];
 if(!$Total_Num[$i]) $Total_Num[$i] = 1;
 ?>
 <tr class="success">
 <td width="16%" align="center"><b><?php echo CASH; ?></b></td>
 <td width="12%" align="right"><?php echo($Cash_Num[$i]); ?></td>
 <td width="12%" align="right"><?php echo(round($Cash_Num[$i]/$Total_Num[$i] * 100)); ?>%</td>
 <!-- <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalCost[$i], 2, '.', '')); ?></td> -->
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalPrice[$i], 2, '.', '')); ?></td>
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalTax[$i], 2, '.', '')); ?></td>
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($Cash_GrandTotal[$i], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_Gross[$i], 2, '.', '')); ?></td> -->
 </tr>
 <?php if($Cash_Num[$i]){ ?>
 <tr class="info">
 <td width="16%" align="center"></td>
 <td width="12%" align="center"></td>
 <td width="12%" align="center"><b><?php echo AVERAGE; ?></b></td>
 <!-- <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalCost[$i]/$Cash_Num[$i], 2, '.', '')); ?></td> -->
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalPrice[$i]/$Cash_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalTax[$i]/$Cash_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($Cash_GrandTotal[$i]/$Cash_Num[$i], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_Gross[$i]/$Cash_Num[$i], 2, '.', '')); ?></td> -->
 </tr>
 <?php } ?>
 <tr><td width="100%" colspan="8"><br></td></tr>
 <tr class="warning">
 <td width="16%" align="center"><b><?php echo CC; ?></b></td>
 <td width="12%" align="right"><?php echo($CC_Num[$i]); ?></td>
 <td width="12%" align="right"><?php echo(round($CC_Num[$i]/$Total_Num[$i] * 100)); ?>%</td>
 <!-- <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalCost[$i], 2, '.', '')); ?></td> -->
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalPrice[$i], 2, '.', '')); ?></td>
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalTax[$i], 2, '.', '')); ?></td>
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($CC_GrandTotal[$i], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_Gross[$i], 2, '.', '')); ?></td> -->
 </tr>
 <?php if($CC_Num[$i]){ ?>
 <tr>
 <td width="16%" style="background-color: #AAF3ED" align="center"></td>
 <td width="12%" style="background-color: #AAF3ED" align="center"></td>
 <td width="12%" style="background-color: #AAF3ED" align="center"><b><?php echo AVERAGE; ?></b></td>
 <!-- <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalCost[$i]/$CC_Num[$i], 2, '.', '')); ?></td> -->
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalPrice[$i]/$CC_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalTax[$i]/$CC_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_GrandTotal[$i]/$CC_Num[$i], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_Gross[$i]/$CC_Num[$i], 2, '.', '')); ?></td> -->
 </tr>
 <?php } ?>
 <tr><td width="100%" colspan="8"><br></td></tr>
 <tr>
 <td width="16%" style="background-color: #CC99FF" align="center"><b><?php echo CHEC; ?></b></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo($Check_Num[$i]); ?></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo(round($Check_Num[$i]/$Total_Num[$i] * 100)); ?>%</td>
 <!-- <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_TotalCost[$i], 2, '.', '')); ?></td> -->
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
 <!-- <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_TotalCost[$i]/$Check_Num[$i], 2, '.', '')); ?></td> -->
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_TotalPrice[$i]/$Check_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_TotalTax[$i]/$Check_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_GrandTotal[$i]/$Check_Num[$i], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_Gross[$i]/$Check_Num[$i], 2, '.', '')); ?></td> -->
 </tr>
 <?php } ?>
 </table><br>

  
 <table class="table">
 <tr>
 <td width="100%" colspan="8" align="center">
  <b>
  <?php echo PAYMENT_TYPE_REPORT; ?>
<?php
$i=1;
if($_REQUEST['CompareType']=="Day"){
	echo($_REQUEST['Day2_Month'] . "-" . $_REQUEST['Day2_Day']-$_REQUEST['Day2_Year']);
}elseif($_REQUEST['CompareType']=="Month"){
	echo($_REQUEST['Month2_Month'] . "-" . $_REQUEST['Month2_Year']);
}else{
	echo($_REQUEST['Year2_Year']);
}
?>
  </b>
 </td>
 </tr>
 <tr>
 <td width="16%" align="center"><b><?php echo PAYMENT_METHOD; ?></b></td>
 <td width="12%" align="center"><b><?php echo ORDER_COUNT; ?></b></td>
 <td width="12%" align="center"><b><?php echo PERCENT; ?></b></td>
 <!-- <td width="12%" align="center"><b><?php echo TOTAL_COST; ?></b></td>-->
 <td width="12%" align="right"><b><?php echo PRICE; ?></b></td>
 <td width="12%" align="right"><b><?php echo TAX; ?></b></td>
 <td width="12%" align="right"><b><?php echo TOTAL; ?></b></td>
 <!-- <td width="12%" align="center"><b><?php echo GROSS; ?></b></td> -->
 </tr>
 <?php
 $Total_Num[$i] = $Cash_Num[$i] + $CC_Num[$i] + $Check_Num[$i];
 if(!$Total_Num[$i]) $Total_Num[$i] = 1;
 ?>
 <tr class="success">
 <td width="16%" align="center"><b><?php echo CASH; ?></b></td>
 <td width="12%" align="right"><?php echo($Cash_Num[$i]); ?></td>
 <td width="12%" align="right"><?php echo(round($Cash_Num[$i]/$Total_Num[$i] * 100)); ?>%</td>
 <!-- <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalCost[$i], 2, '.', '')); ?></td> -->
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalPrice[$i], 2, '.', '')); ?></td>
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalTax[$i], 2, '.', '')); ?></td>
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($Cash_GrandTotal[$i], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_Gross[$i], 2, '.', '')); ?></td> -->
 </tr>
 <?php if($Cash_Num[$i]){ ?>
 <tr class="info">
 <td width="16%" align="center"></td>
 <td width="12%" align="center"></td>
 <td width="12%" align="center"><b><?php echo AVERAGE; ?></b></td>
 <!-- <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalCost[$i]/$Cash_Num[$i], 2, '.', '')); ?></td> -->
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalPrice[$i]/$Cash_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($Cash_TotalTax[$i]/$Cash_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($Cash_GrandTotal[$i]/$Cash_Num[$i], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #D7ECA0" align="right"><?php echo $default_currency_symbol . (number_format($Cash_Gross[$i]/$Cash_Num[$i], 2, '.', '')); ?></td> -->
 </tr>
 <?php } ?>
 <tr><td width="100%" colspan="8"><br></td></tr>
 <tr class="warning">
 <td width="16%" align="center"><b><?php echo CC; ?></b></td>
 <td width="12%" align="right"><?php echo($CC_Num[$i]); ?></td>
 <td width="12%" align="right"><?php echo(round($CC_Num[$i]/$Total_Num[$i] * 100)); ?>%</td>
 <!-- <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalCost[$i], 2, '.', '')); ?></td> -->
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalPrice[$i], 2, '.', '')); ?></td>
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalTax[$i], 2, '.', '')); ?></td>
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($CC_GrandTotal[$i], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_Gross[$i], 2, '.', '')); ?></td> -->
 </tr>
 <?php if($CC_Num[$i]){ ?>
 <tr class="danger">
 <td width="16%" align="center"></td>
 <td width="12%" align="center"></td>
 <td width="12%" align="center"><b><?php echo AVERAGE; ?></b></td>
 <!-- <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalCost[$i]/$CC_Num[$i], 2, '.', '')); ?></td> -->
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalPrice[$i]/$CC_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($CC_TotalTax[$i]/$CC_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($CC_GrandTotal[$i]/$CC_Num[$i], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #AAF3ED" align="right"><?php echo $default_currency_symbol . (number_format($CC_Gross[$i]/$CC_Num[$i], 2, '.', '')); ?></td> -->
 </tr>
 <?php } ?>
 <tr><td width="100%" colspan="8"><br></td></tr>
 <tr class="active">
 <td width="16%" align="center"><b><?php echo CHECK; ?></b></td>
 <td width="12%" align="right"><?php echo($Check_Num[$i]); ?></td>
 <td width="12%" align="right"><?php echo(round($Check_Num[$i]/$Total_Num[$i] * 100)); ?>%</td>
 <!-- <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_TotalCost[$i], 2, '.', '')); ?></td> -->
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($Check_TotalPrice[$i], 2, '.', '')); ?></td>
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($Check_TotalTax[$i], 2, '.', '')); ?></td>
 <td width="12%" align="right"><?php echo $default_currency_symbol . (number_format($Check_GrandTotal[$i], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_Gross[$i], 2, '.', '')); ?></td> -->
 </tr>
 <?php if($Check_Num[$i]){ ?>
 <tr class="success">
 <td width="16%" align="center"></td>
 <td width="12%" align="center"></td>
 <td width="12%" style="background-color: #CC99FF" align="center"><b><?php echo AVERAGE; ?></b></td>
 <!-- <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_TotalCost[$i]/$Check_Num[$i], 2, '.', '')); ?></td>-->
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_TotalPrice[$i]/$Check_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_TotalTax[$i]/$Check_Num[$i], 2, '.', '')); ?></td>
 <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_GrandTotal[$i]/$Check_Num[$i], 2, '.', '')); ?></td>
 <!-- <td width="12%" style="background-color: #CC99FF" align="right"><?php echo $default_currency_symbol . (number_format($Check_Gross[$i]/$Check_Num[$i], 2, '.', '')); ?></td> -->
 </tr>
 <?php } ?>
 <tr><td width="100%" colspan= "8"><br></td></tr>
 <tr>
 <td width="20%" align="center"><b><?php echo GRAND_TOTAL; ?></b></td>
 <td width="16%" align="right"><b><?php echo($Check_Num[$i] + $CC_Num[$i] + $Cash_Num[$i]); ?></b></td>
 <td width="16%" align="right"><b>100%</b></td>
 <td width="16%" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_TotalPrice[$i] + $Cash_TotalPrice[$i] + $CC_TotalPrice[$i]), 2, '.', '')); ?></b></td>
 <td width="16%" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_TotalTax[$i] + $Cash_TotalTax[$i] + $CC_TotalTax[$i]), 2, '.', '')); ?></b></td>
 <td width="16%" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_GrandTotal[$i] + $Cash_GrandTotal[$i] + $CC_GrandTotal[$i]), 2, '.', '')); ?></b></td>
  <!-- <td width="16%" align="right"><b><?php  echo $default_currency_symbol . (number_format(($Check_Gross[$i] + $Check_Gross[$i] + $Check_Gross[$i]), 2, '.', '')); ?></b></td> -->
 </tr>
 </table><br>
 
 
  </td>
 </tr>
</table>


	  <footer class="footer">
        <?php include("includes/footer.php"); ?>
      </footer>
    </div> <!-- /container -->
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
	<!-- include jquery and bootstrap -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="bootstrap-3.3.4/js/bootstrap.min.js"></script>
  </body>
</html>
