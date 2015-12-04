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
	   <script language="JavaScript">
	   
	   function ProcessCheck(){
	   		window.opener.location.href='action.php?Action=ProcessOrder&payment_method=<?php echo($_REQUEST['payment_method']); ?>';
			window.close();
	   }
	   
	   </script>
	   <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
</head>
<body onload="<?php echo($Onload); ?>">
  
 <table align="center">
 <form name="CheckForm" method="post">

 <tr>
    <td width="100%" class="tdBlue" align="center"><b><?php echo ENTER_CHECK . ' (' . $RemainingTotal . ') '; ?></b><br><br>
 </td>
 </tr>
 <tr>
 <td width="100%" align="center">
 
  <form name="CheckForm" method="post" class="form-inline">
  <div class="form-group">
    <label class="sr-only" for="exampleInputAmount">Amount</label>
    <div class="input-group">
      <div class="input-group-addon"><?php echo $default_currency_symbol; ?></div>
      <input autofocus type="text" class="form-control" name="Check" id="exampleInputAmount" placeholder="Amount">
      
    </div>
  </div>
</form>

 </td>
 </tr> 
 <tr height="45px">
 <td width="100%" class="tdBlue" align="center"> 
  <?php if($_SESSION['CurrentOrderIndex'] == -1){ ?>
	  <a href="#" title="<?php echo PROCESS_ORDER_BUTTON_TITLE; ?>" onclick="this.blur(); document.CheckForm.submit();" class="btn btn-success btn-default disabled" role="button"><?php echo PROCESS_ORDER; ?></a>
  <?php } else { ?>
	  <a href="#" title="<?php echo PROCESS_ORDER_BUTTON_TITLE; ?>" onclick="this.blur(); document.CheckForm.submit();" class="btn btn-success btn-default" role="button"><?php echo PROCESS_ORDER; ?></a>
   <?php } ?>
  
  <a href="#" title="<?php echo CANCEL_BUTTON_TITLE; ?>" onclick="this.blur(); window.close();" class="btn btn-danger btn-default" role="button"><?php echo CANCEL; ?></a>
  
 </td>
 </tr>
 </form>
 </table>

</body>
</html>
