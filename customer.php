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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title><?php echo($POSName) . ': ' . TITLE; ?></title>
       <link rel="Stylesheet" href="css/style.css">
       <script language="JavaScript" src="javascript.js" type="text/javascript"></script>
</head>
<body>
<?php include("includes/header.php"); ?>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <tr>
     <td width="100%" class="tdBlue" colspan="3" align="center">
  <b><?php echo CUSTOMER_INFORMATION; ?>"<?php echo($R_Customer['customers_firstname'] . ' ' . $R_Customer['customers_lastname']); ?>"</b>
 </td>
 </tr>
 <tr>
     <td width="20%" class="tdBlue"><b><?php echo FULL_NAME; ?></b></td>
     <td width="80%" colspan="2"><?php echo($R_Customer['customers_firstname'] . ' ' . $R_Customer['customers_lastname']); ?></td>
 </tr>
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo PHONE; ?></b></td>
     <td width="80%" colspan="2"><?php echo($R_Customer['customers_telephone']); ?></td>
 </tr>
 <tr height="30px">
     <td class="tdBlue"><b><?php echo EMAIL; ?></b></td>
     <td width="40%"><a href="mailto:<?php echo($R_Customer['customers_email_address']); ?>"><?php echo($R_Customer['customers_email_address']); ?></a></td>
     <td width="40%"><a class="button" title="<?php echo EDIT_CUSTOMER_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); window.location.href='customer_edit.php?CustomerID=<?php echo($R_Customer['customers_id']); ?>'"><span><?php echo EDIT_CUSTOMER; ?></span></a></td>
 </tr>
 
<?php
$AddressCounter=1;
$Q_Address = mysql_query("SELECT ab.*, co.countries_name, z.zone_name FROM " . COUNTRIES . " co, " . ADDRESS_BOOK. " ab LEFT JOIN " . ZONES  . " z ON (ab.entry_zone_id = z.zone_id) WHERE
	ab.customers_id = '" . $_REQUEST['CustomerID'] . "' AND
	co.countries_id = ab.entry_country_id");
    
while($R_Address = mysql_fetch_assoc($Q_Address)){
?>
 <tr>
     <td width="100%" class="tdBlue" colspan="3" align="center">
  <b><?php echo ADDRESS_COUNT; ?><?php echo($AddressCounter); ?></b>
 </td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo COMPANY; ?></b></td>
     <td colspan="2"><?php echo($R_Address['entry_company']); ?></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo FULL_NAME; ?></b></td>
     <td><?php echo($R_Address['entry_firstname'] . ' ' . $R_Address['entry_lastname']); ?></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo ADDRESS; ?></b></td>
     <td colspan="2"><?php echo($R_Address['entry_street_address']); ?></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo SUBURB; ?></b></td>
     <td colspan="2"><?php echo($R_Address['entry_suburb']); ?></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo CITY; ?></b></td>
     <td colspan="2"><?php echo($R_Address['entry_city']); ?></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo STATE; ?></b></td>
     <td colspan="2"><?php echo($R_Address['zone_name']); ?></td>
 </tr>
 <tr>
     <td class="tdBlue"><b><?php echo POST_CODE; ?></b></td>
     <td colspan="2"><?php echo($R_Address['entry_postcode']); ?></td>
 </tr>
 <tr height=30px">
     <td class="tdBlue"><b><?php echo COUNTRY; ?></b></td>
     <td><?php echo($R_Address['countries_name']); ?></td>
     <td><a class="button" title="<?php echo EDIT_ADDRESS_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); window.location.href='customer_edit_address.php?CustomerID=<?php echo($R_Customer['customers_id']); ?>&AddressID=<?php echo $R_Address['address_book_id']; ?>'"><span><?php echo EDIT_CUSTOMER_ADDRESS; ?></span></a></td>
 </tr>
<?php
	$AddressCounter++;
}
?>
 <tr height="45px">
     <td width="100%" class="tdBlue" colspan="3" align="center"><br>
    <?php if($_SESSION['CurrentOrderIndex'] == -1) { ?>
        <a class="button-disabled" title="<?php echo ASSIGN_TO_CURRENT_BUTTON_TITLE; ?>" href="#" onclick="this.blur();"><span><?php echo ASSIGN_TO_CURRENT_ORDER; ?></span></a> 
   <?php } else { ?> 
        <a class="button" title="<?php echo ASSIGN_TO_CURRENT_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); window.location.href='action.php?Action=AssignCustomer&CustomerID=<?php echo($R_Customer['customers_id']); ?>'"><span><?php echo ASSIGN_TO_CURRENT_ORDER; ?></span></a> 
   <?php } ?>

        <a class="button" title="<?php echo ASSIGN_TO_NEW_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); window.location.href='action.php?Action=NewOrder&CustomerID=<?php echo($R_Customer['customers_id']); ?>'"><span><?php echo ASSIGN_TO_NEW_ORDER; ?></span></a>
        <a class="button" title="<?php echo ADD_ADDRESS_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); window.location.href='customer_add_address.php?CustomerID=<?php echo($R_Customer['customers_id']); ?>'"><span><?php echo ADD_CUSTOMER_ADDRESS; ?></span></a>
        <a class="button" title="<?php echo BACK_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); window.history.go(-1);"><span><?php echo BACK; ?></span></a>
 </td>
 </tr>
 </table>
 
 
  </td>
 </tr>
</table>


<?php include("includes/footer.php"); ?>
</body>
</html>
