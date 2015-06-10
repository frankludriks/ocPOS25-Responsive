<?php
// product_edit_sale.php


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
	header("Location: product.php?ProductID=" . $_REQUEST['ProductID'] . "");
}

//$ReqVars = Array (
//'Model Number' => $_POST['products_model'],
//'Price' => $_POST['products_price']
//);

//if(TestFormInput(false,$ReqVars)){

if ( (isset($_REQUEST['delete']) && ($_REQUEST['delete'] == '1')) ) {
	mysql_query("DELETE FROM " . SPECIALS . " WHERE products_id='" . $_REQUEST['ProductID'] . "'");
	header("Location: product.php?ProductID=" . $_REQUEST['ProductID'] . "");
	
}
		
//if a special exists, then update	
if ( isset($_POST['update']) || isset($_POST['insert']) ) {
	if (!is_int($_POST['year']) || !is_int($_POST['month']) || !is_int($_POST['day']) ) { 
		$expires_date = $_POST['year'] . '-' . $_POST['month'] . '-' . $_POST['day'] . ' 00:00:00';
	} else { $expires_date = NULL;
	}
	$today = date("Y-m-d") . " 00:00:00";
	
	if (isset($_POST['update'])) {
	    mysql_query("UPDATE " . SPECIALS . " SET
			specials_new_products_price='" . $_POST['specials_new_products_price'] . "',
			specials_last_modified='$today',
	        expires_date='$expires_date',
			date_status_change='$today',
	        status='" . $_POST['status'] . "'
			WHERE products_id='" . $_REQUEST['ProductID'] . "'
	    ");
	}

//else, insert
	if (isset($_POST['insert'])) {
	    mysql_query("INSERT INTO " . SPECIALS . " SET
	        products_id='" . $_REQUEST['ProductID'] . "',
			specials_new_products_price='" . $_POST['specials_new_products_price'] . "',
			specials_date_added='$today',
	        expires_date='$expires_date',
	        status='" . $_POST['status'] . "'
	    ");
	}

//	$R_Product = mysql_fetch_decode_assoc($Q_Product);
	
	$SUCCESS=true;
}else{
	$SUCCESS=false;
}

$Q_Product = mysql_query("SELECT pd.products_name, s.specials_new_products_price, s.expires_date, s.status FROM " . SPECIALS . " s, " . PRODUCTS_DESCRIPTION . " pd WHERE s.products_id = '" . $_REQUEST['ProductID'] . "' AND pd.products_id = s.products_id");

if(mysql_num_rows($Q_Product)){ 
// found that this product has a specials price, do an update
	$update_or_insert = 'update';
	$R_Product = mysql_fetch_assoc($Q_Product);
	if ($R_Product['status'] == '1') {
		$enabled = 'checked';
		$disabled = '';
	} else {
		$enabled = '';
		$disabled = 'checked';
	}
	
	$year = substr($R_Product['expires_date'], 0, 4);
	$month = substr($R_Product['expires_date'], 5, 2);
	$day = substr($R_Product['expires_date'], 8, 2);
}else{ 
// this product has no specials price, create a new row in specials table
	$update_or_insert = 'insert';
	$enabled = 'checked';
	$Q_Product = mysql_query("SELECT pd.products_name FROM " . PRODUCTS_DESCRIPTION . " pd WHERE pd.products_id = '" . $_REQUEST['ProductID'] . "'");
	if(mysql_num_rows($Q_Product)){
		$R_Product = mysql_fetch_assoc($Q_Product);
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
<body>

<?php include("includes/header.php"); ?>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <form name="EditSpecial" method="post">
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
 <td width="20%" class="tdBlue"><b><?php echo SPECIAL_PRICE; ?></b></td>
 <td width="80%"><?php echo $default_currency_symbol; ?><input type="text" name="specials_new_products_price" size="14" value="<?php echo(number_format(($R_Product['specials_new_products_price']), 2, '.', '')); ?>"></td>
 </tr>
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo SPECIAL_EXPIRES; ?></b></td>
 <td width="80%"><input type="text" name="year" size="2" maxlength="4" value="<?php echo $year; ?>"><?php echo YEAR; ?>&nbsp;&nbsp;&nbsp;&nbsp;
				 <input type="text" name="month" size="2" maxlength="2" value="<?php echo $month; ?>"><?php echo MONTH; ?>&nbsp;&nbsp;&nbsp;&nbsp;
				 <input type="text" name="day" size="1" maxlength="2" value="<?php echo $day; ?>"><?php echo DAY; ?></td>
 </tr>
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo ENABLED; ?></b></td>
 <td width="80%"><input type="radio" name="status" size="15" value="1" <?php echo $enabled; ?> ><?php echo YES; ?>&nbsp;&nbsp;
				 <input type="radio" name="status" size="15" value="0" <?php echo $disabled; ?> ><?php echo NO; ?><br></td>
 </tr>

 <td width="100%" class="tdBlue" colspan="2">
 <table cellpadding="0" cellspacing="0" width="100%"><tr height="45px"><td class="tdBlue" width="60%" align="center"><br>
  <input type="hidden" name="<?php echo $update_or_insert; ?>" value="1">
  
  <a class="button" title="<?php echo UPDATE_SPECIAL_PRICE_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); document.EditSpecial.submit();"><span><?php echo UPDATE_SPECIAL_PRICE; ?></span></a>
  <a class="button" title="<?php echo BACK_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); window.location.href='product.php?ProductID=<?php echo($_REQUEST['ProductID']); ?>'"><span><?php echo BACK; ?></span></a>
  </td><td class="tdBlue" width="40%" align="right"><br>
    <a class="button" title="<?php echo DELETE_SPECIAL_PRICE_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); window.location.href='product_edit_sale.php?ProductID=<?php echo($_REQUEST['ProductID']); ?>&delete=1'"><span><?php echo DELETE_SPECIAL_PRICE; ?></span></a>
  </td></tr></table>
 </td>
 </tr>
 </form>
 </table>
 
 
  </td>
 </tr>
</table><br><br>

<center>
<?php
  if (isset($_POST['update'])) { 
	  if ($SUCCESS=true) {
		  echo '<br><b><font color="green">' . UPDATED_SUCCESS . '</b></font>';
	  } else {
		  echo '<br><b><font color="red">' . UPDATED_FAILURE . '</b></font>';
	  }
  }
?>
</center>
<?php include("includes/footer.php"); ?>
</body>
</html>
