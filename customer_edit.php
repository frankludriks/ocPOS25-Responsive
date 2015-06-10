<?php
// customer_edit.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}


$dob_month = intval(substr($_POST['dob_month'],0,2));
$dob_day = intval(substr($_POST['dob_day'],0,2));
$dob_year = intval(substr($_POST['dob_year'],0,4));

if ( ($dob_year < 1800) || ($dob_year > 2100) ) {
	$dob_year = 1000; // mark invalid years as year 1000 for valid checkdate() processing
}

if (checkdate($dob_month, $dob_day, $dob_year) == false) {  // clear date if invalid inputs
	$dob_month = '';
	$dob_day = '';
	$dob_year = '';
}

if (isset($_GET['CustomerID'])) $customer_id = sanitize($_GET['CustomerID']);

if (isset($_GET['submit']) && ($_GET['submit'] == 1) && (isset($_POST['fname']) && (isset($_POST['lname']))) ) {

    foreach ($_POST as $k=>$v) {
        $_POST[$k] = sanitize($v);
    }

    //PY: Determine if email address is already in use:
    if ($_POST['email'] <> '') {
        $email_exists_sql = "SELECT 1 as found FROM " . CUSTOMERS . " WHERE customers_id <> " . $customer_id . " AND customers_email_address='" . $_POST['email'] . "'";
        $Q_email_exists = mysql_query($email_exists_sql) or die ("SQL Error: seek email address failed. SQL=$email_exists_sql");
        $R_email = mysql_fetch_assoc($Q_email_exists);
    }

    if ($R_email['found']=='1') {

        // update all fields except email address
        $sql="UPDATE " . CUSTOMERS . " SET
            customers_telephone = '" . $_POST['phone'] . "',
            customers_firstname = '" . $_POST['fname'] . "',
            customers_lastname = '" . $_POST['lname'] . "',
            customers_dob = '" . $dob_year . "-" . $dob_month . "-" . $dob_day . " 00:00:00" . "',
            WHERE customers_id = '" . $customer_id . "'";

        $email_warning=true;

    } else {
   // update all fields

        mysql_query("update " . CUSTOMERS . " SET
            customers_telephone = '" . $_POST['phone'] . "',
            customers_email_address = '" . $_POST['email'] . "',
            customers_firstname = '" . $_POST['fname'] . "',
            customers_lastname = '" . $_POST['lname'] . "',
            customers_dob = '" . $dob_year . "-" . $dob_month . "-" . $dob_day . " 00:00:00" . "'
            WHERE customers_id = '" . $customer_id . "'");

        mysql_query("UPDATE " . CUSTOMERS_INFO . " SET
            customers_info_date_account_last_modified = now()
            WHERE customers_info_id = '" . $customer_id . "'");
    }

    $addrbook_id_query = mysql_query("SELECT customers_default_address_id from " . CUSTOMERS . " WHERE customers_id = '" . $customer_id . "'");
    $addrbook_id = mysql_fetch_array($addrbook_id_query);

    $updated = 1;
}


$Q_Customer = mysql_query("SELECT * FROM " . CUSTOMERS . " WHERE customers_id = '" . $customer_id . "' LIMIT 1");
if(mysql_num_rows($Q_Customer)) {
	$R_Customer = mysql_fetch_assoc($Q_Customer);
} else {
	header("Location: index.php");
}

if ($updated == 1 && !$email_warning) {
    header("Location: customer.php?CustomerID=$customer_id");
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<?php if ($updated == 1) { ?>
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

 <form name="EditCustomer" action="customer_edit.php?CustomerID=<?php echo $customer_id; ?>&submit=1" method="post">

 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <tr>
 <td width="100%" class="tdBlue" colspan="2" align="center">
  <b><?php echo(CUSTOMER_INFORMATION . ' ' . $R_Customer['customers_firstname'] . ' ' . $R_Customer['customers_lastname']); ?></b>
 </td>
 </tr>

 <?php

if ( ($R_Customer['day'] < 1) && ($R_Customer['day'] < 1) ) {
	$dob = '';
} else {
	//$dob_month = $R_Address['month'];
	//$dob_day = $R_Address['day'];
	//$dob_year = $R_Address['year'];
	if ($R_Customer['year'] == 1000) {
		$R_Customer['year'] = '';
	}
	$dob = $R_Customer['month'] . ' ' . $R_Customer['day'] . ' ' . $R_Customer['year'];
}
?>

 <tr>
 <td width="20%" class="tdBlue"><b><?php echo FULL_NAME; ?></b></td>
 <td width="80%">
        <input type="text" size="20" maxlength="40" name="fname" value="<?php echo($R_Customer['customers_firstname']); ?>">
        <input type="text" size="20" maxlength="40" name="lname" value="<?php echo($R_Customer['customers_lastname']); ?>">
 </td>
 </tr>

 <tr>
     <td width="20%" class="tdBlue"><b><?php echo PHONE; ?></b></td>
     <td width="80%">
        <input type="text" size="20" maxlength="40" name="phone" value="<?php echo($R_Customer['customers_telephone']); ?>"></td>
 </tr>

 <tr>
     <td width="20%" class="tdBlue"><b><?php echo EMAIL; ?></b></td>
     <td width="80%"><input type="text" size="20" maxlength="40" name="email" value="<?php echo($R_Customer['customers_email_address']); ?>"></a>
     <?php
    if ($email_warning) {
        echo EMAIL_IN_USE;
    }
    ?>
     </td>
 </tr>

 <tr>
 <td width="20%" class="tdBlue"><b><?php echo BIRTHDAY; ?></b></td>
 <td width="26%">
        <input type="text" size="2" maxlength="2" name="dob_month" value="<?php echo($R_Customer['month']); ?>"> /
        <input type="text" size="2" maxlength="2" name="dob_day" value="<?php echo($R_Customer['day']); ?>"> /
        <input type="text" size="4" maxlength="4" name="dob_year" value="<?php echo($R_Customer['year']); ?>">
	&nbsp;<?php echo MMDDYYYY; ?>
 </td>
 </tr>

 <tr height="35px">
 <td width="100%" class="tdBlue" colspan="2" align="center">
  <?php if ($invalid_address) { ?>
      <a class="button-disabled" title="<?php echo SUBMIT_BUTTON_TITLE; ?>" href="#" onclick="this.blur();"><span><?php echo SUBMIT_CHANGES; ?></span></a>
   <?php } else { ?>
      <a class="button" title="<?php echo SUBMIT_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.EditCustomer.submit();"><span><?php echo SUBMIT_CHANGES; ?></span></a>
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
