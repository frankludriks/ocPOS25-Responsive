<?php
// customer_edit_address.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}

if (isset($_GET['AddressID'])) $address_id = sanitize($_GET['AddressID']);
if (isset($_GET['CustomerID'])) $customer_id = sanitize($_GET['CustomerID']);

if (isset($_GET['submit']) && ($_GET['submit'] == 1) && (isset($_POST['fname']) && (isset($_POST['lname']))) ) {

    foreach ($_POST as $k=>$v) {
        $_POST[$k] = sanitize($v);
    }

    mysql_query("UPDATE " . ADDRESS_BOOK . " SET
        entry_firstname = '" . $_POST['fname'] . "',
        entry_lastname = '" . $_POST['lname'] . "',
        entry_company = '" . $_POST['company'] . "',
        entry_street_address = '" . $_POST['street'] . "',
        entry_city = '" . $_POST['city'] . "',
        entry_suburb='" . $_POST['suburb'] . "',
        entry_zone_id = '" . $_POST['state'] . "',
        entry_postcode = '" . $_POST['zip'] . "',
        entry_country_id = '" . $_POST['country'] . "'
        WHERE customers_id = '" . $customer_id . "'
        AND address_book_id = '" . $address_id . "'");

// verify correct zone name (state if you're in the US) before writing the state and entry_zone_id
    $zone_lookup_query = mysql_query("SELECT * FROM " . ZONES . "
        WHERE zone_id = '" . $_POST['state'] . "'
        OR zone_code = '" . $_POST['state'] . "'
        LIMIT 1");
    if(mysql_num_rows($zone_lookup_query)) {
        $zone_id = mysql_fetch_assoc($zone_lookup_query);
        mysql_query("UPDATE " . ADDRESS_BOOK . " SET
            entry_zone_id = '" . $zone_id['zone_id'] . "',
            entry_state = '" . $zone_id['zone_name'] . "'
            WHERE customers_id = '" . $customer_id . "'
            AND address_book_id = '" . $address_id . "'");

        mysql_query("UPDATE " . CUSTOMERS_INFO . " SET
            customers_info_date_account_last_modified = now()
            WHERE customers_info_id = '" . $customer_id . "'");
    }
    $updated = 1;
}


$Q_Customer = mysql_query("SELECT * FROM " . CUSTOMERS . " WHERE customers_id = '" . $customer_id . "' LIMIT 1");
if(mysql_num_rows($Q_Customer)) {
	$R_Customer = mysql_fetch_assoc($Q_Customer);
} else {
	header("Location: index.php");
}

$Q_Address = mysql_query("SELECT ab.*, co.countries_name, z.zone_name, dayofmonth(c.customers_dob) as day, month(c.customers_dob) as month, year(c.customers_dob) as year FROM " . ADDRESS_BOOK. " ab LEFT JOIN " . CUSTOMERS . " c on (ab.customers_id = c.customers_id) LEFT JOIN " . ZONES . " z on (ab.entry_zone_id = z.zone_id), " . COUNTRIES . " co WHERE ab.address_book_id = '" . $address_id . "' AND
	ab.customers_id = '" . $customer_id . "' AND
	co.countries_id = ab.entry_country_id");

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
   <form class="form-horizontal" name="EditAddress" action="customer_edit_address.php?CustomerID=<?php echo $customer_id; ?>&AddressID=<?php echo $address_id; ?>&submit=1" method="post">
	<div class="panel panel-primary">
		<div class="panel-heading">
		  <h3 class="panel-title text-center"><?php echo(CUSTOMER_INFORMATION . ' ' . $R_Customer['customers_firstname'] . ' ' . $R_Customer['customers_lastname']); ?></h3>
		</div>
		<div class="panel-body">
		<?php
		if (mysql_affected_rows()==0) {
		   $invalid_address = 1;
		   echo '<div class="alert alert-danger" role="alert">' . NO_VALID_ADDRESS_RETURNED . '</div>';
		}

		while($R_Address = mysql_fetch_assoc($Q_Address)) {

		?>
		  <div class="form-group">
			<label for="company" class="col-sm-2 control-label"><?php echo COMPANY; ?></label>
			  <div class="col-sm-10">
				<input type="text" name="company" value="<?php echo($R_Address['entry_company']); ?>" class="form-control" id="company" placeholder="<?php echo COMPANY; ?>">
			  </div>
		  </div>
		  <div class="form-group">
			<label for="fname" class="col-sm-2 control-label"><?php echo FIRST_NAME; ?></label>
			  <div class="col-sm-10">
				<input type="text" name="fname" value="<?php echo($R_Address['entry_firstname']); ?>" class="form-control" id="fname" placeholder="<?php echo FIRST_NAME; ?>">
				<span class="glyphicon glyphicon-asterisk form-control-feedback inputRequirement"></span>
			  </div>
		  </div>
		  <div class="form-group">
			<label for="lname" class="col-sm-2 control-label"><?php echo LAST_NAME; ?></label>
			  <div class="col-sm-10">
				<input type="text" name="lname" value="<?php echo($R_Address['entry_lastname']); ?>" class="form-control" id="lname" placeholder="<?php echo LAST_NAME; ?>">
				<span class="glyphicon glyphicon-asterisk form-control-feedback inputRequirement"></span>
			  </div>
		  </div>
		  <div class="form-group">
			<label for="street" class="col-sm-2 control-label"><?php echo ADDRESS; ?></label>
			  <div class="col-sm-10">
				<input type="text" name="street" value="<?php echo($R_Address['entry_street_address']); ?>" class="form-control" id="street" placeholder="<?php echo ADDRESS; ?>">
			  </div>
		  </div>
		  <div class="form-group">
			<label for="suburb" class="col-sm-2 control-label"><?php echo SUBURB; ?></label>
			  <div class="col-sm-10">
				<input type="text" name="suburb" value="<?php echo($R_Address['entry_suburb']); ?>" class="form-control" id="suburb" placeholder="<?php echo SUBURB; ?>">
			  </div>
		  </div>
		  <div class="form-group">
			<label for="city" class="col-sm-2 control-label"><?php echo CITY; ?></label>
			  <div class="col-sm-10">
				<input type="text" name="city" value="<?php echo($R_Address['entry_city']); ?>" class="form-control" id="city" placeholder="<?php echo CITY; ?>">
			  </div>
		  </div>
		  <div class="form-group">
			<label for="entry_zone_id" class="col-sm-2 control-label"><?php echo STATE; ?></label>
				<div class="col-sm-10">
					<select class="form-control" name="state">
					<?php
					$default_zone_query = mysql_query("select configuration_value as zone_id from " . CONFIGURATION . " where configuration_key = 'STORE_ZONE'");
					$default_zone = mysql_fetch_array($default_zone_query);
							 
					$Q_Zone = mysql_query("SELECT * FROM " . ZONES . " ORDER BY zone_name");
						while($R_Zone = mysql_fetch_assoc($Q_Zone)){
							if($_POST['entry_zone_id'] == $R_Zone['zone_id']){
								echo("<option value=\"" . $R_Zone['zone_id'] . "\" selected>" . $R_Zone['zone_name'] . "</option>\n");
							}elseif(!$_POST['entry_zone_id'] && $R_Zone['zone_id'] == $default_zone['zone_id']){ 
								echo("<option value=\"" . $R_Zone['zone_id'] . "\" selected>" . $R_Zone['zone_name'] . "</option>\n");
							}else{
								echo("<option value=\"" . $R_Zone['zone_id'] . "\">" . $R_Zone['zone_name'] . "</option>\n");
							}
						}
					?>
					</select>
				</div>
		  </div>
		  <div class="form-group">
			<label for="zip" class="col-sm-2 control-label"><?php echo POST_CODE; ?></label>
			  <div class="col-sm-10">
				<input type="text" name="zip" value="<?php echo($R_Address['entry_postcode']); ?>" class="form-control" id="zip" placeholder="<?php echo POST_CODE; ?>">
			  </div>
		  </div>
		  <div class="form-group">
			<label for="entry_country_id" class="col-sm-2 control-label"><?php echo COUNTRY; ?></label>
				<div class="col-sm-10">
					<select class="form-control" name="country">
					<?php
					$Q_Country = mysql_query("SELECT * FROM " . COUNTRIES . " ORDER BY countries_name");
					 while($R_Country = mysql_fetch_assoc($Q_Country)){
						if($R_Address['entry_country_id'] == $R_Country['countries_id']){
							echo("<option value=\"" . $R_Country['countries_id'] . "\" selected>" . $R_Country['countries_name'] . "</option>\n");
						}elseif(!$R_Address['entry_country_id'] && $R_Country['countries_id'] == 223){
							echo("<option value=\"" . $R_Country['countries_id'] . "\" selected>" . $R_Country['countries_name'] . "</option>\n");
						}else{
							echo("<option value=\"" . $R_Country['countries_id'] . "\">" . $R_Country['countries_name'] . "</option>\n");
						}
					 }
					 
					?>
					</select>
				</div>
		  </div>
		  

<?php
}
?>
		  <div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
			  <?php if ($invalid_address) { ?>
			  <a href="#" class="btn btn-success btn-sm" disabled="disabled" role="button" onclick="this.blur();"><?php echo SUBMIT_CHANGES; ?></a>
				  <!--<a class="button-disabled" title="<?php //echo SUBMIT_BUTTON_TITLE; ?>" href="#" onclick="this.blur();"><span><?php //echo SUBMIT_CHANGES; ?></span></a>-->
			   <?php } else { ?>
				  <a href="#" class="btn btn-success btn-sm" role="button" onclick="this.blur(); document.EditAddress.submit();"><?php echo SUBMIT_CHANGES; ?></a>
				  <!--<a class="button" title="<?php //echo SUBMIT_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.EditAddress.submit();"><span><?php //echo SUBMIT_CHANGES; ?></span></a>-->
			   <?php } ?>
			  <a href="index.php" class="btn btn-default btn-sm" role="button"><?php echo HOME; ?></a>
			  <!--<a class="button" title="<?php// echo BACK_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); window.history.go(-1);"><span><?php //echo BACK; ?></span></a>-->
			  </div>
		  </div>
 
		</div> <!-- end of panel body-->
	</div> <!-- end of panel -->
  </form>



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