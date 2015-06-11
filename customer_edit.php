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

<table class="table">
 <tr>
  <td width="100%">

 <form name="EditCustomer" action="customer_edit.php?CustomerID=<?php echo $customer_id; ?>&submit=1" method="post">

 <table class="table">
 <tr>
 <td width="100%" colspan="2" align="center">
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
 <td width="20%"><b><?php echo FULL_NAME; ?></b></td>
 <td width="80%">
        <input type="text" size="20" maxlength="40" name="fname" value="<?php echo($R_Customer['customers_firstname']); ?>">
        <input type="text" size="20" maxlength="40" name="lname" value="<?php echo($R_Customer['customers_lastname']); ?>">
 </td>
 </tr>

 <tr>
     <td width="20%"><b><?php echo PHONE; ?></b></td>
     <td width="80%">
        <input type="text" size="20" maxlength="40" name="phone" value="<?php echo($R_Customer['customers_telephone']); ?>"></td>
 </tr>

 <tr>
     <td width="20%"><b><?php echo EMAIL; ?></b></td>
     <td width="80%"><input type="text" size="20" maxlength="40" name="email" value="<?php echo($R_Customer['customers_email_address']); ?>"></a>
     <?php
    if ($email_warning) {
        echo '<p class="text-danger">' . EMAIL_IN_USE . '</p>';
    }
    ?>
     </td>
 </tr>

 <tr>
 <td width="20%"><b><?php echo BIRTHDAY; ?></b></td>
 <td width="26%">
        <input type="text" size="2" maxlength="2" name="dob_month" value="<?php echo($R_Customer['month']); ?>"> /
        <input type="text" size="2" maxlength="2" name="dob_day" value="<?php echo($R_Customer['day']); ?>"> /
        <input type="text" size="4" maxlength="4" name="dob_year" value="<?php echo($R_Customer['year']); ?>">
	&nbsp;<?php echo MMDDYYYY; ?>
 </td>
 </tr>

 <tr height="35px">
 <td width="100%" colspan="2" align="center">
  <?php if ($invalid_address) { ?>
		<a href="#" class="btn btn-default btn-sm" disabled="disabled" role="button" onclick="this.blur();"><?php echo SUBMIT_CHANGES; ?></a>
		
      <!--<a class="button-disabled" title="<?php //echo SUBMIT_BUTTON_TITLE; ?>" href="#" onclick="this.blur();"><span><?php //echo SUBMIT_CHANGES; ?></span></a>-->
   <?php } else { ?>
      <a href="#" class="btn btn-success btn-sm" role="button" onclick="this.blur(); document.EditCustomer.submit();"><?php echo SUBMIT_CHANGES; ?></a>
	  
      <!--<a class="button" title="<?php //echo SUBMIT_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.EditCustomer.submit();"><span><?php //echo SUBMIT_CHANGES; ?></span></a>-->
   <?php } ?>
   <a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur(); window.history.go(-1);"><?php echo BACK; ?></a>
  <!--<a class="button" title="<?php //echo BACK_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); window.history.go(-1);"><span><?php //echo BACK; ?></span></a>-->
 </td>
 </tr>
 </table>
  </form>
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
