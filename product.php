<?php
// product.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}


$Q_Product = mysql_query("SELECT p.products_id, p.products_quantity, p.products_model, p.products_image, p.products_price, p.products_weight, " .
		"pd.products_name, pd.products_description, s.specials_new_products_price, s.expires_date, s.status, " .
		"if ((s.specials_new_products_price is not NULL), s.specials_new_products_price, p.products_price) as sales_price " .
		"FROM " . PRODUCTS . " p left join ". SPECIALS ." s on (p.products_id=s.products_id), " . PRODUCTS_DESCRIPTION . " pd " .
		"WHERE (p.products_id = '" . $_REQUEST['ProductID'] . "' AND " .
		"pd.products_id = p.products_id ) " .
        "AND pd.language_id = $language_id " .
		"LIMIT 1");

if(mysql_num_rows($Q_Product)){
	$R_Product = mysql_fetch_assoc($Q_Product);
}else{
	header("Location: index.php?error=no_product_found");
}

// -- Load all options
// -- Load available options for this product
if (use_attribs()) {
  $R_product_attribs = new attributes($_REQUEST['ProductID'],$language_id);
}

//get the total number of product options, so we know how many possible options can be on a product page
$option_count_query = mysql_query("SELECT count(*) as total from " . PRODUCTS_OPTIONS);
$option_count_results = mysql_fetch_assoc($option_count_query);

$option_count = $option_count_results['total'];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title><?php echo($POSName) . ': ' . TITLE; ?></title>
       <link rel="Stylesheet" href="css/style.css">
       <script language="JavaScript" src="javascript.js" type="text/javascript"></script>
<?php

// -- Load product page javascript
if (use_attribs()) {
  $R_product_attribs->product_page_js();
}

?>       
</head>
<!-- javascript function to recalculate price and quantity on page load -->
<body>
<?php include("includes/header.php"); ?>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <tr>
 <td width="100%" class="tdBlue" colspan="2" align="center">
  <b><?php echo PRODUCT_INFO; ?>"<?php echo($R_Product['products_name']); ?>"</b>
 </td>
 </tr>
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo PRODUCT_MODEL; ?></b></td>
 <td width="80%"><?php echo($R_Product['products_model']); ?></td>
 </tr>
 <tr>
 <td width="20%" class="tdBlue"><b><?PHP echo PRICE; ?></b></td>
 <td width="80%" id="product_price_display">
  <?php  
  // Store base product price for automated adjustments 
  // ----------------------------
  $product_final_price = $R_Product['products_price'];  
  	if (($R_Product['specials_new_products_price'] != NULL) && ($R_Product['status'] == '1') && ($R_Product['expires_date'] >= date("Y-m-d") || (substr($R_Product['expires_date'],0,10) == '0000-00-00') || (is_null($R_Product['expires_date']) ) ) ) {
		if (IN_STORE_PRICING == 1) {
		$R_Product['specials_new_products_price'] += ($R_Product['specials_new_products_price'] * IN_STORE_SURCHARGE);
			}
	  	echo $default_currency_symbol . "<s>".(number_format($R_Product['products_price'], 2, '.', ''))."</s> <span class='productSpecialPrice'>".(number_format($R_Product['specials_new_products_price'], 2, '.', ''))."</span>"; 
  $product_final_price =  $R_Product['specials_new_products_price'];  
		} else {
			if (IN_STORE_PRICING == 1) {
				$R_Product['products_price'] += ($R_Product['products_price'] * IN_STORE_SURCHARGE);
			}
	  	echo $default_currency_symbol . (number_format($R_Product['products_price'], 2, '.', '')); 
	  }
  ?>
 </td>
 </tr>
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo QTY_IN_STOCK; ?></b></td>
 <td width="80%" id="product_quantity_display">
 <?php
 echo($R_Product['products_quantity']);
 if($R_Product['products_quantity'] == 0 && ALLOW_SOLDOUT_PRODUCTS == 0){
 	//echo(" ( $R_Product['products_reorder_quantity'] reordered )");
 	echo('&nbsp;&nbsp;&nbsp;<font color="red">' . OUT_OF_STOCK . '</font>');
 }
 ?></td>
 </tr>
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo PRODUCT_WEIGHT; ?></b></td>
 <td width="80%"><?php echo($R_Product['products_weight']); ?> lbs.</td>
 </tr>
<?php
  //  Display attributes form elements
	if (use_attribs() && (count($R_product_attribs->options)>0)) {
?> <tr>
 <td width="100%" class="tdBlue" colspan="2" align="center">
  <b><?php echo PRODUCT_OPTIONS; ?></b>
 </td>
 </tr>
 <tr>
 <td width="100%" colspan="2">
<?php
    echo $R_product_attribs->product_form($product_final_price);
?>
 </td>
 </tr>
<?php	  
	} 
?>

<?php if(SHOW_PRODUCT_DESCRIPTION) { ?>
 <tr>
 <td width="100%" class="tdBlue" colspan="2" align="center">
  <b><?php echo PRODUCT_DESCRIPTION; ?></b>
 </td>
 </tr>
 <tr>
 <td width="100%" colspan="2">
  <?php echo($R_Product['products_description']); ?>
 </td>
 </tr>
<?php } ?>

<?php if(SHOW_PRODUCT_IMAGE) { ?>
 <tr>
 <td width="100%" class="tdBlue" colspan="2" align="center">
  <b><?php echo PRODUCT_IMAGE; ?></b>
 </td>
 </tr>
 <tr>
 <td width="100%" colspan="2">
  <img src="<?php echo IMAGE_PATH; ?><?php echo($R_Product['products_image']); ?>">
 </td>
 </tr>
<?php } ?>
 
 <tr height="45px">
 <td width="100%" class="tdBlue" colspan="2" align="center"><br>
  
  <?php if($_SESSION['CurrentOrderIndex'] == -1) { ?>
      <a class="button-disabled" title="<?php echo ADD_TO_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur();"><span><?php echo ADD_TO_ORDER; ?></span></a> 
  <?php } elseif (use_attribs() && (count($R_product_attribs->options)>0)) {
  //  Use form submit feature, with additional hidden fields, when adding product.
  //    This forces the POSTing of any option values.
  //  Use the old-format page redirection if attributes not used or not present
    ?>    <a class="button" title="<?php echo ADD_TO_ORDER_BUTTON_TITLE; ?>" href="#" onclick="check_attrform(document.product_options_form,<?php echo $option_count; ?>,'<?php echo MAKE_SELECTION; ?>')"><span><?php echo ADD_TO_ORDER; ?></span><input type="hidden" name="CreateAssign" value="<?php echo ADD_TO_ORDER; ?>"></a>
    <?php 
    } else {
    ?>    <a class="button" title="<?php echo ADD_TO_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); window.location.href='action.php?Action=AddItem&ProductID=<?php echo($R_Product['products_id']); ?><?php  
            if ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ReturnOrder == 1) {
                  echo ("&Quantity=-1");
               } else {
                  echo ("&Quantity=1");
               }
      ?>'"?><span><?php echo ADD_TO_ORDER; ?></span><input type="hidden" name="CreateAssign" value="<?php echo ADD_TO_ORDER; ?>"></a>
<?php 
    }
    
    if (use_attribs() && (count($R_product_attribs->options)>0)) {
?>
  		<input type=hidden name=Action value='AddItem'>
  		<input type=hidden name=ProductID value=<?php echo $R_Product['products_id'] ?> >
      <?php  
            if ($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->ReturnOrder == 1) {
                  $qty = -1;
               } else {
                  $qty = 1;
               }
      ?>
  		<input type=hidden name=Quantity value=<?php echo $qty;?> >
  		<input type=hidden name=Price value=<?php echo $product_final_price ?> >
  		<?php if (is_attrib_mode("QTP")) { ?>
  		<input type=hidden name=StockQuantity value=<?php echo $R_Product['products_quantity'] ?> >
  <?php } ?>
   	</form> 
  <?php 
  } 
  ?>
  
  <?php if (IN_STORE_PRICING == 0) { // Disallow price editing if in-store pricing is in effect.  It is too easy to confuse this and enter incorrect prices
  ?>     
     <a class="button" title="<?php echo EDIT_PRODUCT_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); window.location.href='product_edit.php?ProductID=<?php echo($R_Product['products_id']); ?>'"><span><?php echo EDIT_PRODUCT; ?></span></a>

     <a class="button" title="<?php echo EDIT_SPECIAL_PRICE_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); window.location.href='product_edit_sale.php?ProductID=<?php echo($R_Product['products_id']); ?>'"><span><?php echo EDIT_SPECIAL_PRICE; ?></span></a>
     
  <?php } ?>
  <a class="button" title="<?php echo BACK_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); window.location.href='index.php';"><span><?php echo BACK; ?></span></a>
  <br>
 </td>
 </tr>
 </table>
 
 
  </td>
 </tr>
</table>


<?php include("includes/footer.php"); ?>
</body>
</html>
