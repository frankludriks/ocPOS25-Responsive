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
  </head>
<body>
<div class="container"> 

<?php include("includes/header.php"); ?>

<table class="table">
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
 <table class="table table-striped">
 <tr>
 <td width="100%" colspan="5" align="center">
  <b><?php echo(mysql_num_rows($Q_OrderCount)); ?><?php echo ORDER_COUNT; ?></b>
 </td>
 </tr>
 <tr>
 <td width="100%" colspan="5">
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
 <td width="15%" align="center"><b><?php echo INVOICE_NUMBER; ?></b></td>
 <td width="19%" align="center"><a href="order_history.php?page=<?php echo($_REQUEST['page']); ?>&order=<?php if($_REQUEST['order']=="dpDESC"){ echo("dpASC"); }else{ echo("dpDESC"); } ?>">Order Date</a></td>
 <td width="32%" align="center"><b><?php echo CUSTOMER_NAME; ?></b></td>
 <td width="21%" align="center"><b><?php echo PAYMENT_METHOD; ?></b></td>
 <td width="13%" align="center"><a href="order_history.php?page=<?php echo($_REQUEST['page']); ?>&order=<?php if($_REQUEST['order']=="otDESC"){ echo("otASC"); }else{ echo("otDESC"); } ?>"><?php echo ORDER_TOTAL; ?></a></td>
 </tr>
<?php 
    if (mysql_num_rows($Q_Order)) {
        while($R_Order = mysql_fetch_assoc($Q_Order)) { ?>
 <tr>
 <td width="15%" align="center">
 <?php
 	echo("<a href=\"order.php?OrderID=" . $R_Order['orders_id'] . "\">" . $R_Order['orders_id'] . "</a>");
 ?>
 </td>
 <td width="19%" align="center">
 <?php
 	$DateExp = explode(" ",$R_Order['date_purchased']);
	$Time = $DateExp[1];
	$DateExp = explode("-",$DateExp[0]);
 	echo("$DateExp[1]-$DateExp[2]-$DateExp[0] $Time");
 ?>
 </td>
 <td width="32%" align="center">
  <?php if(!$R_Order['customers_name']){ ?>
  <b><?php echo("<?php echo IN_STORE_CUSTOMER; ?>"); ?></b>
  <?php }else{ ?>
  <a href="customer.php?CustomerID=<?php echo($R_Order['customers_id']); ?>"><?php echo($R_Order['customers_name']); ?></a>
  <?php } ?>
 </td>
 <td width="21%" align="center"> <?php echo($R_Order['payment_method']); ?> </td>
 <td width="13%" align="center">
  <?php echo $default_currency_symbol; ?><?php echo(number_format($R_Order['value'], 2, '.', '')); ?>
 </td>
 </tr>
<?php } 
    } 
?>
 <tr>
 <td width="100%" colspan="5">
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
