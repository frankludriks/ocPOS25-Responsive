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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
       <title><?php echo($POSName) . ': ' . TITLE; ?></title>
       <link rel="Stylesheet" href="css/style.css">
       <script language="JavaScript" src="javascript.js" type="text/javascript"></script>
</head>
<body onload="document.ProductSearch.Query.focus();">

<?php

$PRODUCT_SEARCH = $Query;

include("includes/header.php");
?>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
<?php if($Query){ ?>
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <tr>
 <td width="100%" class="tdBlue" colspan="6" align="center">
  <b><?php $results_count = mysql_num_rows($Q_Product);
           if ($results_count >= $maximum_search_results) {
	           $results_string = MORE_THAN . $maximum_search_results . RESULTS_FOUND . '  ' . DISPLAYING . $results_count . RESULTS;
           } else {
	           $results_string = DISPLAYING . $results_count . RESULTS;
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
		 <a class="button" title="<?php echo SEARCH_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.ProductSearch.submit();"><span><?php echo SEARCH; ?></span></a>
		 <?php 
			 if($_SESSION['CurrentOrderIndex'] == -1) { ; } // if no order don't display button to add non-inventory item to order
			 else {
		 ?>		 
		<a class="button" title="<?php echo NON_INVENTORY_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); window.location.href='product_noninventory.php'"><span><?php echo NON_INVENTORY; ?></span></a>
		 <?php } ?>	 
	 </td>
  </form>
 </tr>
 <tr>
	 <td width="100" class="tdBlue" align="center"><b><?php echo PRODUCT_MODEL; ?></b></td>
	 <td width="400" class="tdBlue" align="center"><b><?php echo PRODUCT_NAME; ?></b></td>
	 <td width="70"  class="tdBlue" align="center"><b><?php echo QTY_IN_STOCK; ?></b></td>
	 <td width="70"  class="tdBlue" align="center"><b><?php echo PRICE; ?></b></td>
	 <td width="180" class="tdBlue" align="center"><b><?php echo ADD_TO_ORDER; ?></b></td>
 </tr>
<?php while($R_Product = mysql_fetch_assoc($Q_Product)){ ?>
 <tr height="40px">
	 <td width="100" align="center"><br>
	  <?php echo($R_Product['products_model']); ?>
	 </td>
	 <td width="400"><br>
	  <a href="product.php?ProductID=<?php echo($R_Product['products_id']); ?>"><?php echo($R_Product['products_name']); ?></a>
	 </td>
	 <td width="70" align="center"><br>
	  <?php echo($R_Product['products_quantity']); ?>
	 </td>
	 <td width="70" align="center"><br>
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
	 <td width="180" align="center"><br>

<?php 
	 if($_SESSION['CurrentOrderIndex'] == -1) { // if no order button says add to new order
 ?>	
		<a class="button" title="<?php echo ADD_TO_NEW_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); window.location.href='action.php?Action=ItemNewOrder&ProductID=<?php echo($R_Product['products_id']); ?>'"?><span><?php echo ADD_TO_NEW_ORDER; ?></span></a>
<?php } else { // otherwise, button says to add to existing order
	?> 
		<a class="button" title="<?php echo ADD_TO_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); window.location.href='action.php?Action=AddItem&ProductID=<?php echo($R_Product['products_id']); ?><?php  
            if ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ReturnOrder == 1) {
                  echo ("&Quantity=-1");
               } else {
                  echo ("&Quantity=1");
               }
      ?>'"?><span><?php echo ADD_TO_ORDER; ?></span></a>
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
