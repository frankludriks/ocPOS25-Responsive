<?php
// check_add.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);


if (isset($_GET['bill_addr']) && isset($_GET['bill_addr'])) {
    $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->BillingID = $_GET['bill_addr'];
    $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ShippingID = $_GET['ship_addr'];
}

if(isset($_POST['Check']) && is_numeric($_POST['Check'])) {
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Check = $_POST['Check'];
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->PaymentMethod = CHECK;
	$Onload = "ProcessCheck();";
} else {
	$Onload = "document.CheckForm.Check.focus();";
}

$OrderTotal = number_format($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Total + round($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Tax,2), 2, '.', '');

// are there any existing partial payments?
$RemainingTotal = $OrderTotal;
if (isset($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SplitPayments[0]['PaymentMethod'])) { 
    while(list ($key, $val) = each ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SplitPayments)) {
        $RemainingTotal -= $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SplitPayments[$key]['PaymentAmount'];
    }
}
$RemainingTotal = number_format($RemainingTotal, 2, '.', '');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title><?php echo($POSName) . ': ' . TITLE; ?></title>
       <link rel="Stylesheet" href="css/style.css">
       <script language="JavaScript" src="javascript.js" type="text/javascript"></script>
	   <script language="JavaScript">
	   
	   function ProcessCheck(){
	   		window.opener.location.href='action.php?Action=ProcessOrder&payment_method=<?php echo($_REQUEST['payment_method']); ?>';
			window.close();
	   }
	   
	   </script>
       <meta http-equiv="Content-Type" content="text/html; charset=us-ascii">
	   <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
</head>
<body onload="<?php echo($Onload); ?>">
  
 <table class="tableBorder" border="0" width="100%" cellpadding="2" cellspacing="1" align="center">
 <form name="CheckForm" method="post">
<!-- 
 <tr>
 <td width="100%" class="tdBlue" align="center"><b>Enter Driver License and State</b>
 </td>
 </tr>
 <tr>
 <td width="100%" align="center">
  <input type="text" name="License" size="20" maxlength="20" value="">
 </td>
 </tr>
--> 
 <tr>
    <td width="100%" class="tdBlue" align="center"><b><?php echo ENTER_CHECK . ' (' . $RemainingTotal . ') '; ?></b>
 </td>
 </tr>
 <tr>
 <td width="100%" align="center">
  <?php echo $default_currency_symbol; ?><input type="text" name="Check" size="8" maxlength="8" value="">
 </td>
 </tr> 
 <tr height="45px">
 <td width="100%" class="tdBlue" align="center"> 
  <?php if($_SESSION['CurrentOrderIndex'] == -1){ ?>
      <a class="button-disabled" title="<?php echo PROCESS_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.CheckForm.submit();"><span><?php echo PROCESS_ORDER; ?></span></a>
  <?php } else { ?>
      <a class="button" title="<?php echo PROCESS_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.CheckForm.submit();"><span><?php echo PROCESS_ORDER; ?></span></a>
   <?php } ?>
  
  <a class="button" title="<?php echo CANCEL_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); window.close();"><span><?php echo CANCEL; ?></span></a>
  
 </td>
 </tr>
 </form>
 </table>

</body>
</html>
