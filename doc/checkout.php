<?php include("../includes/db.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title>OllaCart Point of Sale Documentation - Checkout</title>
       <link rel="Stylesheet" href="../style.css">
</head>
<body>

<table width="760" border="0" cellpadding="4" cellspacing="0" align="center"><tr><td class="tdDoc" width="100%">

<?php include 'nav_top.php'; ?>

<h1 align="center">Checkout</h1><br>

When you choose to complete an order, you will be taken to a screen like the one below. There are
three payment methods that can be used: Cash, Credit Card, or Check. You can Choose to process
the order, return to the order screen, or preview the receipt.<br><br>

<div align="center"><img src="images/checkout.gif" border="0" alt="Checkout Screen"></div><br>

When you click "Yes, Process Order," the Credit Card type will simply complete the order.
Cash and Check, however, will popup a window like the one below for you to insert the amount tendered. Once
you've inserted the amount tendered, click "Process Order" and the order will be completed.
<br><br>

<div align="center"><img src="images/cash.gif" border="0" alt="Cash Tendered Popup"></div><br>

After the order is processed, you will then be taken to the <a href="reports.php">order details screen</a>.

<br><br>

<div align="center"><img src="images/cash_sale_change.gif" border="0" alt="Cash Change Due"></div><br>

If cash is the selected method of payment, the cash amount screen will pop up.  After entering the dollar amount given, you will be prompted with the change due as seen here.

</td></tr></table><br><br>

</body>
</html>