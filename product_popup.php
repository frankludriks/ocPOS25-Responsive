<?php
//product_popup.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);


$ProductID = $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->GetProductID($_REQUEST['Index']);
$Q_Product = mysql_query("SELECT p.products_quantity, pd.products_name FROM " . PRODUCTS . " p, " . PRODUCTS_DESCRIPTION  . " pd WHERE p.products_id = '$ProductID' AND pd.products_id = p.products_id");
if(mysql_num_rows($Q_Product)){
	$R_Product = mysql_fetch_assoc($Q_Product);
}elseif($ProductID > 1000000000){
	$R_Product['products_name'] = "Non-Inventory Product";
	$R_Product['products_quantity'] = 9999;
}else{
	$Onload = "window.close()";
}



if($_POST['Quantity']){
	if (is_numeric($_POST['Quantity'])) {
		$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SetItemQuantity($_REQUEST['Index'], $_POST['Quantity']);
		$Onload .= "AddFunction();";
	}
}else{
	$Onload .= "document.ProductForm.Quantity.select();";
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title><?php echo($POSName) . ': ' . TITLE; ?></title>
       <link rel="Stylesheet" href="css/style.css">
       <script language="JavaScript" src="javascript.js" type="text/javascript"></script>
	   <script language="JavaScript">
	   
	   function AddFunction(){
	   		window.opener.location.reload();
			window.close();
	   }
	   
	   </script>
       <meta http-equiv="Content-Type" content="text/html; charset=us-ascii">
	   <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
</head>
<body onload="<?php echo($Onload); ?>">
  
 <table class="tableBorder" border="0" width="100%" cellpadding="2" cellspacing="1" align="center">
 <form name="ProductForm" method="post">
 <tr>
 <td width="100%" class="tdBlue" align="center">
  <b><?php echo($R_Product['products_name']); ?></b>
 </td>
 </tr>
 <tr>
 <td width="100%" align="center">
  <input type="text" name="<?php echo PRODUCT_QTY; ?>" size="10" maxlength="4" value="<?php echo($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->GetItemQuantity($_REQUEST['Index'])); ?>">
 </td>
 </tr>
 <tr height="45px">
 <td width="100%" class="tdBlue" align="center">
   <?php if($_SESSION['CurrentOrderIndex'] == -1 || $R_Product['products_quantity']==0){ ?>
      <a class="button" title="<?php echo SET_PRODUCT_QTY_BUTTON_TITLE; ?>" href="#" onclick="this.blur();"><span><?php echo SET_PRODUCT_QTY; ?></span></a>
   <?php } else { ?>
      <a class="button" title="<?php echo SET_PRODUCT_QTY_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.ProductForm.submit();"><span><?php echo SET_PRODUCT_QTY; ?></span><input type="hidden" value="<?php echo SET_PRODUCT_QTY; ?>"></a>
   <?php } ?>
      <a class="button" title="<?php echo CANCEL_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); window.close();"><span><?php echo CANCEL; ?></span><input type="hidden" value="<?php echo CANCEL; ?>"></a>
 </td>
 </tr>
 </form>
 </table>

</body>
</html>
