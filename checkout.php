<?php 

// checkout.php


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
}

if (isset($_GET['clear_split_payments']) && $_GET['clear_split_payments'] == 'clear') {
    $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ClearSplitPayment();
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
	   <script language="JavaScript">
       
        // return the value of the radio button that is checked
        // return an empty string if none are checked, or
        // there are no radio buttons
        function getCheckedValue(radioObj) {
            if(!radioObj)
                return "";
            var radioLength = radioObj.length;
            if(radioLength == undefined)
                if(radioObj.checked)
                    return radioObj.value;
                else
                    return "";
            for(var i = 0; i < radioLength; i++) {
                if(radioObj[i].checked) {
                    return radioObj[i].value;
                }
            }
            return "";
        }

       
	   function Process() {
        var BillAddr = getCheckedValue(document.Checkout.elements['billing_address']);
        var ShipAddr = getCheckedValue(document.Checkout.elements['shipping_address']);
       
	   		var PayType = "";
            for (i=0; i<4; i++) {
				if(document.Checkout.payment_method[i].checked) PayType = document.Checkout.payment_method[i].value;
			}
            
			switch(PayType) {
				case "Cash":
                popupStaticWindow('cash','cash_add.php?payment_method=' + PayType + '&bill_addr=' + BillAddr + '&ship_addr=' + ShipAddr,300,300);
                    break;
                case "Check":
                popupStaticWindow('check','check_add.php?payment_method=' + PayType + '&bill_addr=' + BillAddr + '&ship_addr=' + ShipAddr,300,300);
                    break;
                case "Credit Card":
                popupStaticWindow('cc','cc_add.php?payment_method=' + PayType + '&bill_addr=' + BillAddr + '&ship_addr=' + ShipAddr,300,300);
                    break;
                case "AuthorizeNet":
                    popupStaticWindow('AuthorizeNet','authorizenet_aim.php?payment_method=' + PayType + '&bill_addr=' + BillAddr + '&ship_addr=' + ShipAddr,400,550);
                    break;
                default:
                }
	   }
	   </script>
</head>
<body>
  <div class="container">
    <?php include("includes/header.php"); ?>

<?php if (isset($_GET['split'])) {
	echo '<div class="alert alert-info" role="alert"><strong>' . PARTIAL_PAYMENT . ' ' . $default_currency_symbol . number_format($_GET['amount'], 2, '.', '') . '</strong></div>';
    // echo('<table><tr><td><pre>'); 
    // print_r($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]);
    // echo('</pre></td></tr></table>');
}
?>
<table class="table">
 <tr>
  <td width="100%"><form name="Checkout">
  
<?php
// print customer info if one is assigned
if($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->CustomerID){
	$CustomerID = $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->CustomerID;
	$Q_Customer = mysql_query("SELECT customers_default_address_id FROM " . CUSTOMERS . " WHERE customers_id = '" . $CustomerID . "'");
	$R_Customer = mysql_fetch_assoc($Q_Customer);
?>
  
 <table class="table">
 <tr class="info">
    <td width="50%" align="center">
     <b><font size="3"><?php echo SELECT_BILLING; ?></font></b>
 </td>
    <td width="50%" align="center">
     <b><font size="3"><?php echo SELECT_SHIPPING; ?></font></b>

   </td>
  </tr>
 
<?php
$AddressCounter=1;
$Q_Address = mysql_query("SELECT ab.*, co.countries_name, z.zone_name FROM " . COUNTRIES . " co, " . ADDRESS_BOOK. " ab LEFT JOIN " . ZONES  . " z ON (ab.entry_zone_id = z.zone_id) WHERE
	ab.customers_id = '" . $CustomerID . "' AND
	co.countries_id = ab.entry_country_id");

while($R_Address = mysql_fetch_assoc($Q_Address)) {
?>
<tr><td>

<?php /* billing addresses */ ?> 
    <table class="table table-striped">
 <tr>
           <td width="15%" align="center"><input type="radio" name="billing_address" value="<?php echo $R_Address['address_book_id'];?>" <?php if ($R_Address['address_book_id'] == $R_Customer['customers_default_address_id']) echo ' checked';?>></td>
           <td width="34%" align="left">
            <b><?php echo ADDRESS_COUNT; ?><?php echo($AddressCounter); ?></b>
 </td>
 </tr>
 <tr>
           <td><b><?php echo COMPANY; ?></b></td>
           <td colspan="2"><?php echo($R_Address['entry_company']); ?></td>
        </tr>
        <tr>
           <td><b><?php echo FULL_NAME; ?></b></td>
           <td><?php echo($R_Address['entry_firstname'] . ' ' . $R_Address['entry_lastname']); ?></td>
        </tr>
        <tr>
           <td><b><?php echo ADDRESS; ?></b></td>
           <td colspan="2"><?php echo($R_Address['entry_street_address']); ?></td>
        </tr>
        <tr>
           <td><b><?php echo SUBURB; ?></b></td>
           <td colspan="2"><?php echo($R_Address['entry_suburb']); ?></td>
 </tr>
 <tr>
           <td><b><?php echo CITY; ?></b></td>
           <td colspan="2"><?php echo($R_Address['entry_city']); ?></td>
        </tr>
        <tr>
           <td><b><?php echo STATE; ?></b></td>
           <td colspan="2"><?php echo($R_Address['zone_name']); ?></td>
        </tr>
        <tr>
           <td><b><?php echo POST_CODE; ?></b></td>
           <td colspan="2"><?php echo($R_Address['entry_postcode']); ?></td>
 </tr>
 <tr>
           <td><b><?php echo COUNTRY; ?></b></td>
           <td><?php echo($R_Address['countries_name']); ?></td>
        </tr>
    </table></td>
<?php /* end billing addresses */ ?> 

<?php /* shipping addresses */ ?> 
    <td><table class="table table-striped">
        <tr>
            <td width="15%" align="center"><input type="radio" name="shipping_address" value="<?php echo $R_Address['address_book_id']; ?>" <?php if ($R_Address['address_book_id'] == $R_Customer['customers_default_address_id']) echo ' checked';?>></td>
           <td width="34%" align="left">
            <b><?php echo ADDRESS_COUNT; ?><?php echo($AddressCounter); ?></b>
 </td>
 </tr>
 <tr>
           <td><b><?php echo COMPANY; ?></b></td>
           <td colspan="2"><?php echo($R_Address['entry_company']); ?></td>
        </tr>
        <tr>
           <td><b><?php echo FULL_NAME; ?></b></td>
           <td><?php echo($R_Address['entry_firstname'] . ' ' . $R_Address['entry_lastname']); ?></td>
 </tr>
 <tr>
           <td><b><?php echo ADDRESS; ?></b></td>
           <td colspan="2"><?php echo($R_Address['entry_street_address']); ?></td>
 </tr>
 <tr>
           <td><b><?php echo SUBURB; ?></b></td>
           <td colspan="2"><?php echo($R_Address['entry_suburb']); ?></td>
 </tr>
 <tr>
           <td><b><?php echo CITY; ?></b></td>
           <td colspan="2"><?php echo($R_Address['entry_city']); ?></td>
        </tr>
        <tr>
           <td><b><?php echo STATE; ?></b></td>
           <td colspan="2"><?php echo($R_Address['zone_name']); ?></td>
 </tr>
 <tr>
           <td><b><?php echo POST_CODE; ?></b></td>
           <td colspan="2"><?php echo($R_Address['entry_postcode']); ?></td>
 </tr>
 <tr>
           <td><b><?php echo COUNTRY; ?></b></td>
           <td><?php echo($R_Address['countries_name']); ?></td>
        </tr>
    </table></td>
<?php /* end billing addresses */ ?> 
 </tr>
        <tr><td colspan="2" class="tdHeader" align="center"></td></tr>
<?php
	$AddressCounter++;
}
?>
 
 </table><br>

<?php
}
// print order
$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->PrintFull(true);
?>
 
 <table class="table">
 <tr>
    <td width="100%" align="center">
     <b><?php echo COMPLETE_ORDER; ?></b>
    </td>
 </tr>
 <tr>
 <td width="100%" align="center">
  <b><?php echo VERIFY_INFORMATION; ?></b><br>
  <?php if(!$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->CustomerID){ ?>
  <span style="color: #993333"><?php echo NO_CUSTOMER_ASSIGNED; ?></span><br>
  <?php } ?>
   <div class="well well-lg text-center"> 


   
   <label class="radio-inline">
	  <input type="radio" name="payment_method" value="Cash" checked><?php echo CASH; ?>
	</label>
	<label class="radio-inline">
	  <input type="radio" name="payment_method" value="Check"> <?php echo CHECK; ?>
	</label>
	<label class="radio-inline">
	  <input type="radio" name="payment_method" value="Credit Card"> <?php echo CREDIT_CARD; ?>
	</label>
	<label class="radio-inline">
	  <input type="radio" name="payment_method" value="AuthorizeNet"> <?php echo CC_PROCESS; ?>
	</label>
   </div>  
  <table class="table">
    <tr height="45px">
      <td width="100%" align="center">
		<a href="#" title="<?php echo PROCESS_ORDER_BUTTON_TITLE; ?>" onClick="this.blur(); Process()" class="btn btn-success btn-default active" role="button"><?php echo PROCESS_ORDER; ?><input type="hidden" value="<?php echo PROCESS_ORDER; ?>"></a>
		<a href="#" title="<?php echo RETURN_TO_ORDER_BUTTON_TITLE; ?>" onClick="this.blur(); window.location.href='index.php'" class="btn btn-danger btn-default active" role="button"><?php echo RETURN_TO_ORDER; ?><input type="hidden" value="<?php echo RETURN_TO_ORDER; ?>"></a>
		<a href="#" title="<?php echo VIEW_RECEIPT_BUTTON_TITLE; ?>" onClick="this.blur(); popupWindow('receipt.php',280,450)" class="btn btn-default btn-default active" role="button"><?php echo VIEW_RECEIPT; ?><input type="hidden" value="<?php echo VIEW_RECEIPT; ?>"></a>
		<a href="#" title="<?php echo CLEAR_SPLIT_PAYMENTS_BUTTON_TITLE; ?>" onClick="this.blur(); window.location.href='checkout.php?clear_split_payments=clear'" class="btn btn-primary btn-default active" role="button"><?php echo CLEAR_SPLIT_PAYMENTS; ?><input type="hidden" value="<?php echo CLEAR_SPLIT_PAYMENTS; ?>"></a>
	</td>
  </tr>
 </form>
 </table><br />
 
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