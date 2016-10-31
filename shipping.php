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
 
  <div class="container">
    <?php include("includes/header.php"); ?>
<div class="alert alert-warning text-center" role="alert"><h3><?php echo SHIPPING; ?></h3></div>
<table width="100%">
 <tr>
  <td>
  
 	   <form name="Shipping" method="post" onsubmit="return validateForm()" class="form-horizontal">
		  <div class="form-group">
			<label for="shipping_method" class="col-sm-2 control-label"><?php echo SHIPPING_METHOD; ?></label>
			<div class="col-sm-10">
			  <input name="shipping_method" class="form-control" id="shipping_method" value="<?php echo $form_shipping_method; ?>" placeholder="<?php echo SHIPPING_METHOD; ?>">
			</div>
		  </div>
		  <div class="form-group">
			<label for="shipping_cost" class="col-sm-2 control-label"><?php echo PRICE; ?></label>
			<div class="col-sm-10">
			  <input name="shipping_cost" class="form-control" id="shipping_cost" onkeydown="if (event.keyCode == 13 && validateForm()) document.Shipping.submit();" value="<?php echo $form_shipping_value; ?>" placeholder="<?php echo $default_currency_symbol; ?> <?php echo SHIPPING_COST; ?>">
			</div>
		  </div>
		  			<?php if ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ShippingMethod != '') { ?>
			<div class="radio text-center">
			  <label>
				<input type="radio" name="remove" value="remove" id="remove" checked>
				<?php echo REMOVE_SHIPPING; ?>
			  </label>
			</div>
			<?php } ?>
		  <div class="form-group">
			<div class="col-sm-offset-4 col-sm-10"><br><br>
			<a href="#" title="<?php echo SUBMIT_BUTTON_TITLE; ?>" onclick="if (validateForm()) {this.blur(); document.Shipping.submit();}" class="btn btn-success btn-default active" role="button"><?php echo SUBMIT; ?></a>
			<a href="#" title="<?php echo CANCEL_BUTTON_TITLE; ?>" onclick="this.blur();window.location.href='index.php'" class="btn btn-danger btn-default active" role="button"><?php echo CANCEL; ?></a>
			
			</div>

			</div>
		  </div>
		  <?php
		  //  Display attributes form elements
			if (use_attribs()) {
		?>	<div>
		<?php
			echo $R_all_attribs->config_form($R_product_attribs);
		?>
			</div>
		<?php	  
			} 
		?>
		</form>

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
</body>
</html>