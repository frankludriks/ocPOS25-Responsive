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

<?php include("includes/header.php"); ?>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">

 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <form name="NewAddress" method="post">
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

 <?php /* primary address section */ ?>
 <tr>
     <td width="20%" class="tdBlue"><b><?php echo FIRST_NAME; ?></b></td>
     <td width="80%"><input type="text" name="customers_firstname" size="40" value="<?php echo $firstname; ?>"></td>
 </tr>
 <tr>
     <td width="20%" class="tdBlue"><b><?php echo LAST_NAME; ?></b></td>
     <td width="80%"><input type="text" name="customers_lastname" size="40" value="<?php echo $lastname; ?>"></td>
 </tr>

 <tr>
     <td width="20%" class="tdBlue"><b><?php echo STREET_ADDRESS; ?></b></td>
     <td width="80%"><input type="text" name="entry_street_address" size="40" value="<?php echo($_POST['entry_street_address']); ?>"></td>
 </tr>

 <tr>
     <td width="20%" class="tdBlue"><b><?php echo SUBURB; ?></b></td>
     <td width="80%"><input type="text" name="entry_suburb" size="40" maxlength="32" value="<?php echo($_POST['entry_suburb']); ?>"></td>
 </tr>

 <tr>
     <td width="20%" class="tdBlue"><b><?php echo CITY; ?></b></td>
     <td width="80%"><input type="text" name="entry_city" size="40" value="<?php echo($_POST['entry_city']); ?>"></td>
 </tr>

 <tr>
     <td width="20%" class="tdBlue"><b><?php echo STATE; ?></b></td>
     <td width="80%">
     <select name="entry_state">
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
     <td width="20%" class="tdBlue"><b><?php echo POST_CODE; ?></b></td>
     <td width="80%"><input type="text" name="entry_postcode" size="40" value="<?php echo($_POST['entry_postcode']); ?>"></td>
 </tr>

 <tr>
     <td width="20%" class="tdBlue"><b><?php echo COUNTRY; ?></b></td>
     <td width="80%">
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

 <tr height="35px">
     <td width="100%" class="tdBlue" colspan="2" align="center">
        <a class="button" title="<?php echo SUBMIT_BUTTON_TITLE; ?>" href="#"  onclick="if (add_address_confirm()) {this.blur();  document.NewAddress.submit();}"><span><?php echo SUBMIT_CHANGES; ?></span></a>
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
