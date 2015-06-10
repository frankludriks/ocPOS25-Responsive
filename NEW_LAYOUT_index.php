<?php 
// index.php


if (file_exists("install.php")) {
    header("Location: install.php");
    die();
}

include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title><?php echo($POSName) . ': ' . TITLE; ?></title>
    <link href="css/newstyle.css" rel="stylesheet" type="text/css" />
    <script language="JavaScript" type="text/javascript" src="js/events.js"></script>
    <script language="JavaScript" type="text/javascript" src="js/calendar.js"></script>
</head>

<!-- <body onLoad="goforit(), changedate('return') <?php if($_SESSION['CurrentOrderIndex'] != -1){ echo(",document.AddProductOrder.ProductQuery.focus();"); } ?>"> -->
<body onLoad="<?php if($_SESSION['CurrentOrderIndex'] != -1){ echo(",document.AddProductOrder.ProductQuery.focus();"); } ?>">

<script type="text/javascript" language="javascript1.1" src="js/date.js"></script>
	<!--main div part start -->
		<div id="mainDiv">
<?php 
    // HEADER BARS
    include("includes/newheader.php"); 
?>
			<!--body part star -->
				<div id="bodyDiv">
					<span class="top">&nbsp;</span>
						<!--inner div part statt -->
							<div id="innerDiv">
								<span class="top">&nbsp;</span>
									<div>
								<!--left part start -->
									<div id="leftPart">
										<!--time part start -->
											<div id="time">
												<span class="top">&nbsp;</span>
													
												<div id="clock"></div>
												<span class="bot">&nbsp;</span>
											</div>
										<!--time part end -->
										<!--calendar part sart-->
											<div id="calendar">
												
												<span class="top">&nbsp;</span><div id="CalendarPanel">
												<div id="calendar"><!--  Dynamically Filled --></div>
												</div>
												<span class="bot">&nbsp;</span>
											</div>
										<!--calendar part end -->
									</div>
								<!--left part end -->
								<!--right part start -->
									<div id="rightPart">
										<!--search part start -->
											<div id="searchPart">
												<!--customer part start -->
													<div id="customer">
														<form name="CustomerSearch" action="customer_search.php" method="get">
															<label><em>Customer  :</em></label>
															<input type="text" name="Query" class="textbox" value="<?php echo($CUSTOMER_SEARCH); ?>">
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
															<input type="submit" name="submit" value="" class="search" />
															<input type="button" name="new" value="" class="new" onclick="this.blur(); window.location.href='customer_new.php'" />
														</form>
														<span>&nbsp;</span>
													</div>
												<!--customer part end -->
												<!--product part start -->
													<div id="product">
														<form name="OrderSearch" action="order.php" method="get">
																<label><em>Order: </em></label>
															<input type="text" name="OrderID" value="" class="textbox" />
															<input type="button" name="new" class="new" onclick="this.blur(); document.OrderSearch.submit();" />
														</form>
														<span>&nbsp;</span>
													</div>
												<!--product part end -->
											</div>
										<!--search part end -->
										<!--order part start -->
											<div id="order">
												<!--new order -->
													<div id="neworder_img"><a href="#"><img src="images/order_icon.gif" alt="" width="79" height="72" border="0" /></a></div>
													<div id="new_order">
														<a href="#"><em>New Order</em></a>
													</div>
													
												<!--new order -->
												<!--current order -->
													<!-- <div id="current">
														<em>Current Order  :</em><span><em>$ 28.60</em></span>													</div>
                                                        -->
												<!--current order end -->
												<!--pending part start -->
													<div id="pending">
													<p><em>Pending Orders  :</em></p>
													<form name="Orders" method="post" action="">
                                                       <select name="OrderIndex" style="background: #FFFFEF" onchange="window.location.href='action.php?Action=SelectOrder&OrderIndex='+document.Orders.OrderIndex.value">
                                                       <option value="xxx"><?php echo IN_PROGRESS; ?></option>
                                                       <option value="xxx">-----------------------</option>
                                                       
                                                       <?php show_pending_orders_dropdown();   ?>
                                                       
                                                       <option value="xxx">-----------------------</option>
                                                       <option value="xxx"><?php echo ARCHIVED_ORDERS; ?></option>
                                                       <option value="xxx">-----------------------</option>
                                                       
                                                       <?php show_archived_orders_dropdown();    ?> 
                                                       
                                                       </select>
													</form>
													</div>
												<!--pending part end -->
												<!--pending part start -->
													<div id="history">
													<p><em>History :</em></p>
													<form name="pending" method="post" action="">
                                                       <select name="OrderID" style="background: #FFFFEF" onchange="window.location.href='order.php?OrderID='+document.Orders.OrderID.value">
                                                       
                                                       <option value=""><?php echo SELECT_ONE; ?></option> 
                                                       <option value=""></option> 
                                                       <?php show_recent_orders_dropdown(); ?>
                                                       <option value="xxx">-----------------------</option>   
                                                          
                                                       <option value="Orders"<?php if(substr_count($_SERVER['PHP_SELF'],"order_history.php") == 1){echo(" selected");} ?>><?php echo VIEW_MORE; ?></option>
                                                       <option value="Report"<?php if(substr_count($_SERVER['PHP_SELF'],"reporting.php") == 1){echo(" selected");} ?>><?php echo ORDER_REPORTING; ?></option>
                                                       </select>
													</form>
													</div>
												<!--pending part end -->
												<span class="right">&nbsp;</span>
											</div>
										<!--order part end -->
										<!--product part start -->
											<div id="product_detail">
												<span class="top">&nbsp;</span>
												<div id="product_detail_inner">
<?php
if($_SESSION['CurrentOrderIndex'] != -1){
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->PrintFull(false);
} else { 
}
// not keeping session ID or something -- can't pull up new order unless getting it from OLDindex.php
?>

													<table width="850" border="0" align="center" cellpadding="0" cellspacing="0" class="border">
													  <tr>
														<td width="87" align="left" valign="top" >&nbsp;</td>
														<td width="137" align="left" valign="top" >&nbsp;</td>
														<td width="215" align="left" valign="top">&nbsp;</td>
														<td width="182" align="left" valign="top">&nbsp;</td>
														<td width="186" align="left" valign="top">&nbsp;</td>
														<td width="98" align="left" valign="top">&nbsp;</td>
													  </tr>
													  <tr>
													    <td align="center" valign="top"><em>Model #</em></td>
													    <td align="center" valign="top"><em>Image</em></td>
													    <td align="center" valign="top"><em>Product Name</em></td>
													    <td align="right" valign="top"><em>Price</em></td>
													    <td align="center" valign="top"><em>Quantity</em></td>
													    <td align="center" valign="top"><em>Total</em></td>
												      </tr>
													  <tr>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="right" valign="top">&nbsp;</td>
													    <td align="center" valign="top"><em>Subtotal</em></td>
													    <td align="center" valign="top"><em>0.00</em></td>
												      </tr>
													  <tr>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="right" valign="top">&nbsp;</td>
													    <td align="center" valign="top"><em>Tax</em></td>
													    <td align="center" valign="top"><em>0.00</em></td>
												      </tr>
													  <tr>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="right" valign="top">&nbsp;</td>
													    <td align="center" valign="top"><em>Total</em></td>
													    <td align="center" valign="top">0.00</td>
												      </tr>
													  <tr>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="right" valign="top">&nbsp;</td>
													    <td align="center" valign="top"><em>#  of items:</em></td>
													    <td align="center" valign="top"><em>0</em></td>
												      </tr>
													  <tr>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="right" valign="top">&nbsp;</td>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="center" valign="top">&nbsp;</td>
												      </tr>
													  <tr>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="right" valign="top">&nbsp;</td>
													    <td align="center" valign="top">&nbsp;</td>
													    <td align="center" valign="top">&nbsp;</td>
												      </tr>
													</table>

													<ul class="icon">
														<li><a href="#" class="remove_order" onclick="this.blur(); window.location.href='action.php?Action=RemoveOrder'"><em>Remove Order</em></a></li>
														<li><a href="#" class="drop"><em>Drop Customer</em></a></li>										
														<li><a href="#" class="add_comment"><em>Add Comments</em></a></li>										
														<li><a href="#" class="tax"><em>Apply Discount</em></a></li>										
														<li><a href="#" class="archive"><em>Archive Order</em></a></li>										
														<li class="nobor"><a href="#" class="comp"><em>Complete Order</em></a></li>										
													</ul>
												</div>
											</div>
										<!--product part end -->
									</div>
								<!--right part end -->
								<br class="spacer" />
								</div>
								<span class="bot">&nbsp;</span>								
							</div>
						<!--inner div part end -->
					
					<span class="bot">&nbsp;</span>
				</div>
			<!--body part end -->
		</div>
	<!--main div part end -->
</body>
</html>
