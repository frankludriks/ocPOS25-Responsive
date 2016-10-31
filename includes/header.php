<?php 

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
} else {
    $logged_in = 1;
}
// includes/header.php
// error level defined in include/functions.php

if (file_exists("includes/lang/$lang/includes/header.php")) {
	include("includes/lang/$lang/includes/header.php");
}

// Custom Header has the logo and the login links.
include 'functions_header.php';
include 'custom_header.php';


?>
<!-- header -->
<div class="header clearfix">
    <nav>
      <ul class="nav nav-pills pull-right">
        <li role="presentation"><a href="#" class="btn btn-success" role="button" onclick="this.blur(); window.location.href='action.php?Action=NewOrder'"><?php echo NEW_ORDER; ?></a></li>
        <li role="presentation"><a href="#" class="btn btn-default" role="button" onclick="this.blur(); window.location.href='action.php?Action=ReturnOrder'"><?php echo RETURN_EXCHANGE; ?><input type="hidden" value="<?php echo RETURN_EXCHANGE; ?>"></a></li>
        <!--<li role="presentation"><a href="#">Contact</a></li>-->
      </ul>
    </nav>
    <h3 class="text-muted"><?php echo($POSName); ?></h3>
</div>
	<?php if (isset($_GET['Error'])) {	
		echo '<div class="alert alert-danger" role="alert"><b>' . $_GET['Error'] . '</div>';
	}
	 ?>
	  
 <div>
  <form class="form-inline" name="CustomerSearch" action="customer_search.php" method="get">
      <label for="Query"><?php echo CUSTOMER_HEADER; ?></label>
        <input type="text" class="form-control" id="Query" value="<?php echo($CUSTOMER_SEARCH); ?>" placeholder="John Smith" name="Query">
          <select name="Type" class="form-control">
		    <?php 
			  $is_customer_search = strstr($_SERVER["PHP_SELF"], 'customer_search.php');
			  $is_product_search = strstr($_SERVER["PHP_SELF"], 'product_search.php');
			  if ($is_product_search != 'product_search.php') { // most pages do not define $CUSTOMER_TYPE variable.  defining it here to avoid PHP warnings.
				 $PRODUCT_SEARCH = '';
			  }
			  if ($is_customer_search != 'customer_search.php') { // most pages do not define $PRODUCT_SEARCH variable.  defining it here to avoid PHP warnings.
				 $CUSTOMER_TYPE = '';
				 $CUSTOMER_SEARCH = '';
			  }
			?>
		   <option value="LastName"<?php if($CUSTOMER_TYPE=="LastName"){echo(" selected");} ?>><?php echo LAST_NAME; ?></option>
		   <option value="FirstName"<?php if($CUSTOMER_TYPE=="FirstName"){echo(" selected");} ?>><?php echo FIRST_NAME; ?></option>
		   <option value="Email"<?php if($CUSTOMER_TYPE=="Email"){echo(" selected");} ?>><?php echo EMAIL; ?></option>
		   <option value="CustomerID"<?php if($CUSTOMER_TYPE=="CustomerID"){echo(" selected");} ?>><?php echo CUSTOMER_ID; ?></option>
		   </select>
	
	<button type="submit" class="btn btn-default" href="#" onclick="this.blur(); document.CustomerSearch.submit();"><?php echo SEARCH_BUTTON_TEXT; ?></button>
	<a href="#" class="btn btn-default" role="button" onclick="this.blur(); window.location.href='customer_new.php'"><?php echo NEW_CUSTOMER_BUTTON_TITLE; ?></a> 
  </form>
 </div>
  <hr>
 <div>
  <form class="form-inline" name="ProductHeaderSearch" action="product_search.php" method="get">
    <div class="form-group">
      <label for="Query"><?php echo PRODUCT; ?></label>
        <input type="text" class="form-control" id="Query" value="<?php echo($PRODUCT_SEARCH); ?>" placeholder="Product" name="Query">
    </div>
	<button type="submit" class="btn btn-default" href="#" onclick="this.blur(); document.ProductHeaderSearch.submit();"><?php echo SEARCH_BUTTON_TEXT; ?></button>
  </form>
 </div>
  <hr>
<form class="form-inline" name="Orders">
 <div>
  <div>
   <div class="form-inline">
    <div class="form-group">
      <label for="OrderIndex"><?php echo PENDING_ORDERS; ?></label>
   
	   <select class="form-control input-sm" name="OrderIndex" onchange="window.location.href='action.php?Action=SelectOrder&OrderIndex='+document.Orders.OrderIndex.value">
	   <option value="xxx"><?php echo IN_PROGRESS; ?></option>
	   <option value="xxx">-----------------------</option>
	   
	   <?php show_pending_orders_dropdown();   ?>
	   
	   <option value="xxx">-----------------------</option>
	   <option value="xxx"><?php echo ARCHIVED_ORDERS; ?></option>
	   <option value="xxx">-----------------------</option>
	   
	   <?php show_archived_orders_dropdown();    ?> 
	   
	   </select>

	</div>
   </div>
  </div>
   <hr>
  <div>
   <div class="form-inline">
	  <div class="form-group">
	   <label for="OrderIndex"><?php echo HISTORY; ?></label>
	   <select class="form-control input-sm" name="OrderID" onchange="window.location.href='order.php?OrderID='+document.Orders.OrderID.value">
	   
	   <option value=""><?php echo SELECT_ONE; ?></option> 
	   <option value=""></option> 
	   <?php show_recent_orders_dropdown(); ?>
	   <option value="xxx">-----------------------</option>   
		  
	   <option value="Orders"<?php if(substr_count($_SERVER['PHP_SELF'],"order_history.php") == 1){echo(" selected");} ?>><?php echo VIEW_MORE; ?></option>
	   <option value="Report"<?php if(substr_count($_SERVER['PHP_SELF'],"reporting.php") == 1){echo(" selected");} ?>><?php echo ORDER_REPORTING; ?></option>
	   </select>
	  </div>
   </div>
 </div>
 </div>
</form>
  <hr>
<!-- end header -->