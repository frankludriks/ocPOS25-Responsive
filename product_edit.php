<?php
// product_edit.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}


if (IN_STORE_PRICING == 1) {
	$url = 'product.php?ProductID=' . $_REQUEST['ProductID'];
	header('Location: ' . $url);
}

$Q_Product = mysql_query("SELECT * FROM " . PRODUCTS . " p, " . PRODUCTS_DESCRIPTION . " pd WHERE
	p.products_id = '" . $_REQUEST['ProductID'] . "'
    AND pd.language_id = $language_id 
    AND pd.products_id = p.products_id LIMIT 1");
if(mysql_num_rows($Q_Product)){
	$R_Product = mysql_fetch_assoc($Q_Product);
}else{
	header("Location: index.php");
}


// -- Load available options for this product
if (use_attribs()) {
  $R_all_attribs = new attributes(0,$language_id);
  $R_product_attribs = new attributes($_REQUEST['ProductID'],$language_id);
}

//$ReqVars = Array (
//'Model Number' => $_POST['products_model'],
//'Price' => $_POST['products_price']
//);

//if(TestFormInput(false,$ReqVars)){
if (isset($_POST['update'])) {
    $model = mysql_real_escape_string($_POST['products_model']);
    $description = mysql_real_escape_string($_POST['products_description']); 
    
    $product_update_sql = ("UPDATE " . PRODUCTS  . " SET
        products_quantity='" . $_POST['products_quantity'] . "',
		products_model='" . $model . "',
		products_price='" . $_POST['products_price'] . "',
		products_last_modified='".date("Y-m-d H:i:s")."',
        products_weight='" . $_POST['products_weight'] . "'
		WHERE products_id='" . $_REQUEST['ProductID'] . "'
    ");
    $product_update = oc_query($product_update_sql, 'Product Update');
    
/* when doing low order stock reports (i.e. you have a products_reorder_quantity row in your prouducts table) then use the query below
    mysql_query("UPDATE " . PRODUCTS . " SET
        products_quantity='" . $_POST['products_quantity'] . "',
        products_reorder_quantity='" . $_POST['products_reorder_quantity'] . "',
		products_model='" . $_POST['products_model'] . "',
		products_price='" . $_POST['products_price'] . "',
		products_last_modified='".date("Y-m-d H:i:s")."',
        products_weight='" . $_POST['products_weight'] . "'
		WHERE products_id='" . $_REQUEST['ProductID'] . "'    
		);
*/
    
	$product_description_sql = ("UPDATE " . PRODUCTS_DESCRIPTION . " SET
        products_description='" . $description . "'
		WHERE products_id='" . $_REQUEST['ProductID'] . "'
        AND language_id = $language_id 
    ");
    $product_description_update = oc_query($product_description_sql, 'Product Description Update');
    
	$Q_Product = mysql_query("SELECT * FROM " . PRODUCTS . " p, " . PRODUCTS_DESCRIPTION . " pd WHERE
		p.products_id = '" . $_REQUEST['ProductID'] . "' AND
		pd.products_id = p.products_id 
        AND pd.language_id = $language_id LIMIT 1");

	$R_Product = mysql_fetch_decode_assoc($Q_Product);
	
  //  update product attributes
	if (use_attribs()) {
	  $R_all_attribs->update_config_form($R_product_attribs);
	} 
	
	$SUCCESS=true;
}else{
	$SUCCESS=false;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title><?php echo($POSName) . ': ' . TITLE; ?></title>
       <link rel="Stylesheet" href="css/style.css">
       <script language="JavaScript" src="javascript.js" type="text/javascript"></script>
</head>
<body>

<?php include("includes/header.php"); ?>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
<?php
  if (isset($_POST['update'])) { 
	  if ($SUCCESS=true) {
		  echo '<center><b><font color="green">' . UPDATED_SUCCESS . '</b></font></center><br>';
	  } else {
		  echo '<center><b><font color="red">' . UPDATED_FAILED . '</b></font></center><br>';
	  }
  }
?>
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <form name="ProductEdit" method="post">
 <input type="hidden" name="posted" value="true">
 <tr>
 <td width="100%" class="tdBlue" colspan="2" align="center">
  <b><?php echo PRODUCT_INFO; ?>"<?php echo($R_Product['products_name']); ?>"</b>
 </td>
 </tr>
 <!--<tr>
 <td width="100%" colspan="2" align="center">
  <div align="left">
  <?php
  //TestFormInput($_POST['posted'],$ReqVars);
  ?>
  </div>
 </td>
 </tr>
 -->
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo PRODUCT_MODEL; ?></b></td>
 <td width="80%"><input type="text" name="products_model" size="15" value="<?php echo($R_Product['products_model']); ?>"></td>
 </tr>
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo PRICE; ?></b></td>
 <td width="80%"><?php echo $default_currency_symbol; ?><input type="text" name="products_price" size="14" value="<?php echo(number_format(($R_Product['products_price']), 2, '.', '')); ?>"></td>
 </tr>
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo QTY_IN_STOCK; ?></b></td>
 <td width="80%"><input type="text" name="products_quantity" size="15" value="<?php echo($R_Product['products_quantity']); ?>"></td>
 </tr>
 <!-- use this section if a reorder quantity contribution is installed
  <tr>
 <td width="20%" class="tdBlue"><b>Quantity Reordered</b></td>
 <td width="80%"><input type="text" name="products_reorder_quantity" size="15" value="<?php echo($R_Product['products_reorder_quantity']); ?>"></td>
 </tr>
 --> 
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo WEIGHT; ?></b></td>
 <td width="80%"><input type="text" name="products_weight" size="15" value="<?php echo($R_Product['products_weight']); ?>"> lbs.</td>
 </tr>
 <tr>
 <td width="100%" class="tdBlue" colspan="2" align="center">
  <b><?php echo PRODUCT_DESCRIPTION; ?></b>
 </td>
 </tr>
 <tr>
 <td width="100%" colspan="2" align="center">
  <textarea name="products_description" cols="140" rows="10"><?php echo($R_Product['products_description']); ?></textarea>
 </td>
 </tr>
<?php
  //  Display attributes form elements
	if (use_attribs()) {
?>	<tr>
	<td width="100%" class="tdBlue" colspan=2 align="center"><table width="100%" cellspacing=0 cellpadding=0 border=0>
<?php
    echo $R_all_attribs->config_form($R_product_attribs);
?>
	</table>
	</td>
	</tr>
<?php	  
	} 
?>
 <tr height="45px">
 <td width="100%" class="tdBlue" colspan="2" align="center"><br>
      <input type="hidden" name="update" value="1">
      <a class="button" title="<?php echo UPDATE_PRODUCT_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); document.ProductEdit.submit();"><span><?php echo UPDATE; ?></span></a>
      <a class="button" title="<?php echo BACK_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); window.location.href='product.php?ProductID=<?php echo($R_Product['products_id']); ?>'"><span><?php echo BACK; ?></span></a>
 </td>
 </tr>
 </form>
 </table>
 
 
  </td>
 </tr>
</table><br><br>

<?php include("includes/footer.php"); ?>
</body>
</html>
