<?php
// customer_add_address.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}


if (isset($_GET['CustomerID'])) $customer_id = sanitize($_GET['CustomerID']);

$updated = 0;

$ReqVars = Array (
'First Name' => $_POST['customers_firstname'],
'Last Name' => $_POST['customers_lastname']
);

if (isset($_POST['customers_firstname']) && (isset($_POST['customers_lastname'])) ) {

    foreach ($_POST as $k=>$v) {
        $_POST[$k] = sanitize($v);
    }

	mysql_query("INSERT INTO " . ADDRESS_BOOK. " SET
		customers_id='" . $customer_id . "',
        entry_company='',
		entry_firstname='" . $_POST['customers_firstname'] . "',
		entry_lastname='" . $_POST['customers_lastname'] . "',
		entry_street_address='" . $_POST['entry_street_address'] . "',
		entry_suburb='" . $_POST['entry_suburb'] . "',
        entry_zone_id = '" . $_POST['entry_state'] . "',
		entry_postcode='" . $_POST['entry_postcode'] . "',
		entry_city='" . $_POST['entry_city'] . "',
		entry_country_id='" . $_POST['entry_country_id'] . "'
	");

    $address_id = mysql_insert_id();

// verify correct zone name (state if you're in the US) before writing the state and entry_zone_id
    $zone_lookup_query = mysql_query("SELECT * FROM " . ZONES . "
        WHERE zone_name = '" . $_POST['entry_state'] . "'
        OR zone_code = '" . $_POST['entry_state'] . "'
        LIMIT 1");
    if(mysql_num_rows($zone_lookup_query)) {
        $zone_id = mysql_fetch_assoc($zone_lookup_query);
        mysql_query("UPDATE " . ADDRESS_BOOK . " SET
            entry_zone_id = '" . $zone_id['zone_id'] . "',
            entry_state = '" . $zone_id['zone_state'] . "'
            WHERE customers_id = '" . $customer_id . "'
            AND address_book_id = '" . $address_id . "'");

        mysql_query("UPDATE " . CUSTOMERS_INFO . " SET
            customers_info_date_account_last_modified = now()
            WHERE customers_info_id = '" . $customer_id . "'");
    }

    $updated = 1;
}

if($updated) {
  header("Location: customer.php?CustomerID=$customer_id");
}

$Q_Customer = mysql_query("SELECT * FROM " . CUSTOMERS . " WHERE customers_id = '" . $customer_id . "' LIMIT 1");
if(mysql_num_rows($Q_Customer)) {
	$R_Customer = mysql_fetch_assoc($Q_Customer);
} else {
	header("Location: index.php");
}

$firstname = (isset($_POST['customers_firstname'])) ? $_POST['customers_firstname'] : $R_Customer['customers_firstname'];
$lastname = (isset($_POST['customers_lastname'])) ? $_POST['customers_lastname'] : $R_Customer['customers_lastname'];


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
   <script language="JavaScript" type="text/javascript">
    <!--
    // address form validation
    function add_address_confirm() {

        // if first name and last name are not filled out, alert.
        if ( document.NewAddress.customers_firstname.value.length < 1
            || document.NewAddress.customers_lastname.value.length < 1 )
        {
            alert("<?php echo FIRST_LAST_REQUIRED; ?>");
            return false;
        }

        // check primary address fields
        var re = /^\s{1,}$/g; //match any white space including space, tab, form-feed, etc.

        var primary_fields_used = 0;
        if (document.NewAddress.entry_street_address.value.length > 0)
            primary_fields_used++;
        if (document.NewAddress.entry_suburb.value.length > 0)
            primary_fields_used++;
        if (document.NewAddress.entry_city.value.length > 0)
            primary_fields_used++;
        if (document.NewAddress.entry_postcode.value.length > 0)
            primary_fields_used++;

        if (primary_fields_used > 0 && primary_fields_used < 4) {
            alert("<?php echo ADDRESS_FORM_INCOMPLETE; ?>");
            return false;
        }

        return true;
    }
    -->
    </script>

</head>
<body onload="document.NewAddress.customers_firstname.focus();">
  <div class="container">
    <?php include("includes/header.php"); ?>
	  <?php if($_POST['posted']){ ?>
		  <?php
		  TestFormInput($_POST['posted'],$ReqVars);
		  if($_POST['customers_password']!=$_POST['customers_password_2']){
				echo('<div class="alert alert-danger" role="alert">' .  PASSWORD_NO_MATCH . '</div>');
		  }
		  if ($R_email['found'] == '1') {
				echo '<div class="alert alert-danger" role="alert">' . EMAIL_IN_USE .'</div>';
		  }
		  ?>
	  <?php 
	  } 
	  ?>
	  <div class="panel panel-primary">
			<div class="panel-heading">
			     <h3 class="panel-title text-center"><?php echo CREATE_NEW_CUST; ?></h3>
			   </div>
			   <form class="form-horizontal" name="NewAddress" method="post">
			   <input type="hidden" name="posted" value="true">
			    <div class="panel-body">
					<div class="form-group">
					 <label for="customers_firstname" class="col-sm-2 control-label"><?php echo FIRST_NAME; ?></label>
					  <div class="col-sm-10">
						 <input type="text" name="customers_firstname" value="<?php echo $firstname; ?>" class="form-control" id="customers_firstname" placeholder="<?php echo FIRST_NAME; ?>">
					  </div>
					</div>
					<div class="form-group">
					 <label for="customers_lastname" class="col-sm-2 control-label"><?php echo LAST_NAME; ?></label>
					  <div class="col-sm-10">
						 <input type="text" name="customers_lastname" value="<?php echo $lastname; ?>" class="form-control" id="customers_lastname" placeholder="<?php echo LAST_NAME; ?>">
					  </div>
					</div>
					<div class="form-group">
					 <label for="entry_street_address" class="col-sm-2 control-label"><?php echo STREET_ADDRESS; ?></label>
					  <div class="col-sm-10">
						 <input type="text" name="entry_street_address" value="<?php echo($_POST['entry_street_address']); ?>" class="form-control" id="entry_street_address" placeholder="<?php echo STREET_ADDRESS; ?>">
					  </div>
					</div>
					<div class="form-group">
					 <label for="entry_suburb" class="col-sm-2 control-label"><?php echo SUBURB; ?></label>
					  <div class="col-sm-10">
						 <input type="text" name="entry_suburb" value="<?php echo($_POST['entry_suburb']); ?>" class="form-control" id="entry_suburb" placeholder="<?php echo SUBURB; ?>">
					  </div>
					</div>
					<div class="form-group">
					 <label for="entry_city" class="col-sm-2 control-label"><?php echo CITY; ?></label>
					  <div class="col-sm-10">
						 <input type="text" name="entry_city" value="<?php echo($_POST['entry_city']); ?>" class="form-control" id="entry_city" placeholder="<?php echo CITY; ?>">
					  </div>
					</div>
					<div class="form-group">
					  <label for="entry_state" class="col-sm-2 control-label"><?php echo STATE; ?></label>
						<div class="col-sm-10">
						  <select class="form-control" name="entry_state">
						  <?php
							 $default_zone_query = mysql_query("select configuration_value as zone_id from " . CONFIGURATION . " where configuration_key = 'STORE_ZONE'");
							 $default_zone = mysql_fetch_array($default_zone_query);


							 $Q_Zone = mysql_query("SELECT * FROM " . ZONES . " ORDER BY zone_name");
							 while($R_Zone = mysql_fetch_assoc($Q_Zone)){
								if($_POST['entry_zone_id'] == $R_Zone['zone_id']){
									echo("<option value=\"" . $R_Zone['zone_id'] . "\" selected>" . $R_Zone['zone_name'] . "</option>\n");
								}elseif(!$_POST['entry_zone_id'] && $R_Zone['zone_id'] == $default_zone['zone_id']){ // Massachusets
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
					 <label for="entry_postcode" class="col-sm-2 control-label"><?php echo POST_CODE; ?></label>
					  <div class="col-sm-10">
						 <input type="text" name="entry_postcode" value="<?php echo($_POST['entry_postcode']); ?>" class="form-control" id="entry_postcode" placeholder="<?php echo POST_CODE; ?>">
					  </div>
					</div>
					<div class="form-group">
					  <label for="entry_country_id" class="col-sm-2 control-label"><?php echo COUNTRY; ?></label>
						<div class="col-sm-10">
						 <select class="form-control" name="entry_country_id">
						 <?php
						 $Q_Country = mysql_query("SELECT * FROM " . COUNTRIES . " ORDER BY countries_name");
						 while($R_Country = mysql_fetch_assoc($Q_Country)){
							if($_POST['entry_country_id'] == $R_Country['countries_id']){
								echo("<option value=\"" . $R_Country['countries_id'] . "\" selected>" . $R_Country['countries_name'] . "</option>\n");
							}elseif(!$_POST['entry_country_id'] && $R_Country['countries_id'] == 223){
								echo("<option value=\"" . $R_Country['countries_id'] . "\" selected>" . $R_Country['countries_name'] . "</option>\n");
							}else{
								echo("<option value=\"" . $R_Country['countries_id'] . "\">" . $R_Country['countries_name'] . "</option>\n");
							}
						 }
						 ?>
						 </select>
						</div>
					</div>
					<?php /* end primary address section */ ?>
					<div class="text-center">
					  <a href="#" class="btn btn-success" role="button" onclick="if (add_address_confirm()) {this.blur();  document.NewAddress.submit();}"><?php echo SUBMIT_CHANGES; ?></a>
					  <!--<a class="button" title="<?php //echo SUBMIT_BUTTON_TITLE; ?>" href="#"  onclick="if (add_address_confirm()) {this.blur();  document.NewAddress.submit();}"><span><?php //echo SUBMIT_CHANGES; ?></span></a>-->
					  <a href="#" class="btn btn-default" role="button" onclick="this.blur(); window.history.go(-1);"><?php echo BACK; ?></a>
					  <!--<a class="button" title="<?php //echo BACK_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); window.history.go(-1);"><span><?php //echo BACK; ?></span></a>-->
					</div>
				  </div>
				</form>
			  </div>
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
