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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<?php if ($updated == 1 && !$email_warning) { ?>
		<meta http-equiv="refresh" content="1;url=customer.php?CustomerID=<?php echo ($_GET['CustomerID']); ?>">
		<?php } ?>

       <title><?php echo($POSName); ?></title>
       <link rel="Stylesheet" href="css/style.css">
       <script language="JavaScript" src="javascript.js" type="text/javascript"></script>
</head>
<body>
<?php include("includes/header.php"); ?>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">

 <form name="EditAddress" action="customer_edit_address.php?CustomerID=<?php echo $customer_id; ?>&AddressID=<?php echo $address_id; ?>&submit=1" method="post">

 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <tr>
     <td width="100%" class="tdBlue" colspan="2" align="center">
      <b><?php echo(CUSTOMER_INFORMATION . ' ' . $R_Customer['customers_firstname'] . ' ' . $R_Customer['customers_lastname']); ?></b>
     </td>
 </tr>

<?php
if (mysql_affected_rows()==0) {
   $invalid_address = 1;
   echo("<tr><td colspan=\"2\"><font color=\"red\"><br><b><center>" . NO_VALID_ADDRESS_RETURNED . "</center></font><br></td></tr>");
}

while($R_Address = mysql_fetch_assoc($Q_Address)) {

?>
 <tr>
     <td width="20%" class="tdBlue"><b><?php echo COMPANY; ?></b></td>
     <td width="80%"><input type="text" size="20" maxlength="40" name="company" value="<?php echo($R_Address['entry_company']); ?>"></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo FULL_NAME; ?></b></td>
     <td>
        <input type="text" size="20" maxlength="40" name="fname" value="<?php echo($R_Address['entry_firstname']); ?>">
        <input type="text" size="20" maxlength="40" name="lname" value="<?php echo($R_Address['entry_lastname']); ?>">
     </td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo ADDRESS; ?></b></td>
     <td><input type="text" size="20" maxlength="40" name="street" value="<?php echo($R_Address['entry_street_address']); ?>"></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo SUBURB; ?></b></td>
     <td><input type="text" size="20" maxlength="40" name="suburb" value="<?php echo($R_Address['entry_suburb']); ?>"></td>
 </tr>
 <tr>
    <td class="tdBlue"><b><?php echo CITY; ?></b></td>
    <td ><input type="text" size="20" maxlength="40" name="city" value="<?php echo($R_Address['entry_city']); ?>"></td>
 </tr>

 <tr>
     <td class="tdBlue"><b><?php echo STATE; ?></b></td>
     <td>
     <select name="state">
     <?php
     $default_zone_query = mysql_query("select configuration_value as zone_id from " . CONFIGURATION . " where configuration_key = 'STORE_ZONE'");
     $default_zone = mysql_fetch_array($default_zone_query);


     $Q_Zone = mysql_query("SELECT * FROM " . ZONES . " ORDER BY zone_name");
     while($R_Zone = mysql_fetch_assoc($Q_Zone)){
        if($R_Address['entry_zone_id'] == $R_Zone['zone_id']){
            echo("<option value=\"" . $R_Zone['zone_id'] . "\" selected>" . $R_Zone['zone_name'] . "</option>\n");
        }elseif(!$R_Address['entry_zone_id'] && $R_Zone['zone_id'] == $default_zone['zone_id']){ // Massachusets
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
     <td><input type="text" size="20" maxlength="40" name="zip" value="<?php echo($R_Address['entry_postcode']); ?>"></td>
 </tr>

 <tr>
     <td class="tdBlue"><b><?php echo COUNTRY; ?></b></td>
     <td>
     <select name="country">
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
     </td>
 </tr>


<?php
}
?>
 <tr height="35px">
     <td width="100%" class="tdBlue" colspan="2" align="center">
      <?php if ($invalid_address) { ?>
          <a class="button-disabled" title="<?php echo SUBMIT_BUTTON_TITLE; ?>" href="#" onclick="this.blur();"><span><?php echo SUBMIT_CHANGES; ?></span></a>
       <?php } else { ?>
          <a class="button" title="<?php echo SUBMIT_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.EditAddress.submit();"><span><?php echo SUBMIT_CHANGES; ?></span></a>
       <?php } ?>
      <a class="button" title="<?php echo BACK_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); window.history.go(-1);"><span><?php echo BACK; ?></span></a>
     </td>
 </tr>
 </table>
  </form>
  </td>
 </tr>
</table>


<?php include("includes/footer.php"); ?>
</body>
</html>
