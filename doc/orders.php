<?php include("../includes/db.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title>OllaCart Point of Sale Documentation - Orders</title>
       <link rel="Stylesheet" href="../style.css">
</head>
<body>

<table width="760" border="0" cellpadding="4" cellspacing="0" align="center"><tr><td class="tdDoc" width="100%">

<?php include 'nav_top.php'; ?>

<h1 align="center">Orders</h1>

<h2>Overview</h2>

<div align="center"><img src="images/order.gif" border="0" alt="Order Screen"></div><br>


Above if the order screen. Near the top is bar for items to the order. They are matched by model or
name, and a list page will be displayed if more than one match is found. There is also a "Non-Inventory"
button for adding random items on the fly that are not in the database. See below for more details.<br><br>

Each item in the order will be listed. You can click on the name for that product's details. If the item added to the order requires an attribute option to be selected (ie. size, color) you will be taken to the product info screen and able to select from a drop down box the attribute option to be assigned.  There is a set
of button on the right side of each item that will let you adjust the price, increase/decrease/specify quanity,
or remove the item from the order.<br><br>

On the bottom of the table is a set of buttons. Here you can remove the order, clear the assigned customer (if
one is assigned), add comments (see below), set the order to be tax exempt, archive the order, or
<a href="checkout.php">complete the order</a>.

Archiving the order temporarily writes it to the database, so that it can be reopened in the future or on another
terminal.(Note: Archived order will be accessible even after the computer is rebooted/shutdown. Pending order will
be removed when the web browser is exited.)<br><br>

<h2>Non-Inventory Items</h2>

The non-inventory function is for products you are selling temporarily that are not in the product database.
To add one click the "Non-Inventory" button at the top of the order table. You will be taken to a form like the
one below where you can specify the details of the product. Once you're done, click "Add Product to Order" to add
the product to the order and return to the order screen.<br><br>

<div align="center"><img src="images/noninventory.gif" border="0" alt="Non-Inventory Product Form"></div>


<h2>Comments</h2>

To add comments to an order, click the "Add Comments" button on the order screen. You will be presented with a form
like the one below that will retain any comments you enter. Upon completion, the comments will be written to the
database and will be viewable in the <a href="report.php">order details</a>.<br><br>
<div align="center"><img src="images/comments.gif" border="0" alt="Order Comments Form"></div>

<h2>Discounts</h2>

Discounts can be assigned to each order based on dollar amount or percentage.  To add a discount, simply click on the discount button.  The discount screen will appear and allow to to click a radio button for either percentage or dollar amount, enter the amount and click submit.  To remove this discount, click the remove option and hit submit.<br><br>

<div align="center"><img src="images/discount.gif" border="0" alt="Order Discounts Form"></div>

</td></tr></table><br><br>

</body>
</html>