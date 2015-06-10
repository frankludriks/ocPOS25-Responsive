<?php include("../includes/db.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title>OllaCart Point of Sale Documentation - Products</title>
       <link rel="Stylesheet" href="../style.css">
</head>
<body>

<table width="760" border="0" cellpadding="4" cellspacing="0" align="center"><tr><td class="tdDoc" width="100%">

<?php include 'nav_top.php'; ?>

<h1 align="center">Products</h1>

<h2>Adding a product to an order</h2>

To add an item enter the mode or name in the search box at the top of the <a href="orders.php">order table</a>.
Products are matched by model or name. If one match is found, it will be added to the order. If this product contains attributes, the product info screen will be displayed with the options in a drop down box as shown.  If multiple matches
are found a list page, like the one below, will be displayed with all the results.<br><br>

<div align="center"><img src="images/product_search.gif" border="0" alt="Product Search Results"></div><br>

You can click the "Add to Order" button on the right to add a product to the order, or you can click on the
product to view more details or edit the product.<br><br>


<h2>Finding a product</h2>

If you wish to find a product without adding it to the current order, use the search form on the top right of
header menu bar. A list will be returned like the one shown above displaying all matches.<br><br>


<h2>Viewing/Editing a product</h2>

When you click on a product in the list above, or on the <a href="">order screen</a>, you will be taken to
a screen like the one below. If you wish to edit the details of a product, you can click the "Edit Product"
button and you will be taken to a edit form.<br><br>

<div align="center"><img src="images/product_view.gif" border="0" alt="View Product Details"></div><br>


</td></tr></table><br><br>

</body>
</html>