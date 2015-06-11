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

<table class="table">
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
 <table  class="table">
 <tr>
 <td width="100%" colspan="4" align="center">
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
		<a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur();window.location.href='action.php?Action=NewOrder&CustomerID=<?php echo($R_Customer['customers_id']); ?>'"><?php echo ASSIGN_TO_NEW_ORDER; ?></a>

		<?php } else { // otherwise, button says to assign to existing order
	?> 
		<a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur();window.location.href='action.php?Action=AssignCustomer&CustomerID=<?php echo($R_Customer['customers_id']); ?>'"><?php echo ASSIGN_TO_ORDER; ?></a>
		
<?php } ?>

 </td>
 </tr>
<?php } ?>
 </table>
<?php
}else{
?>
 <table class="table">
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
