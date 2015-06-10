<?php
// restock_fee.php


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
    'Restock Method' => $_POST['restock_method']
);

$restock_method = $_POST['restock_method'];

if ($restock_method == 'absolute') {
	$restock_value = $_POST['abs_restock'];
} elseif ($restock_method == 'percent') {
	$restock_value = $_POST['percent_restock'];
} else {
	$restock_value = 0;
}

// verify amount is a valid restock amount
if (!is_numeric($restock_value)) {
	$restock_value = 0;
}

if ($restock_method == 'percent') {
	if ( ($restock_value > 100) || ($restock_value < 0) ) {
		$restock_value = 0;
	}
}

if(TestFormInput(false,$ReqVars)) {
	// add the restock
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SetRestock($restock_method, $restock_value);
	header("Location: index.php");
	exit();
} else {
	$SUCCESS=false;
}

if ($_GET['method'] == 'absolute') { 
    $abs_checked = 'checked';
    $abs_value = $_GET['value'];
    $percent_checked = '';
    $percent_value = '';
    $focus = 'abs_restock.';
} elseif ($_GET['method'] == 'percent') { 
    $abs_checked = '';
    $abs_value = '';
    $percent_checked = 'checked';
    $percent_value = $_GET['value'];
    $focus = 'percent_restock.';
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
<body onload="document.Restock.<?php echo $focus; ?>focus();">
 
<?php include("includes/header.php"); ?>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <form name="Restock" method="post">
 <input type="hidden" name="posted" value="true">
 <tr height="40px">
	 <td width="100%" class="tdBlue" colspan="2" align="center">
	  <b><?php echo RESTOCK_FEE; ?></b><br>
	 </td>
 </tr>
 
	 <td width="50%" class="tdBlue" align="center">
		 <input type=radio name="restock_method" value="absolute" <?php echo $abs_checked; ?>>
	 	<b><?php echo $default_currency_symbol; ?></b>
		<input type="text" name="abs_restock" size="4" maxlength="6" value="<?php echo $abs_value; ?>" onkeydown="if (event.keyCode == 13) document.Restock.submit();" onclick="document.Restock.restock_method[0].checked=true">
	 </td>
	 <td width="50%" class="tdBlue" align="center">
		 <input type="text" name="percent_restock" size="4" maxlength="6" value="<?php echo $percent_value; ?>" onkeydown="if (event.keyCode == 13) document.Restock.submit();" onclick="document.Restock.restock_method[1].checked=true"><b><?php echo PERCENT; ?></b>
		 <input type=radio name="restock_method" value="percent" <?php echo $percent_checked; ?>>
	 </td>
 </tr>
 <tr>
	 <td colspan="2" align="center" class="tdBlue">
	 	<input type=radio name="restock_method" value="remove">
	 	<b><?php echo REMOVE_RESTOCK_FEE; ?></b>
	 </td>
 </tr>

 <tr height="45px">
	 <td width="100%" class="tdBlue" colspan="2" align="right"><br>
    		<a class="button" title="<?php echo SUBMIT_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.Restock.submit();"><span><?php echo SUBMIT; ?></span></a>
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