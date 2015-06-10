<?php include("../includes/db.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title>OllaCart Point of Sale Documentation - Reporting</title>
       <link rel="Stylesheet" href="../style.css">
</head>
<body>

<table width="760" border="0" cellpadding="4" cellspacing="0" align="center"><tr><td class="tdDoc" width="100%">

<?php include 'nav_top.php'; ?>

<h1 align="center">History</h1><br>

<img src="images/menu_history.gif" border="0" align="left" alt="History Dropdown Menu">

There is a "History" dropdown menu which lists recently completed orders on the top menu bar.
If you select one of these orders, and click "Go," you will be taken to the details of that order. 
If the order you're looking for is not list in the menu, choose the "View More..." option and click
"Go" to get a full list like the one below.<br><br><br><br>

<div align="center"><img src="images/order_history.gif" border="0" alt="POS Order History"></div><br>

Once you choose an order from the dropdown or from the full page listing, you'll be taken to screen
like the one below, displaying the details of the order.<br><br>

<div align="center"><img src="images/order_details.gif" border="0" alt="POS Order History"></div><br>

You also have the option of viewing the receipt for the order, or voiding the order. Voiding it set
a flag in the database establishing that the order has been cancelled AND WILL return the items
back to inventory (incrementing the quantity of the purchased products by the number purchased).<br><br>




<h1 align="center">Reporting</h1><br>



<div align="center"><img src="images/reporting.gif" border="0" alt="POS Reporting Options"></div><br>


OllaCart Point of Sale comes with 4 major report types, as you can see above. To get to this screen, click on the
"History" dropdown menu near the top right of the screen, choose "Order Reporting..." and click "Go."<br><br>

These reports are fairly self-explanatory. "Invoice Order Reporting" simply shows a list of the orders
that occurred between the selected times, and allows you to view the details. "Comparison Reporting" uses
tables and graphs to compare a pair, or range, or days, months, or years. "Days of the Week Reporting"
uses tables and graphs to compare days of the week. Finally, "Hours of the Day Reporting" uses tables and
graphs to compare hours of the day.


</td></tr></table><br><br>

</body>
</html>