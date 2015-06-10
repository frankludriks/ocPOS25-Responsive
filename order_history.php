<?php
// order_history.php


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
  
<?php
if(!$_REQUEST['page']){
	$_REQUEST['page']=0;
}

$BoundFront=$_REQUEST['page']*LISTPERPAGE;
$BoundBack=LISTPERPAGE;

/*if($_REQUEST['order']!="o.date_purchased DESC" && $_REQUEST['order']!="o.date_purchased" &&
   $_REQUEST['order']!="ot.value DESC" && $_REQUEST['order']!="ot.value"){
	$_REQUEST['order'] = "o.date_purchased DESC";
*/	
switch ($_REQUEST['order']) {
	case "otDESC": 
		$sort="ot.value DESC";
		break;
	case "otASC": 
		$sort="ot.value ASC";
		break;
	case "dpDESC": 
		$sort="o.date_purchased DESC";
		break;
	case "dpASC": 
		$sort="o.date_purchased ASC";
		break;
	default:
		$sort="o.date_purchased DESC";
		break;
}

if (IN_STORE_ONLY == 1) {
    $instore_only = " o.in_store_purchase = '1' AND ";
} else {
    $instore_only = '';
}

$Q_Order = mysql_query("SELECT o.orders_id, o.customers_id, o.date_purchased, o.customers_name, ot.value, o.payment_method 
							FROM " . ORDERS . " o, " . ORDERS_TOTAL . " ot 
							WHERE ot.orders_id = o.orders_id AND $instore_only
							ot.class = 'ot_total' ORDER BY $sort LIMIT $BoundFront,$BoundBack");
$Q_OrderTotal = mysql_query("SELECT o.orders_id, o.customers_id, o.date_purchased, o.customers_name, ot.value
							FROM " . ORDERS . " o, " . ORDERS_TOTAL . " ot 
							WHERE ot.orders_id = o.orders_id AND $instore_only 
							ot.class = 'ot_total' ORDER BY $sort LIMIT $BoundFront,$BoundBack");
if (IN_STORE_ONLY == 1) {
$Q_OrderCount = mysql_query("SELECT o.orders_id
							FROM " . ORDERS . " o
							WHERE o.in_store_purchase = '1' ");
} else {
    $Q_OrderCount = mysql_query("SELECT o.orders_id
							FROM " . ORDERS . " o ");
}

?>
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <tr>
 <td width="100%" class="tdBlue" colspan="5" align="center">
  <b><?php echo(mysql_num_rows($Q_OrderCount)); ?><?php echo ORDER_COUNT; ?></b>
 </td>
 </tr>
 <tr>
 <td width="100%" class="tdBlue" colspan="5">
  <b><?php echo PAGES; ?></b>
  <?php
  for($i=0;$i<mysql_num_rows($Q_OrderCount);$i+=LISTPERPAGE){
    $PageNum=$i/LISTPERPAGE;
	$PageDisplay=($i/LISTPERPAGE)+1;
  	echo("<a href=\"order_history.php?order=" . $_REQUEST['order'] . "&page=" . $PageNum . "\">" . $PageDisplay . "</a> | ");
  }
  ?>
 </td>
 </tr>
 <tr>
 <td width="120" class="tdBlue" align="center"><b><?php echo INVOICE_NUMBER; ?></b></td>
 <td width="150" class="tdBlue" align="center"><a href="order_history.php?page=<?php echo($_REQUEST['page']); ?>&order=<?php if($_REQUEST['order']=="dpDESC"){ echo("dpASC"); }else{ echo("dpDESC"); } ?>">Order Date</a></td>
 <td width="250" class="tdBlue" align="center"><b><?php echo CUSTOMER_NAME; ?></b></td>
 <td width="170" class="tdBlue" align="center"><b><?php echo PAYMENT_METHOD; ?></b></td>
 <td width="100" class="tdBlue" align="center"><a href="order_history.php?page=<?php echo($_REQUEST['page']); ?>&order=<?php if($_REQUEST['order']=="otDESC"){ echo("otASC"); }else{ echo("otDESC"); } ?>"><?php echo ORDER_TOTAL; ?></a></td>
 </tr>
<?php 
    if (mysql_num_rows($Q_Order)) {
        while($R_Order = mysql_fetch_assoc($Q_Order)) { ?>
 <tr>
 <td width="120" class="tdBlue" align="center">
 <?php
 	echo("<a href=\"order.php?OrderID=" . $R_Order['orders_id'] . "\">" . $R_Order['orders_id'] . "</a>");
 ?>
 </td>
 <td width="150" class="tdBlue" align="center">
 <?php
 	$DateExp = explode(" ",$R_Order['date_purchased']);
	$Time = $DateExp[1];
	$DateExp = explode("-",$DateExp[0]);
 	echo("$DateExp[1]-$DateExp[2]-$DateExp[0] $Time");
 ?>
 </td>
 <td width="250" align="center">
  <?php if(!$R_Order['customers_name']){ ?>
  <b><?php echo("<?php echo IN_STORE_CUSTOMER; ?>"); ?></b>
  <?php }else{ ?>
  <a href="customer.php?CustomerID=<?php echo($R_Order['customers_id']); ?>"><?php echo($R_Order['customers_name']); ?></a>
  <?php } ?>
 </td>
 <td width="170" align="center"> <?php echo($R_Order['payment_method']); ?> </td>
 <td width="100" align="center">
  <?php echo $default_currency_symbol; ?><?php echo(number_format($R_Order['value'], 2, '.', '')); ?>
 </td>
 </tr>
<?php } 
    } 
?>
 <tr>
 <td width="100%" class="tdBlue" colspan="5">
  <b>Pages:</b>
  <?php
  for($i=0;$i<mysql_num_rows($Q_OrderCount);$i+=LISTPERPAGE){
    $PageNum=$i/LISTPERPAGE;
	$PageDisplay=($i/LISTPERPAGE)+1;
  	echo("<a href=\"order_history.php?order=" . $_REQUEST['order'] . "&page=" . $PageNum . "\">" . $PageDisplay . "</a> | ");
  }
  ?>
 </td>
 </tr>
 </table>
 
 
  </td>
 </tr>
</table>


<?php include("includes/footer.php"); ?>
</body>
</html>
