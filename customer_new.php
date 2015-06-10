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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title><?php echo($POSName) . ': ' . TITLE; ?></title>
       <link rel="Stylesheet" href="css/style.css">
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

<?php include("includes/header.php"); ?>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <form name="NewCustomer" method="post">
 <input type="hidden" name="posted" value="true">
 <tr>
 <td width="100%" class="tdBlue" colspan="2" align="center">
  <b><?php echo CREATE_NEW_CUST; ?></b>
 </td>
 </tr>
 <?php if($_POST['posted']){ ?>
 <tr>
 <td width="100%" class="tdBlue" colspan="2">
  <?php
  TestFormInput($_POST['posted'],$ReqVars);
  if($_POST['customers_password']!=$_POST['customers_password_2']){
  		echo('<li>' .  PASSWORD_NO_MATCH . '</li>');
  }
  if ($R_email['found'] == '1') {
        echo '<li>' . EMAIL_IN_USE .'</li>';
  }
  ?>
 </td>
 </tr>
<?php } ?>
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo FIRST_NAME; ?></b></td>
 <td width="80%"><input type="text" name="customers_firstname" size="40" value="<?php echo($_POST['customers_firstname']); ?>"> *</td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo LAST_NAME; ?></b></td>
     <td><input type="text" name="customers_lastname" size="40" value="<?php echo($_POST['customers_lastname']); ?>"> *</td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo EMAIL; ?></b></td>
     <td><input type="text" name="customers_email_address" size="40" value="<?php echo($_POST['customers_email_address']); ?>"></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo PHONE; ?></b></td>
     <td><input type="text" name="customers_telephone" size="40" value="<?php echo($_POST['customers_telephone']); ?>"></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo FAX; ?></b></td>
     <td><input type="text" name="customers_fax" size="40" value="<?php echo($_POST['customers_fax']); ?>"></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo PASSWORD; ?></b></td>
     <td><input type="password" name="customers_password" size="40" value="<?php echo($_POST['customers_password']); ?>"></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo PASSWORD_AGAIN; ?></b></td>
     <td><input type="password" name="customers_password_2" size="40" value="<?php echo($_POST['customers_password_2']); ?>"></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo NEWSLETTER; ?></b></td>
     <td><input type="checkbox" name="customers_newsletter" value="1"<?php if($_POST['customers_newsletter']){ echo(" checked"); } ?>><?php echo NEWSLETTER_SUBSCRIBE; ?></td>
 </tr>
 
 
 <?php /* primary address section */ ?>
 <tr>
 <td width="100%" class="tdBlue" colspan="2" align="center">
  <b><?php echo PRIMARY_ADDRESS; ?></b>
 </td>
 </tr>
 
 <tr>
     <td class="tdBlue"><b><?php echo COMPANY; ?></b></td>
     <td><input type="text" name="entry_company" size="40" value="<?php echo($_POST['entry_company']); ?>"></td>
 </tr>
 
 <tr>
     <td class="tdBlue"><b><?php echo STREET_ADDRESS; ?></b></td>
     <td><input type="text" name="entry_street_address" size="40" value="<?php echo($_POST['entry_street_address']); ?>"></td>
 </tr>
 
 <tr>
     <td class="tdBlue"><b><?php echo SUBURB; ?></b></td>
     <td><input type="text" name="entry_suburb" size="40" maxlength="32" value="<?php echo($_POST['entry_suburb']); ?>"></td>
 </tr>
 
 <tr>
     <td class="tdBlue"><b><?php echo CITY; ?></b></td>
     <td><input type="text" name="entry_city" size="40" value="<?php echo($_POST['entry_city']); ?>"></td>
 </tr>
 
 <tr>
     <td class="tdBlue"><b><?php echo STATE; ?></b></td>
     <td>
 <select name="entry_zone_id">
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
 </td>
 </tr>
 
 <tr>
 <td class="tdBlue"><b><?php echo POST_CODE; ?></b></td>
 <td><input type="text" name="entry_postcode" size="40" value="<?php echo($_POST['entry_postcode']); ?>"></td>
 </tr>
 
 <tr>
 <td class="tdBlue"><b><?php echo COUNTRY; ?></b></td>
 <td>
 <select name="entry_country_id">
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
 </td>
 </tr>
<?php /* end primary address section */ ?>

 <?php /* secondary address section */ ?>
 <tr>
 <td width="100%" class="tdBlue" colspan="2" align="center">
  <b><?php echo SECONDARY_ADDRESS; ?></b>
  <input type="checkbox" name="use_secondary_address" value="1"<?php if($_POST['use_secondary_address']){ echo(" checked"); } ?>></td>
 </tr>
 
 <tr>
     <td class="tdBlue"><b><?php echo COMPANY; ?></b></td>
     <td><input type="text" name="secondary_entry_company" size="40" value="<?php echo($_POST['secondary_entry_company']); ?>"></td>
 </tr>
 
 <tr>
     <td class="tdBlue"><b><?php echo FIRST_NAME; ?></b></td>
    <td><input type="text" name="secondary_customers_firstname" size="40" value="<?php echo($_POST['secondary_customers_firstname']); ?>"> *</td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo LAST_NAME; ?></b></td>
     <td><input type="text" name="secondary_customers_lastname" size="40" value="<?php echo($_POST['secondary_customers_lastname']); ?>"> *</td>
 </tr>
 
 <tr>
     <td class="tdBlue"><b><?php echo STREET_ADDRESS; ?></b></td>
     <td><input type="text" name="secondary_entry_street_address" size="40" value="<?php echo($_POST['secondary_entry_street_address']); ?>"></td>
 </tr>
 
 <tr>
     <td class="tdBlue"><b><?php echo SUBURB; ?></b></td>
     <td><input type="text" name="secondary_entry_suburb" size="40" maxlength="32" value="<?php echo($_POST['secondary_entry_suburb']); ?>"></td>
 </tr>
 
 <tr>
     <td class="tdBlue"><b><?php echo CITY; ?></b></td>
     <td><input type="text" name="secondary_entry_city" size="40" value="<?php echo($_POST['secondary_entry_city']); ?>"></td>
 </tr>
 
 <tr>
 <td class="tdBlue"><b><?php echo STATE; ?></b></td>
 <td>
 <select name="secondary_entry_zone_id">
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
 </td>
 </tr>
 
 <tr>
     <td class="tdBlue"><b><?php echo POST_CODE; ?></b></td>
     <td><input type="text" name="secondary_entry_postcode" size="40" value="<?php echo($_POST['secondary_entry_postcode']); ?>"></td>
 </tr>
 
 <tr>
     <td class="tdBlue"><b><?php echo COUNTRY; ?></b></td>
     <td>
 <select name="secondary_entry_country_id">
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
 </td>
 </tr>
<?php /* end secondary address section */ ?>
 
 
 <tr height="75px">
 <td width="100%" class="tdBlue" colspan="2" align="center"><br>
   <input type="hidden" name="Create" value="tempvalue">
   <?php if($_SESSION['CurrentOrderIndex'] == -1) { ?>
      <a class="button-disabled" title="<?php echo CREATE_ASSIGN_CURRENT_BUTTON_TITLE; ?>" href="#" onclick="this.blur();"><span><?php echo CREATE_AND_ASSIGN_CURRENT; ?></span></a> 
   <?php } else { ?> 
      <a class="button" title="<?php echo CREATE_ASSIGN_CURRENT_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.NewCustomer.Create.value='<?php echo CREATE_AND_ASSIGN_CURRENT; ?>';  document.NewCustomer.submit();"><span><?php echo CREATE_AND_ASSIGN_CURRENT; ?></span></a>
   <?php } ?>
   
   <a class="button" title="<?php echo CREATE_ASSIGN_NEW_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); document.NewCustomer.Create.value='<?php echo CREATE_AND_ASSIGN_NEW; ?>';  document.NewCustomer.submit();"><span><?php echo CREATE_AND_ASSIGN_NEW; ?></span></a>
   
   <a class="button" title="<?php echo CREATE_NO_ASSIGN_BUTTON_TITLE; ?>" href="#"  onclick="if (add_address_confirm()) {this.blur(); document.NewCustomer.Create.value='<?php echo CREATE_NO_ASSIGN; ?>';  document.NewCustomer.submit();}"><span><?php echo CREATE_NO_ASSIGN; ?></span></a>
   <br><br>
   <a class="button" title="<?php echo BACK_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); window.history.go(-1);"><span><?php echo BACK; ?></span></a>
    
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
