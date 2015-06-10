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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <title><?php echo($POSName) . ': ' . TITLE; ?></title>
    <link rel="Stylesheet" href="css/style.css">
    <script language="JavaScript" src="javascript.js" type="text/javascript"></script>
</head>
<body onload="document.Discount.<?php echo $focus; ?>focus();">
 
<?php include("includes/header.php"); ?>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <form name="Discount" method="post">
 <input type="hidden" name="posted" value="true">
 <tr height="40px">
	 <td width="100%" class="tdBlue" colspan="2" align="center">
	  <b><?php echo DISCOUNT; ?></b><br>
	 </td>
 </tr>
 
<?php if ($_GET['method'] == 'absolute') { ?>
	 <td width="50%" class="tdBlue" align="center">
		 <input type=radio name="discount_method" value="absolute" checked>
	 	<b><?php echo $default_currency_symbol; ?></b>
		<input type="text" name="abs_discount" size="4" maxlength="6" value="<?php echo $_GET['value']; ?>" onkeydown="if (event.keyCode == 13) document.Discount.submit();" onclick="document.Discount.discount_method[0].checked=true">
	 </td>
	 <td width="50%" class="tdBlue" align="center">
		 <input type="text" name="percent_discount" size="4" maxlength="6" value="" onkeydown="if (event.keyCode == 13) document.Discount.submit();" onclick="document.Discount.discount_method[1].checked=true"><b><?php echo PERCENT; ?></b>
		 <input type=radio name="discount_method" value="percent">
	 </td>

<?php } else { ?>
	 <td width="50%" class="tdBlue" align="center"> 
		 <input type=radio name="discount_method" value="absolute">
	 	<b><?php echo $default_currency_symbol; ?></b>
		<input type="text" name="abs_discount" size="4" maxlength="6" value="" onkeydown="if (event.keyCode == 13) document.Discount.submit();" onclick="document.Discount.discount_method[0].checked=true">
	 </td>
	 <td width="50%" class="tdBlue" align="center">
		 <input type="text" name="percent_discount" size="4" maxlength="6" value="<?php echo($_GET['value']); ?>" onkeydown="if (event.keyCode == 13) document.Discount.submit();" onclick="document.Discount.discount_method[1].checked=true"><b><?php echo PERCENT; ?></b>
		 <input type=radio name="discount_method" value="percent" checked>
	 </td>
<?php } ?>	

 </tr>
 <tr>
	 <td colspan="2" align="center" class="tdBlue">
	 	<input type=radio name="discount_method" value="remove">
	 	<b><?php echo REMOVE_DISCOUNT; ?></b>
	 </td>
 </tr>

 <tr height="45px">
	 <td width="100%" class="tdBlue" colspan="2" align="right"><br>
    		<a class="button" title="<?php echo SUBMIT_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.Discount.submit();"><span><?php echo SUBMIT; ?></span></a>
         <a class="button" title="<?php echo CANCEL_BUTTON_TITLE; ?>" href="#" onclick="this.blur();window.location.href='index.php'"><span><?php echo CANCEL; ?></span></a>
	 </td>
 </tr>
 </form>
 </table>
 
 
  </td>
 </tr>
</table>


<?php include("includes/footer.php"); ?>
</body>
</html>
