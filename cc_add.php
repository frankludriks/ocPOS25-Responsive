<?php
// cc_add.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);


if (isset($_GET['bill_addr']) && isset($_GET['bill_addr'])) {
    $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->BillingID = $_GET['bill_addr'];
    $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ShippingID = $_GET['ship_addr'];
}

if( (is_numeric($_POST['ccnum']) && (strlen($_POST['ccnum']) == 4)) ){ // only allow numbers, length 4.
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->PaymentMethod = CREDIT_CARD;
     $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->cc_last4 = $_POST['ccnum'];
	$Onload = "ProcessCC();";
}else{
	$Onload = "document.CCForm.CC.focus();";
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
	   
	   function ProcessCC(){
            window.opener.location.href='action.php?Action=ProcessOrder&payment_method=<?php echo($_GET['payment_method']); ?>&ccnum=<?php echo ($_POST['ccnum']); ?>';
			window.close();
	   }
	   
	   </script>
       <meta http-equiv="Content-Type" content="text/html; charset=us-ascii">
	   <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
</head>
<body onload="<?php echo($Onload); ?>">
  
 <table class="tableBorder" border="0" width="100%" cellpadding="2" cellspacing="1" align="center">
 <form name="CCForm" method="post">
 <tr>
 <td width="100%" class="tdBlue" align="center">
  <b><?php echo ENTER_LAST4 . ' (' . $RemainingTotal . ') '; ?></b>
 </td>
 </tr>
 <tr>
 <td width="100%" align="center"><input type="text" name="ccnum" size="4" maxlength="4" value="">
 </td>
 </tr>
 <tr height="45px">
 <td width="100%" class="tdBlue" align="center">
  <?php if($_SESSION['CurrentOrderIndex'] == -1){ ?>
      <a class="button-disabled" title="<?php echo PROCESS_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.CCForm.submit();"><span><?php echo PROCESS_ORDER; ?></span></a>
  <?php } else { ?>
      <a class="button" title="<?php echo PROCESS_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.CCForm.submit();"><span><?php echo PROCESS_ORDER; ?></span></a>
   <?php } ?>
  
  <a class="button" title="<?php echo CANCEL_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); window.close();"><span><?php echo CANCEL; ?></span></a>
 </td>
 </tr>
 </form>
 <script language="JavaScript">
	<!--
	document.CCForm.ccnum.focus();
	//-->
</script>
 </table>

</body>
</html>
