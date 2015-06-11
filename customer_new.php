<?php
// customer_new.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}

function get_rand($min = null, $max = null) {
	static $seeded;
	if (!isset($seeded)) {
		mt_srand((double)microtime()*1000000);
		$seeded = true;
	}
	if (isset($min) && isset($max)) {
		if ($min >= $max) {
			return $min;
		} else {
			return mt_rand($min, $max);
		}
	} else {
		return mt_rand();
	}
}

function encrypt_password($plaintext) {
	$password = '';
    for ($i=0; $i<10; $i++) {
      $password .= get_rand();
    }
    $seed = substr(md5($password), 0, 4);
    $password = md5($seed . $plaintext) . ':' . $seed;
    return $password;
}

$ReqVars = Array (
'First Name' => $_POST['customers_firstname'],
'Last Name' => $_POST['customers_lastname']
);

//PY: Determine if email address is already in use:

if ($_POST['customers_email_address'] <> '') {
    $email_exists_sql = "SELECT 1 as found FROM " . CUSTOMERS  . " WHERE customers_email_address='" . $_POST['customers_email_address'] . "'";
    $Q_email_exists = mysql_query($email_exists_sql) or die ("SQL Error: seek email address failed. SQL=$email_exists_sql");
    $R_email = mysql_fetch_assoc($Q_email_exists);
}

// if no existing customer already has this email address, proceed
if(TestFormInput(false,$ReqVars) && $_POST['customers_password'] == $_POST['customers_password_2'] && $R_email['found'] <> '1') {
	
    if (AUTOCAP_NAMES) {
	$_POST['customers_firstname']=ucwords(strtolower($_POST['customers_firstname']));
	$_POST['customers_lastname']=ucwords(strtolower($_POST['customers_lastname']));
    }

	mysql_query("INSERT INTO " . CUSTOMERS . " SET
		customers_firstname='" . $_POST['customers_firstname'] . "',
		customers_lastname='" . $_POST['customers_lastname'] . "',
		customers_email_address='" . $_POST['customers_email_address'] . "',
		customers_telephone='" . $_POST['customers_telephone'] . "',
		customers_fax='" . $_POST['customers_fax'] . "',
		customers_password='" . encrypt_password($_POST['customers_password']) ."',
		customers_newsletter='" . $_POST['customers_newsletter'] . "'
	");

	$CustomerID = mysql_insert_id();

	mysql_query("INSERT INTO " . CUSTOMERS_INFO . " SET
		customers_info_id='$CustomerID',
		customers_info_number_of_logons='0',
		customers_info_date_account_created=now();
	");
	
	mysql_query("INSERT INTO " . ADDRESS_BOOK. " SET
		customers_id='$CustomerID',
		entry_firstname='" . $_POST['customers_firstname'] . "',
		entry_lastname='" . $_POST['customers_lastname'] . "',
        entry_company='" . $_POST['entry_company'] . "',
		entry_street_address='" . $_POST['entry_street_address'] . "',
		entry_suburb='" . $_POST['entry_suburb'] . "',
		entry_postcode='" . $_POST['entry_postcode'] . "',
		entry_city='" . $_POST['entry_city'] . "',
		entry_country_id='" . $_POST['entry_country_id'] . "',
		entry_zone_id='" . $_POST['entry_zone_id'] . "'
	");
	$AddressID = mysql_insert_id();
	
	mysql_query("UPDATE " . CUSTOMERS . " SET customers_default_address_id='$AddressID' WHERE customers_id='$CustomerID'");

    if (isset($_POST['use_secondary_address'])) {
        mysql_query("INSERT INTO " . ADDRESS_BOOK. " SET
            customers_id='$CustomerID',
            entry_firstname='" . $_POST['secondary_customers_firstname'] . "',
            entry_lastname='" . $_POST['secondary_customers_lastname'] . "',
            entry_company='" . $_POST['secondary_entry_company'] . "',
            entry_street_address='" . $_POST['secondary_entry_street_address'] . "',
            entry_suburb='" . $_POST['secondary_entry_suburb'] . "',
            entry_postcode='" . $_POST['secondary_entry_postcode'] . "',
            entry_city='" . $_POST['secondary_entry_city'] . "',
            entry_country_id='" . $_POST['secondary_entry_country_id'] . "',
            entry_zone_id='" . $_POST['secondary_entry_zone_id'] . "'
        ");
    }
	$plainpwd = $_POST['customers_password'];
	$pwd_to_store = $pwd;
	
	if ($_POST['customers_email_address'])  {		// if they have an email address
		if ($_POST['customers_password'] == '') {		// if they did not get a password set	
			if (AUTO_CREATE_PASSWORDS == '1') 	{			// if auto create password is enabled
					$plainpwd = "";
					for ($i=0; $i<10; $i++) {
					  $plainpwd .= rand(0,9);
					}
					$seed = substr(md5($plainpwd), 0, 4);
					$encrypted_pwd = md5($seed . $plainpwd) . ':' . $seed;
					
					mysql_query("UPDATE " . CUSTOMERS . " SET customers_password='$encrypted_pwd' 
						WHERE customers_id = '$CustomerID'");
				}
			}
		if (EMAIL_NEW_CUSTOMER == '1') {			// if set to notify new customers of their new accounts
			$message = WELCOME_MESSAGE1 . "\r\n\r\n" . $plainpwd . "\r\n" . WELCOME_MESSAGE2 . $StoreWebsite . '.';
			$headers  = "From: \" $StoreName \" <" . STORE_EMAIL . ">" . "\r\n";
			mail($_POST['customers_email_address'], WELCOME_SUBJECT, $message, $headers);
		}
	}
	
	if(($_POST['Create']) == CREATE_AND_ASSIGN_CURRENT) {
         header("Location: action.php?Action=AssignCustomer&CustomerID=$CustomerID");
   }
   if(($_POST['Create']) == CREATE_AND_ASSIGN_NEW) {
         header("Location: action.php?Action=NewOrder&CustomerID=$CustomerID");
   }
   if(($_POST['Create']) == CREATE_NO_ASSIGN) {
      header("Location: customer.php?CustomerID=$CustomerID");
	}
}


// clean slashes
if(is_array($_POST)){
	$_POST = StripArraySlashes($_POST);
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
   <script language="JavaScript" type="text/javascript">
    <!--
    // address form validation
    function add_address_confirm() {
        
        // if first name and last name are not filled out, alert.
        if ( document.NewCustomer.customers_firstname.value.length < 1
            || document.NewCustomer.customers_lastname.value.length < 1 )
        {
            alert("<?php echo FIRST_LAST_REQUIRED; ?>");
            return false;
        }
        
        // password confirmation check
        if(document.NewCustomer.customers_password.value != document.NewCustomer.customers_password_2.value) {
            alert("<?php echo PASSWORD_NO_MATCH; ?>");
            return false;
        }
        
        // check primary address fields
        var re = /^\s{1,}$/g; //match any white space including space, tab, form-feed, etc. 
        
        var primary_fields_used = 0;
        if (document.NewCustomer.entry_street_address.value.length > 0)
            primary_fields_used++;
        if (document.NewCustomer.entry_suburb.value.length > 0)
            primary_fields_used++;
        if (document.NewCustomer.entry_city.value.length > 0)
            primary_fields_used++;
        if (document.NewCustomer.entry_postcode.value.length > 0)
            primary_fields_used++;
            
        if (primary_fields_used > 0 && primary_fields_used < 4) {
            alert("<?php echo ADDRESS_FORM_INCOMPLETE; ?>");
            return false;
        }
        
        // check secondary address fields
        var secondary_fields_used = 0;
        if (document.NewCustomer.secondary_entry_street_address.value.length > 0)
            secondary_fields_used++;
        if (document.NewCustomer.secondary_entry_suburb.value.length > 0)
            secondary_fields_used++;
        if (document.NewCustomer.secondary_entry_city.value.length > 0)
            secondary_fields_used++;
        if (document.NewCustomer.secondary_entry_postcode.value.length > 0)
            secondary_fields_used++;
        if (secondary_fields_used > 0 && secondary_fields_used < 4) {
            alert("<?php echo SECONDARY_ADDRESS_FORM_INCOMPLETE; ?>");
            return false;
        }
        return true;
    }
    -->
    </script>
   
</head>
<body onload="document.NewCustomer.customers_firstname.focus();">
  <div class="container">
  
		<?php include("includes/header.php"); ?>
			<form class="form-horizontal" name="NewCustomer" method="post">
			 <input type="hidden" name="posted" value="true">
			 
			 <?php if($_POST['posted']){ ?>
					  <?php
					  TestFormInput($_POST['posted'],$ReqVars);
					  if($_POST['customers_password']!=$_POST['customers_password_2']){
							echo'<div class="alert alert-danger" role="alert">' . PASSWORD_NO_MATCH . '</div>';
					  }
					  if ($R_email['found'] == '1') {
							echo '<div class="alert alert-danger" role="alert">' . EMAIL_IN_USE . '</div>';
					  }
					  ?>
			<?php } ?>
			
			 <div class="panel panel-primary">
			   <div class="panel-heading">
			     <h3 class="panel-title text-center"><?php echo CREATE_NEW_CUST; ?></h3>
			   </div>
			    <div class="panel-body">
					<div class="form-group">
					  <label for="customers_firstname" class="col-sm-2 control-label"><?php echo FIRST_NAME; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="customers_firstname" value="<?php echo($_POST['customers_firstname']); ?>" class="form-control" id="customers_firstname" placeholder="<?php echo FIRST_NAME; ?>">
						  <span class="glyphicon glyphicon-asterisk form-control-feedback inputRequirement"></span>
						</div>
					</div>
					<div class="form-group">
					  <label for="customers_lastname" class="col-sm-2 control-label"><?php echo LAST_NAME; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="customers_lastname" value="<?php echo($_POST['customers_lastname']); ?>" class="form-control" id="customers_lastname" placeholder="<?php echo LAST_NAME; ?>">
						  <span class="glyphicon glyphicon-asterisk form-control-feedback inputRequirement"></span>
						</div>
					</div>
					<div class="form-group">
					  <label for="customers_email_address" class="col-sm-2 control-label"><?php echo EMAIL; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="customers_email_address" value="<?php echo($_POST['customers_email_address']); ?>" class="form-control" id="customers_email_address" placeholder="<?php echo EMAIL; ?>">
						</div>
					</div>
					<div class="form-group">
					  <label for="customers_telephone" class="col-sm-2 control-label"><?php echo PHONE; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="customers_telephone" value="<?php echo($_POST['customers_telephone']); ?>" class="form-control" id="customers_telephone" placeholder="<?php echo PHONE; ?>">
						</div>
					</div>
					<div class="form-group">
					  <label for="customers_fax" class="col-sm-2 control-label"><?php echo FAX; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="customers_fax" value="<?php echo($_POST['customers_fax']); ?>" class="form-control" id="customers_fax" placeholder="<?php echo FAX; ?>">
						</div>
					</div>
					<div class="form-group">
					  <label for="customers_password" class="col-sm-2 control-label"><?php echo PASSWORD; ?></label>
						<div class="col-sm-10">
						  <input type="password" name="customers_password" value="<?php echo($_POST['customers_password']); ?>" class="form-control" id="customers_password" placeholder="<?php echo PASSWORD; ?>">
						</div>
					</div>
					<div class="form-group">
					  <label for="customers_password_2" class="col-sm-2 control-label"><?php echo PASSWORD_AGAIN; ?></label>
						<div class="col-sm-10">
						  <input type="password" name="customers_password_2" value="<?php echo($_POST['customers_password_2']); ?>" class="form-control" id="customers_password_2" placeholder="<?php echo PASSWORD_AGAIN; ?>">
						</div>
					</div>
					<div class="checkbox">
					  <label>
						<input type="checkbox" name="customers_newsletter" value="1"<?php if($_POST['customers_newsletter']){ echo(" checked"); } ?>> <?php echo NEWSLETTER; ?>
					  </label>
					</div>
					<?php /* primary address section */ ?>
					<div><h3><?php echo PRIMARY_ADDRESS; ?></h3></div>
					<div class="form-group">
					  <label for="entry_company" class="col-sm-2 control-label"><?php echo COMPANY; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="entry_company" value="<?php echo($_POST['entry_company']); ?>" class="form-control" id="entry_company" placeholder="<?php echo COMPANY; ?>">
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
					  <label for="entry_zone_id" class="col-sm-2 control-label"><?php echo STATE; ?></label>
						<div class="col-sm-10">
						  <select class="form-control" name="entry_zone_id">
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
					<?php /* secondary address section */ ?>
					<div><h3><?php echo SECONDARY_ADDRESS; ?> <input type="checkbox" name="use_secondary_address" value="1"<?php if($_POST['use_secondary_address']){ echo(" checked"); } ?>></h3> </div>
					<div class="form-group">
					  <label for="secondary_entry_company" class="col-sm-2 control-label"><?php echo COMPANY; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="secondary_entry_company" value="<?php echo($_POST['secondary_entry_company']); ?>" class="form-control" id="secondary_entry_company" placeholder="<?php echo COMPANY; ?>">
						</div>
					</div>
					<div class="form-group">
					  <label for="secondary_customers_firstname" class="col-sm-2 control-label"><?php echo FIRST_NAME; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="secondary_customers_firstname" value="<?php echo($_POST['secondary_customers_firstname']); ?>" class="form-control" id="secondary_customers_firstname" placeholder="<?php echo FIRST_NAME; ?>">
						  <span class="glyphicon glyphicon-asterisk form-control-feedback inputRequirement"></span>
						</div>
					</div>
					<div class="form-group">
					  <label for="secondary_customers_lastname" class="col-sm-2 control-label"><?php echo LAST_NAME; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="secondary_customers_lastname" value="<?php echo($_POST['secondary_customers_lastname']); ?>" class="form-control" id="secondary_customers_lastname" placeholder="<?php echo LAST_NAME; ?>">
						  <span class="glyphicon glyphicon-asterisk form-control-feedback inputRequirement"></span>
						</div>
					</div>
					<div class="form-group">
					  <label for="secondary_entry_street_address" class="col-sm-2 control-label"><?php echo STREET_ADDRESS; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="secondary_entry_street_address" value="<?php echo($_POST['secondary_entry_street_address']); ?>" class="form-control" id="secondary_entry_street_address" placeholder="<?php echo STREET_ADDRESS; ?>">
						</div>
					</div>
					<div class="form-group">
					  <label for="secondary_entry_suburb" class="col-sm-2 control-label"><?php echo SUBURB; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="secondary_entry_suburb" value="<?php echo($_POST['secondary_entry_suburb']); ?>" class="form-control" id="secondary_entry_suburb" placeholder="<?php echo SUBURB; ?>">
						</div>
					</div>
					<div class="form-group">
					  <label for="secondary_entry_city" class="col-sm-2 control-label"><?php echo CITY; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="secondary_entry_city" value="<?php echo($_POST['secondary_entry_city']); ?>" class="form-control" id="secondary_entry_city" placeholder="<?php echo CITY; ?>">
						</div>
					</div>
					<div class="form-group">
					  <label for="secondary_entry_zone_id" class="col-sm-2 control-label"><?php echo STATE; ?></label>
						<div class="col-sm-10">
						  <select class="form-control" name="secondary_entry_zone_id">
						  <?php
							 $default_zone_query = mysql_query("select configuration_value as zone_id from " . CONFIGURATION . " where configuration_key = 'STORE_ZONE'");
							 $default_zone = mysql_fetch_array($default_zone_query);
							 
							 $Q_Zone = mysql_query("SELECT * FROM " . ZONES . " ORDER BY zone_name");
							 while($R_Zone = mysql_fetch_assoc($Q_Zone)){
								if($_POST['secondary_entry_zone_id'] == $R_Zone['zone_id']){
									echo("<option value=\"" . $R_Zone['zone_id'] . "\" selected>" . $R_Zone['zone_name'] . "</option>\n");
								}elseif(!$_POST['secondary_entry_zone_id'] && $R_Zone['zone_id'] == $default_zone['zone_id']){ 
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
					  <label for="secondary_entry_postcode" class="col-sm-2 control-label"><?php echo POST_CODE; ?></label>
						<div class="col-sm-10">
						  <input type="text" name="secondary_entry_postcode" value="<?php echo($_POST['secondary_entry_postcode']); ?>" class="form-control" id="secondary_entry_postcode" placeholder="<?php echo POST_CODE; ?>">
						</div>
					</div>
					<div class="form-group">
					  <label for="secondary_entry_country_id" class="col-sm-2 control-label"><?php echo COUNTRY; ?></label>
						<div class="col-sm-10">
						 <select class="form-control" name="secondary_entry_country_id">
						 <?php
						 $Q_Country = mysql_query("SELECT * FROM " . COUNTRIES . " ORDER BY countries_name");
						 while($R_Country = mysql_fetch_assoc($Q_Country)){
							if($_POST['secondary_entry_country_id'] == $R_Country['countries_id']){
								echo("<option value=\"" . $R_Country['countries_id'] . "\" selected>" . $R_Country['countries_name'] . "</option>\n");
							}elseif(!$_POST['secondary_entry_country_id'] && $R_Country['countries_id'] == 223){
								echo("<option value=\"" . $R_Country['countries_id'] . "\" selected>" . $R_Country['countries_name'] . "</option>\n");
							}else{
								echo("<option value=\"" . $R_Country['countries_id'] . "\">" . $R_Country['countries_name'] . "</option>\n");
							}
						 }
						 ?>
						 </select>
						</div>
					</div>

			<?php /* end secondary address section */ ?>
					<div>
					  <input type="hidden" name="Create" value="tempvalue">
					    <?php if($_SESSION['CurrentOrderIndex'] == -1) { ?>
						  <a href="#" class="btn btn-default btn-sm" disabled="disabled" role="button" onclick="this.blur();"><?php echo CREATE_AND_ASSIGN_CURRENT; ?></a>
						<?php } else { ?>
						  <a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur(); document.NewCustomer.Create.value='<?php echo CREATE_AND_ASSIGN_CURRENT; ?>';  document.NewCustomer.submit();"><?php echo CREATE_AND_ASSIGN_CURRENT; ?></a>
						<?php 
						  } 
						?>	
						  <a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur(); document.NewCustomer.Create.value='<?php echo CREATE_AND_ASSIGN_NEW; ?>';  document.NewCustomer.submit();"><?php echo CREATE_AND_ASSIGN_NEW; ?></a>
						  <a href="#" class="btn btn-default btn-sm" role="button" onclick="if (add_address_confirm()) {this.blur(); document.NewCustomer.Create.value='<?php echo CREATE_NO_ASSIGN; ?>';  document.NewCustomer.submit();}"><?php echo CREATE_NO_ASSIGN; ?></a>
						  <a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur(); window.history.back();"><?php echo BACK; ?></a>
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
