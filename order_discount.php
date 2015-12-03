<?php
// order_discount.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}

if($_SESSION['CurrentOrderIndex'] == -1){
	header("Location: index.php");
	exit();
}

$ReqVars = Array (
'Discount Method' => $_POST['discount_method']
);

$discount_method = $_POST['discount_method'];

if ($discount_method == 'absolute') {
	$discount_value = $_POST['abs_discount'];
} elseif ($discount_method == 'percent') {
	$discount_value = $_POST['percent_discount'];
} else {
	$discount_value = 0;
}

// verify amount is a valid discount amount
if (!is_numeric($discount_value)) {
	$discount_value = 0;
}

if ($discount_method == 'percent') {
	if ( ($discount_value > 100) || ($discount_value < 0) ) {
		$discount_value = 0;
	}
}

if(TestFormInput(false,$ReqVars)){
	// add the discount
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SetDiscount($discount_method, $discount_value);
	header("Location: index.php");
	exit();
}else{
	$SUCCESS=false;
}

if (isset($_GET['method']) && $_GET['method'] == 'absolute') {
    $focus = 'abs_discount.';
} else {
    $focus = 'percent_discount.';
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
<body onload="document.Discount.<?php echo $focus; ?>focus();">
<div class="container"> 
  <?php include("includes/header.php"); ?>

<table class="table">
 <tr>
  <td width="100%">
  
	 <table class="table">
	   <form name="Discount" method="post">
	   <input type="hidden" name="posted" value="true">
	   <tr>
		 <td width="100%" colspan="2" align="center">
		  <b><?php echo DISCOUNT; ?></b><br>
		 </td>
	   </tr>
	 
	    <?php if ($_GET['method'] == 'absolute') { ?>
		 <td width="50%" align="center">
			 <input type=radio name="discount_method" value="absolute" checked>
			<b><?php echo $default_currency_symbol; ?></b>
			<input type="text" name="abs_discount" size="4" maxlength="6" value="<?php echo $_GET['value']; ?>" onkeydown="if (event.keyCode == 13) document.Discount.submit();" onclick="document.Discount.discount_method[0].checked=true">
		 </td>
		 <td width="50%" align="center">
			 <input type="text" name="percent_discount" size="4" maxlength="6" value="" onkeydown="if (event.keyCode == 13) document.Discount.submit();" onclick="document.Discount.discount_method[1].checked=true"><b><?php echo PERCENT; ?></b>
			 <input type=radio name="discount_method" value="percent">
		 </td>

	<?php } else { ?>
		 <td width="50%" align="center"> 
			 <input type=radio name="discount_method" value="absolute">
			<b><?php echo $default_currency_symbol; ?></b>
			<input type="text" name="abs_discount" size="4" maxlength="6" value="" onkeydown="if (event.keyCode == 13) document.Discount.submit();" onclick="document.Discount.discount_method[0].checked=true">
		 </td>
		 <td width="50%" align="center">
			 <input type="text" name="percent_discount" size="4" maxlength="6" value="<?php echo($_GET['value']); ?>" onkeydown="if (event.keyCode == 13) document.Discount.submit();" onclick="document.Discount.discount_method[1].checked=true"><b><?php echo PERCENT; ?></b>
			 <input type=radio name="discount_method" value="percent" checked>
		 </td>
	<?php } ?>	

	 </tr>
	 <tr>
		 <td colspan="2" align="center">
			<input type=radio name="discount_method" value="remove">
			<b><?php echo REMOVE_DISCOUNT; ?></b>
		 </td>
	 </tr>

	 <tr height="45px">
		 <td width="100%" colspan="2" align="center"><br>
			<a href="#" class="btn btn-success btn-sm" role="button" onclick="this.blur(); document.Discount.submit();"><?php echo SUBMIT; ?></a>
			<a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur();window.location.href='index.php'"><?php echo CANCEL; ?></a>
		 </td>
	 </tr>
	 </form>
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
