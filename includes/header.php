<?php 
// includes/header.php
// error level defined in include/functions.php

if (file_exists("includes/lang/$lang/includes/header.php")) {
	include("includes/lang/$lang/includes/header.php");
}

// Custom Header has the logo and the login links.
include 'custom_header.php';
include 'functions_header.php';

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
} else {
    $logged_in = 1;
}

if (isset($_GET['Error'])) {
	echo '<table width="100%" bgcolor="red"><tr><td align="middle" width="100%"><font color="white" size="3"><b>' . $_GET['Error'] . '</b></font></td></tr></table>';
}

?>
<!-- header -->
<table width="100%" border="0" cellpadding="2" cellspacing="0">
 <tr height="35px">
 <form name="CustomerSearch" action="customer_search.php" method="get">
  <td class="tdHeader" align="center">
   <b><?php echo CUSTOMER_HEADER; ?></b>
   <input type="text" name="Query" size="10" value="<?php echo($CUSTOMER_SEARCH); ?>">
   <select name="Type">
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
   <a class="headerbutton" title="<?php echo SEARCH_CUSTOMER_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); document.CustomerSearch.submit();"><span><?php echo SEARCH_BUTTON_TEXT; ?></span></a>
   <a class="headerbutton" title="<?php echo NEW_CUSTOMER_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); window.location.href='customer_new.php'"><span><?php echo NEW_BUTTON_TEXT; ?></span></a>
   &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
  </td>
  </form>
  

        <form name="OrderHeaderSearch" action="order.php" method="get">
        <td class="tdHeader" align="center">
            <b><?php echo ORDER; ?></b>
            <input type="text" name="OrderID" size="5" value="<?php echo($ORDER_SEARCH); ?>">
            <a class="headerbutton" title="<?php echo SEARCH_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.OrderHeaderSearch.submit();"><span><?php echo GO; ?></span></a>
        </td>
        </form>

  <form name="ProductHeaderSearch" action="product_search.php" method="get">
  <td class="tdHeader" align="center">
   &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
   <b><?php echo PRODUCT; ?></b>
   <input type="text" name="Query" size="10" value="<?php echo($PRODUCT_SEARCH); ?>">
   <a class="headerbutton" title="<?php echo SEARCH_PROD_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.ProductHeaderSearch.submit();"><span><?php echo SEARCH_BUTTON_TEXT; ?></span></a>
  </td>
<!--  <td width="5%" align="right">
  open drawer
  </td>
-->  
  </form>
 </tr>
</table>

<table width="100%" height="1" border="0" cellpadding="0" cellspacing="0">
<tr><td style="background-color: #606060;" width="100%" height="1"></td></tr>
</table>

<table width="100%" border="0" cellpadding="2" cellspacing="0">
 <tr height="35px">
  <form name="Orders">
  <td class="tdBlue" width="100%" align="center">
   <a class="button" title="<?php echo NEW_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); window.location.href='action.php?Action=NewOrder'"><span><?php echo NEW_ORDER; ?></span></a>
   <a class="button" title="<?php echo RETURN_EXCHANGE_BUTTON_TITLE; ?>" style="color: #993333" href="#" onclick="this.blur(); window.location.href='action.php?Action=ReturnOrder'"><span><?php echo RETURN_EXCHANGE; ?></span><input type="hidden" value="<?php echo RETURN_EXCHANGE; ?>"></a>
   
   &nbsp; | &nbsp;

   <b><?php echo PENDING_ORDERS; ?></b>:
   <select name="OrderIndex" style="background: #FFFFEF" onchange="window.location.href='action.php?Action=SelectOrder&OrderIndex='+document.Orders.OrderIndex.value">
   <option value="xxx"><?php echo IN_PROGRESS; ?></option>
   <option value="xxx">-----------------------</option>
   
   <?php show_pending_orders_dropdown();   ?>
   
   <option value="xxx">-----------------------</option>
   <option value="xxx"><?php echo ARCHIVED_ORDERS; ?></option>
   <option value="xxx">-----------------------</option>
   
   <?php show_archived_orders_dropdown();    ?> 
   
   </select>

<!--
   <a class="button" title="<?php echo GO; ?>" href="#" onclick="this.blur(); window.location.href='action.php?Action=SelectOrder&OrderIndex='+document.Orders.OrderIndex.value"><span><?php echo GO; ?></span></a>
-->

   &nbsp; | &nbsp;
   <b><?php echo HISTORY; ?></b>:
   <select name="OrderID" style="background: #FFFFEF" onchange="window.location.href='order.php?OrderID='+document.Orders.OrderID.value">
   
   <option value=""><?php echo SELECT_ONE; ?></option> 
   <option value=""></option> 
   <?php show_recent_orders_dropdown(); ?>
   <option value="xxx">-----------------------</option>   
      
   <option value="Orders"<?php if(substr_count($_SERVER['PHP_SELF'],"order_history.php") == 1){echo(" selected");} ?>><?php echo VIEW_MORE; ?></option>
   <option value="Report"<?php if(substr_count($_SERVER['PHP_SELF'],"reporting.php") == 1){echo(" selected");} ?>><?php echo ORDER_REPORTING; ?></option>
   </select>
<!--
   <a class="button" title="<?php echo GO; ?>" href="#" onclick="this.blur(); window.location.href='order.php?OrderID='+document.Orders.OrderID.value"><span><?php echo GO; ?></span></a>
-->
  </td>
  </form>
 </tr>
</table>

<table width="100%" height="1" border="0" cellpadding="0" cellspacing="0">
<tr><td style="background-color: #606060;" width="100%" height="1"></td></tr>
</table><br>

<!-- end header -->