<?php
//product_search.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}


if (SEARCH_TERM == 'ALL') {
	$choose = 'AND';
} elseif (SEARCH_TERM == 'ANY') {
	$choose = 'OR';
} else $config_error = true;


// Capture query without leading or trailing spaces, or leading zeroes
// $Query = ltrim(trim($_GET["Query"]),"0");

// Capture query without leading or trailing spaces
$Query = trim($_GET["Query"]);

// sanitize search term(s)
$Query = sanitize($Query); 

// use only exact matches for products_model?
// we always use fuzzy matches for products_name
if (EXACT_MODEL_MATCH == 1) {
	$match_query = "'$Query'";
} else {
	$match_query = "'%$Query%'";
}

// remove asterisks in search string - taken care of by the LIKE statement in the query
$Query = str_replace("*", "", $Query); 
// redirect to index page if empty search
if (strlen($Query) < 1) {
	header("Location: index.php");
	exit();
}

// separate out multiple search terms so we can search for all words independently rather than as a complete string
$Queryparts = explode(" ", $Query, 6); 

// determine whether or not to include disabled products in search results
if (ALLOW_DISABLED_PRODUCTS == '0') {
$products_status_filter = " and p.products_status = '1' ";
} else {
    $products_status_filter = '';
}

if (ALLOW_SOLDOUT_PRODUCTS == '0') {
   $products_qty_filter = " AND p.products_quantity > 0 ";
} else {
    $products_qty_filter = '';
}

if (USE_PRODUCT_BARCODE == '1') {
// Verify that the barcode field exists.  Otherwise, ugly errors appear
    $barcode_query = mysql_query("show columns from " . PRODUCTS . " ");
    
    $i = 0;
    $fields=array();
    while ($row = mysql_fetch_assoc($barcode_query) ) {
       $fields[$i] = ($row['Field']);
       $i++;
    }
    if (!in_array(PRODUCT_BARCODE_FIELD, $fields)) {  // no product barcode (as defined in includes/db.php) column found.
        $products_barcode_filter = '';
    } else {
        $products_barcode_filter = " OR p." . PRODUCT_BARCODE_FIELD . " = '" . $Query . "' ";
    }
} else {
    $products_barcode_filter = '';
}

   
$product_query = "SELECT p.products_id, p.products_quantity, p.products_model, p.products_price, ".
		"pd.products_name ".
	"FROM	" . PRODUCTS . " p, " . PRODUCTS_DESCRIPTION . " pd ".
	" WHERE	(p.products_model LIKE $match_query OR 
	";
$sqls = array();
foreach ($Queryparts as $part) {
    $sqls[] = "pd.products_name LIKE '%$part%' ";
}
$product_query .= "
    (".join(" $choose ", $sqls).")
    $products_barcode_filter
    )
	AND pd.products_id = p.products_id 
    AND p.products_quantity > '0' 
    AND pd.language_id = $language_id 
    $products_status_filter " . " $products_qty_filter
    GROUP BY p.products_id 
    LIMIT $maximum_search_results
";

$Q_Product = mysql_query($product_query);


if( (mysql_num_rows($Q_Product)==1) && ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]) ) {
	$R_Product = mysql_fetch_assoc($Q_Product);
    // If this is a new return order (NOT one created from an existing order), add all new items as -1 rather than 1
    // In other words, if you are creating a return order from an existing order, AddItem below will add positive quantities to the cart
    // but if you are creating a new return order using the header return/exchange button, AddItem below will add negative quantities.
   if ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ReturnOrder == 1) {
      $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->AddItem($R_Product['products_id'], -1, IN_STORE_PRICING);
   } else {
      $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->AddItem($R_Product['products_id'], 1, IN_STORE_PRICING);
   }
	header("Location: index.php");
	exit();
} else {

		$ProductQ = "
            SELECT 
                p.products_id, 
                p.products_quantity, 
                p.products_model, 
                p.products_price, 
                pd.products_name, 
                s.specials_new_products_price, 
                s.expires_date, 
                if ((s.specials_new_products_price is not NULL AND ((s.expires_date >= '" . date("Y-m-d") . "') OR (s.expires_date LIKE '0001-01-01%') OR (s.expires_date LIKE '0000-00-00%') OR (s.expires_date IS NULL) )), s.specials_new_products_price, p.products_price) as sales_price 
            FROM " . PRODUCTS . " p left join ". SPECIALS ." s on (p.products_id=s.products_id) AND (s.status='1') , " . PRODUCTS_DESCRIPTION . " pd
			WHERE (
                p.products_model LIKE $match_query OR
        ";
        $sqls = array();
        foreach ($Queryparts as $part) {
            $sqls[] = "pd.products_name LIKE '%$part%' ";
        }
        $ProductQ .= "
            (".join(" $choose ", $sqls).")
            $products_barcode_filter
            )
			AND pd.products_id = p.products_id 
            AND pd.language_id = $language_id 
            $products_status_filter " . " $products_qty_filter
            GROUP BY p.products_id 
            LIMIT $maximum_search_results
        ";
		$Q_Product = mysql_query($ProductQ);
		}

// remove escaped apostrophes in query to preserve appearance of query term
$Query = str_replace("\'", "'", $Query); 

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
<body onload="document.ProductSearch.Query.focus();">

<?php

$PRODUCT_SEARCH = $Query;
?>
<div class="container">
      
		<?php include("includes/header.php"); ?>
      <div class="row marketing">
<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
<?php if($Query){ ?>
 <table class="table" align="center">
 <tr>
 <td align="center">
  <b><?php $results_count = mysql_num_rows($Q_Product);
           if ($results_count >= $maximum_search_results) {
	           $results_string = '<div class="alert alert-info" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> 
  ' . MORE_THAN . $maximum_search_results . RESULTS_FOUND . '  ' . DISPLAYING . $results_count . RESULTS . '</div>';
           } else {
	           $results_string = '<div class="alert alert-success" role="alert">
  <span class="glyphicon glyphicon-tick" aria-hidden="true"></span> 
  ' . DISPLAYING . $results_count . RESULTS . '</div>';
           }
           echo $results_string; 
     ?>
  </b>
 </td>
 </tr>
 <tr>
  <form name="ProductSearch" method="get">
	 <td width="100%" colspan="5" height="30px" class="tdHeader" align="center">
		 <input type="text" name="Query" size="20" value="">
		 <a class="btn btn-default btn-sm" role="button" title="<?php echo SEARCH_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.ProductSearch.submit();"><?php echo SEARCH; ?></a>
		 <?php 
			 if($_SESSION['CurrentOrderIndex'] == -1) { ; } // if no order don't display button to add non-inventory item to order
			 else {
		 ?>		 
		<a class="btn btn-default btn-sm" role="button" title="<?php echo NON_INVENTORY_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); window.location.href='product_noninventory.php'"><?php echo NON_INVENTORY; ?></a>
		 <?php } ?>	 
	 </td>
  </form>
 </tr>
 </table>
 <table class="table table-striped table-condensed table-hover">
 <tr>
   <thead>
   <th width="100" align="left"><b>ID</b></th>
	 <th width="100" align="left"><b><?php echo PRODUCT_MODEL; ?></b></th>
	 <th width="350" align="center"><b><?php echo PRODUCT_NAME; ?></b></th>
	 <th width="70"  align="center"><b><?php echo QTY_IN_STOCK; ?></b></th>
	 <th width="120"  align="center"><b><?php echo PRICE; ?></b></th>
	 <th width="180" align="center"><b><?php echo ADD_TO_ORDER; ?></b></th>
   </thead>
 </tr>
<?php while($R_Product = mysql_fetch_assoc($Q_Product)){ ?>
 <tr>
	 <td align="left">
	  <?php echo($R_Product['products_id']); ?>
	 </td>
	 <td align="left">
	  <?php echo($R_Product['products_model']); ?>
	 </td>
	 <td>
	  <a href="product.php?ProductID=<?php echo($R_Product['products_id']); ?>"><?php echo($R_Product['products_name']); ?></a>
	 </td>
	 <td align="center">
	  <?php echo($R_Product['products_quantity']); ?>
	 </td>
	 <td align="center">
	  <?php  
	  	if ($R_Product['sales_price'] <  $R_Product['products_price']) {
		  	if (IN_STORE_PRICING == 1) {
			  	$R_Product['sales_price'] += ($R_Product['sales_price'] * IN_STORE_SURCHARGE);
		  	}
		  	echo $default_currency_symbol . "<s>".(number_format($R_Product['products_price'], 2, '.', ''))."</s> <span class='productSpecialPrice'>".(number_format($R_Product['sales_price'], 2, '.', ''))."</span>"; 
			} else {
				if (IN_STORE_PRICING == 1) {
					$R_Product['products_price'] += ($R_Product['products_price'] * IN_STORE_SURCHARGE);
				}
				echo $default_currency_symbol . (number_format($R_Product['products_price'], 2, '.', '')); 
		  }
	  ?>
	 </td>
	 <td align="center">

<?php 
	 if($_SESSION['CurrentOrderIndex'] == -1) { // if no order button says add to new order
 ?>	
		<a class="btn btn-default btn-xs" role="button" title="<?php echo ADD_TO_NEW_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); window.location.href='action.php?Action=ItemNewOrder&ProductID=<?php echo($R_Product['products_id']); ?>'"><?php echo ADD_TO_NEW_ORDER; ?></a>
<?php } else { // otherwise, button says to add to existing order
	?> 
		<a class="btn btn-default btn-xs" role="button" title="<?php echo ADD_TO_ORDER_BUTTON_TITLE; ?>" onclick="this.blur(); window.location.href='action.php?Action=AddItem&ProductID=<?php echo($R_Product['products_id']); ?><?php  
            if ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ReturnOrder == 1) {
                  echo ("&Quantity=-1");
               } else {
                  echo ("&Quantity=1");
               }
      ?>'"><?php echo ADD_TO_ORDER; ?></a>
<?php } ?>
	 </td>
 </tr>
<?php } ?>
<tr><td><br></td></tr>
 </table>
<?php
	} else {
?>
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <tr>
	 <td width="100%" class="tdBlue" align="center">
	  <b><?php echo NO_SEARCH_TERMS; ?></b>
	 </td>
 </tr>
 <tr>
  <form name="ProductSearch" method="get">
	 <td width="100%" height="30px" class="tdHeader" align="center">
	 <input type="text" name="Query" size="20">
	 <a class="button" title="<?php echo SEARCH_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.ProductSearch.submit();"><span><?php echo SEARCH; ?></span></a>
	 </td>
  </form>
 </tr>
 </table>
<?php } ?>
 
 
  </td>
 </tr>
</table>


<?php include("includes/footer.php"); ?>
</body>
</html>
