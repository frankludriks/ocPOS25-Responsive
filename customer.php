<?php
// customer.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}

$Q_Customer = mysql_query("SELECT * FROM " . CUSTOMERS . " WHERE customers_id = '" . $_REQUEST['CustomerID'] . "' LIMIT 1");
if(mysql_num_rows($Q_Customer)){
	$R_Customer = mysql_fetch_assoc($Q_Customer);
}else{
	header("Location: index.php");
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
  
 <table class="table table-striped">
 <tr>
     <td width="100%" class="tdBlue" colspan="3" align="center">
  <b><?php echo CUSTOMER_INFORMATION; ?>"<?php echo($R_Customer['customers_firstname'] . ' ' . $R_Customer['customers_lastname']); ?>"</b>
 </td>
 </tr>
 <tr>
     <td width="20%"><b><?php echo FULL_NAME; ?></b></td>
     <td width="80%" colspan="2"><?php echo($R_Customer['customers_firstname'] . ' ' . $R_Customer['customers_lastname']); ?></td>
 </tr>
 <tr>
 <td width="20%"><b><?php echo PHONE; ?></b></td>
     <td width="80%" colspan="2"><?php echo($R_Customer['customers_telephone']); ?></td>
 </tr>
 <tr>
     <td><b><?php echo EMAIL; ?></b></td>
     <td width="40%"><a href="mailto:<?php echo($R_Customer['customers_email_address']); ?>"><?php echo($R_Customer['customers_email_address']); ?></a></td>
     <td width="40%"><a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur(); window.location.href='customer_edit.php?CustomerID=<?php echo($R_Customer['customers_id']); ?>'"><?php echo EDIT_CUSTOMER; ?></a></td>
 </tr>
 
<?php
$AddressCounter=1;
$Q_Address = mysql_query("SELECT ab.*, co.countries_name, z.zone_name FROM " . COUNTRIES . " co, " . ADDRESS_BOOK. " ab LEFT JOIN " . ZONES  . " z ON (ab.entry_zone_id = z.zone_id) WHERE
	ab.customers_id = '" . $_REQUEST['CustomerID'] . "' AND
	co.countries_id = ab.entry_country_id");
    
while($R_Address = mysql_fetch_assoc($Q_Address)){
?>
 <tr>
     <td width="100%" colspan="3" align="center">
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
 <tr height=30px">
     <td><b><?php echo COUNTRY; ?></b></td>
     <td><?php echo($R_Address['countries_name']); ?></td>
     <td><a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur(); window.location.href='customer_edit_address.php?CustomerID=<?php echo($R_Customer['customers_id']); ?>&AddressID=<?php echo $R_Address['address_book_id']; ?>'"><?php echo EDIT_CUSTOMER_ADDRESS; ?></a></td>
 </tr>
<?php
	$AddressCounter++;
}
?>
 <tr>
     <td width="100%" colspan="3" align="center"><br>
    <?php if($_SESSION['CurrentOrderIndex'] == -1) { ?>
		<a href="#" class="btn btn-default btn-sm" disabled="disabled" role="button" onclick="this.blur();"><?php echo ASSIGN_TO_CURRENT_ORDER; ?></a>
		
        
   <?php } else { ?>
		<a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur(); window.location.href='action.php?Action=AssignCustomer&CustomerID=<?php echo($R_Customer['customers_id']); ?>'"><?php echo ASSIGN_TO_CURRENT_ORDER; ?></a>
		
   <?php } ?>

        <a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur(); window.location.href='action.php?Action=NewOrder&CustomerID=<?php echo($R_Customer['customers_id']); ?>'"><?php echo ASSIGN_TO_NEW_ORDER; ?></a>
		
        <a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur(); window.location.href='customer_add_address.php?CustomerID=<?php echo($R_Customer['customers_id']); ?>'"><?php echo ADD_CUSTOMER_ADDRESS; ?></a>
		<a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur(); window.history.back();"><?php echo BACK; ?></a>
 </td>
 </tr>
 </table>
 
 
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
