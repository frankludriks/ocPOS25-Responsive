<?php
// customer_search.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
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

<?php
$CUSTOMER_SEARCH = $_REQUEST['Query'];
$CUSTOMER_TYPE = $_REQUEST['Type'];

$Query = $_REQUEST['Query'];

// sanitize search term(s)
$Query = sanitize($Query); 

// remove asterisks in search string - taken care of by the LIKE statement in the query
$Query = str_replace("*", "", $Query); 

// remove leading or trailing spaces from search string (sometimes entered accidentally by operator)
$Query = trim($Query);

include("includes/header.php");
?>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
<?php
if($Query){
	if($_REQUEST['Type']=="LastName"){
		$Q_Customer = mysql_query("SELECT * FROM " . CUSTOMERS . " WHERE customers_lastname LIKE '%$Query%' ORDER BY customers_lastname,customers_firstname LIMIT $maximum_customer_search_results");
	}elseif($_REQUEST['Type']=="FirstName"){
		$Q_Customer = mysql_query("SELECT * FROM " . CUSTOMERS . " WHERE customers_firstname LIKE '%$Query%' ORDER BY customers_lastname,customers_firstname LIMIT $maximum_customer_search_results");
	}elseif($_REQUEST['Type']=="Email"){
		$Q_Customer = mysql_query("SELECT * FROM " . CUSTOMERS . " WHERE customers_email_address LIKE '%$Query%' ORDER BY customers_lastname,customers_firstname LIMIT $maximum_customer_search_results");
	}else{
		$Q_Customer = mysql_query("SELECT * FROM " . CUSTOMERS . " WHERE customers_id = '$Query' ORDER BY customers_lastname,customers_firstname LIMIT $maximum_customer_search_results");
	}
?>
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <tr>
 <td width="100%" class="tdBlue" colspan="4" align="center">
  <b><?php $results_count = mysql_num_rows($Q_Customer);
           if ($results_count >= $maximum_customer_search_results) {
	           $results_string = MORE_THAN . $maximum_customer_search_results . RESULTS_FOUND;
	           $results_string .= DISPLAYING . $results_count . ' ';
	           if ($results_count != 1) { 
		           $results_string .= CUSTOMERS_PLURAL; 
	           } else {
		           $results_string .= CUSTOMER_SINGULAR;
	           }
           } else {
	           $results_string = FOUND . $results_count . ' ';
	           if ($results_count != 1) { 
		           $results_string .= CUSTOMERS_PLURAL; 
	           } else {
		           $results_string .= CUSTOMER_SINGULAR;
	           }
           }
           echo $results_string; 
     ?>
   </b>
 </td>
 </tr>
 <tr>
 <td width="300" class="tdBlue" align="center"><b><?php echo CUSTOMER_NAME; ?></b></td>
 <td width="100" class="tdBlue" align="center"><b><?php echo PHONE; ?></b></td>
 <td width="250" class="tdBlue" align="center"><b><?php echo EMAIL; ?></b></td>
 <td width="110" class="tdBlue" align="center"><b><?php echo ASSIGN_TO_ORDER; ?></b></td>
 </tr>
<?php while($R_Customer = mysql_fetch_assoc($Q_Customer)){ ?>
 <tr height="45px">
 <td width="200" class="tdBlue"><br>
  <a href="customer.php?CustomerID=<?php echo($R_Customer['customers_id']); ?>"><?php echo($R_Customer['customers_lastname'] . ', ' .  $R_Customer['customers_firstname']); ?></a>
 </td>
 <td width="100" align="center"><br>
  <?php echo($R_Customer['customers_telephone']); ?>
 </td>
 <td width="250" align="center"><br>
  <a href="mailto:<?php echo($R_Customer['customers_email_address']); ?>"><?php echo($R_Customer['customers_email_address']); ?></a>
 </td>
 <td width="210" align="center"><br>
 
 <?php 
	 if($_SESSION['CurrentOrderIndex'] == -1) { // if no order button says assign to new order
 ?>	
		<a class="button" title="<?php echo ASSIGN_TO_NEW_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur();window.location.href='action.php?Action=NewOrder&CustomerID=<?php echo($R_Customer['customers_id']); ?>'"><span><?php echo ASSIGN_TO_NEW_ORDER; ?></span></a>
<?php } else { // otherwise, button says to assign to existing order
	?> 
		<a class="button" title="<?php echo ASSIGN_TO_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur();window.location.href='action.php?Action=AssignCustomer&CustomerID=<?php echo($R_Customer['customers_id']); ?>'"><span><?php echo ASSIGN_TO_ORDER; ?></span></a>
		
<?php } ?>

 </td>
 </tr>
<?php } ?>
 </table>
<?php
}else{
?>
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <tr>
 <td width="100%" class="tdBlue" align="center">
  <b><?php echo NO_SEARCH_TERMS; ?></b>
 </td>
 </tr>
 </table>
<?php } ?>
 
 
  </td>
 </tr>
</table>


<?php include("includes/footer.php"); ?>
</body>
</html>
