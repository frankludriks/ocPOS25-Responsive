<?php
// product_adjust_price.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);


$CLOSE = " onload=\"document.FormChange.Price.select()\"";

$_REQUEST['ProductID'] = $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->GetProductID($_REQUEST['Index']);


$Q_Product = mysql_query("SELECT pd.products_name,
                 if ((s.specials_new_products_price is not NULL), s.specials_new_products_price, p.products_price) as products_price ".
	"FROM " . PRODUCTS . " p left join ". SPECIALS ." s on (p.products_id=s.products_id) AND (s.status='1') , " . PRODUCTS_DESCRIPTION . " pd " .
	"WHERE p.products_id = '" . $_REQUEST['ProductID'] . "' AND pd.products_id = p.products_id LIMIT 1");


if(mysql_num_rows($Q_Product)){
	$R_Product = mysql_fetch_assoc($Q_Product);
}elseif($_REQUEST['ProductID'] > 1000000000){
	$R_Product['products_name'] = "Non-Inventory Product";
}else{
	$CLOSE = " onload=\"window.close();\"";
}


if($_POST['Price']){
	if (is_numeric($_POST['Price'])) {
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SetItemPrice($_POST['Index'], $_POST['Price']);
	$CLOSE = " onload=\"window.opener.location.reload(); window.close();\"";
	} else {
//PY: if the input included % (e.g. 10%), discount by percent
		$len=strlen($_POST['Price']);

		if ((substr($_POST['Price'] , $len-1,1) == '%') &&
			 is_numeric(substr($_POST['Price'],0,len-1)))
		{
			$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->SetItemPriceByPercentDiscount($_POST['Index'], substr($_POST['Price'],0,len-1));
			$CLOSE = " onload=\"window.opener.location.reload(); window.close();\"";
		}
	}
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title><?php echo($POSName) . ': ' . TITLE; ?></title>
       <link rel="Stylesheet" href="css/style.css">
       <script language="JavaScript" src="javascript.js" type="text/javascript"></script>
</head>
<body<?php echo($CLOSE); ?>>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
 <table class="tableBorder" border="0" width="100%" cellpadding="2" cellspacing="1" align="center">
 <form name="FormChange" method="post">
 <input type="hidden" name="Index" value="<?php echo($_REQUEST['Index']); ?>">
 <tr>
 <td width="100%" colspan="2" class="tdBlue" align="center">
  <b><?php $_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->PrintHeader(); ?></b>
 </td>
 </tr>
 <tr>
 <td width="100%" colspan="2" class="tdHeader" align="center">
 <?php echo($R_Product['products_name']); ?>
 </td>
 </tr>
 <?php if($_REQUEST['ProductID'] < 1000000000){ ?>
 <tr>
 <td width="40%" class="tdBlue"><b><?php echo PRICE; ?></b></td>
 <td width="60%"><?php  echo $default_currency_symbol . (number_format(($R_Product['products_price']), 2, '.', '')); ?></td>
 </tr>
 <?php } ?>
 <tr>
 <td width="40%" class="tdBlue"><b><?php echo NEW_PRICE; ?></b></td>
 <td width="60%">
 <?php echo $default_currency_symbol; ?><input type="text" name="<?php echo PRICE; ?>" size="20" value="<?php echo(number_format(($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->GetItemPrice($_REQUEST['Index'])), 2, '.', '')); ?>">
 </td>
 </tr>
 <tr height="45px">
 <td width="100%" colspan="2" class="tdBlue" align="center">
   <a class="button" title="<?php echo UPDATE_PRICE_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.FormChange.submit();"><span><?php echo UPDATE_PRICE; ?></span></a>
   <a class="button" title="<?php echo CANCEL_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); window.close();"><span><?php echo CANCEL; ?></span></a>
 </td>
 </tr>
 </form>
 </table>
 
 
  </td>
 </tr>
</table>

</body>
</html>
