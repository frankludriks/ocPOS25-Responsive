<?php include("../includes/db.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title>OllaCart Point of Sale Documentation - Introduction</title>
       <link rel="Stylesheet" href="../style.css">
</head>
<body>

<table width="760" border="0" cellpadding="4" cellspacing="0" align="center"><tr><td class="tdDoc" width="100%">

<?php include 'nav_top.php'; ?>

<h1 align="center">Introduction</h1><br>


Most of the functions you will need have been placed in the header menu shown above.
This menu will always appear at the top of the screen.<br><br>

<div align="center"><img src="images/header.gif" border="0" alt="POS Header"></div><br>

The top (gray) bar allows you to search for an existing customer, or add a new one. It also allows you to
search the product database (products will be matches by model or name).<br><br>

Below that is a blue bar which allows you to manage your orders. There is a button to start a new order.
Clicking the button will do exactly that. It will create a new empty order.<br><br>

<img src="images/menu_orders.gif" border="0" align="left" alt="Pending Dropdown Menu">

There is also a dropdown menu which includes pending orders (in case you have multiple orders open at one
time), and also archived orders (in case you need to store a pending order for an extended period of
time--or share it between terminals). Pending orders are listed under the "On This Machine" header, and
archived orders are listed under the "Archived Orders" header. Selecting one of these orders and clicking
"Go" will set it to the current order (moving the previous "Current" order to pending if one exists).<br><br><br><br>

<img src="images/menu_history.gif" border="0" align="right" alt="History Dropdown Menu">

Lastly, there is a "History" dropdown menu which lists recently completed orders.
If you select one of these orders, and click "Go," you will be taken to the details of that order. There are
also options at the bottom of that dropdown menu for viewing more previous orders, or for viewing
<a href="reports.html">Order Reporting</a>.



</td></tr></table><br><br>

</body>
</html>