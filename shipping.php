<?php
// shipping_fee.php


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
    'Shipping Method' => $_POST['shipping_method']
);

$shipping_method = $_POST['shipping_method'];
$shipping_cost = $_POST['shipping_cost'];


// verify amount is a valid shipping amount
if (!is_numeric($shipping_cost)) {
	$shipping_cost = '';
}

if (isset($_REQUEST['remove']) && ($_REQUEST['remove'] == 'remove')) {
    $form_shipping_method = $shipping_method = '';
    $form_shipping_value = $shipping_cost = '';
} else {
    $form_shipping_method = $_GET['method'];
    $form_shipping_value = $_GET['value'];
}

if(TestFormInput(false,$ReqVars)) {
	// add the shipping
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SetShipping($shipping_method, $shipping_cost);
	header("Location: index.php");
	exit();
} else {
	$SUCCESS=false;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
   <title><?php echo($POSName) . ': ' . TITLE; ?></title>
   <link rel="Stylesheet" href="css/style.css">
   <script language="JavaScript" src="javascript.js" type="text/javascript"></script>
    <script LANGUAGE="JavaScript">
        <!--
        function validateForm() {
            if (document.Shipping.shipping_method.value == '') {
                alert('Please enter a shipping method.');
                return false;
            }
            if (document.Shipping.shipping_cost.value == '') {
                alert('Please enter a shipping cost.');
                return false;
            }
            return true;
        }
    //  End -->
    </script>
</head>
<body onload="document.Shipping.shipping_method.focus();">
 
<?php include("includes/header.php"); ?>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
 <table class="tableBorder" border="0" width="760" cellpadding="0" cellspacing="0" align="center">
 <form name="Shipping" method="post" onsubmit="return validateForm()">
 <input type="hidden" name="posted" value="true">
 <tr height="40px">
	 <td width="100%" class="tdBlue" colspan="5" align="center">
	  <b><?php echo SHIPPING; ?></b><br>
	 </td>
 </tr>
 <tr>
     <td width="10%" class="tdBlue" align="right">&nbsp;</td>
	 <td width="35%" class="tdBlue" align="left">
		&nbsp;<input type="text" name="shipping_method" size="15" maxlength="30" value="<?php echo $form_shipping_method; ?>" onkeydown="if (event.keyCode == 13 && validateForm()) document.Shipping.submit();"><b><?php echo SHIPPING_METHOD; ?></b>
		<br>
        <b><?php echo $default_currency_symbol; ?></b><input type="text" name="shipping_cost" size="4" maxlength="6" value="<?php echo $form_shipping_value; ?>" onkeydown="if (event.keyCode == 13 && validateForm()) document.Shipping.submit();">
        <b><?php echo SHIPPING_COST; ?></b>
	 </td>
     <td width="10%" class="tdBlue" align="right">&nbsp;</td>
	 <td width="35%" class="tdBlue" align="right">
	 	<?php if ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ShippingMethod != '') { ?>
        <b><?php echo REMOVE_SHIPPING; ?></b><input type=radio name="remove" value="remove">
        <?php } ?>
	 </td>
     <td width="10%" class="tdBlue" align="right">&nbsp;</td>
 </tr>

 <tr height="45px">
	 <td width="100%" class="tdBlue" colspan="5" align="right"><br>
    		<a class="button" title="<?php echo SUBMIT_BUTTON_TITLE; ?>" href="#" onclick="if (validateForm()) {this.blur(); document.Shipping.submit();}"><span><?php echo SUBMIT; ?></span></a>
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