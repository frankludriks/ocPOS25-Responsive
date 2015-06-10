<?php
// product_noninventory.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}

if($_SESSION['CurrentOrderIndex'] == -1){
	header("Location: index.php");
	exit();
}

$ReqVars = Array (
'Product Name' => $_POST['products_name'],
'Price' => $_POST['products_price'],
'Quantity' => $_POST['products_quantity']
);

$dummy_product_id = time();

if(TestFormInput(false,$ReqVars)){
    // add item
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->AddItem($dummy_product_id, $_POST['products_quantity']);
	header("Location: index.php");
	exit();
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
<body onload="document.ProductAdd.products_name.focus();">
 
<?php include("includes/header.php"); ?>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <form name="ProductAdd" method="post">
 <input type="hidden" name="posted" value="true">
 <tr>
 <td width="100%" class="tdBlue" colspan="2" align="center">
  <b><?php echo ADD_PRODUCT; ?></b>
 </td>
 </tr>
 <?php if($_POST['posted'] && !$SUCCESS){ ?>
 <tr>
 <td width="100%" colspan="5">
  <?php TestFormInput($_POST['posted'],$ReqVars); ?>
 </td>
 </tr>
 <?php } ?>
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo PRODUCT_NAME; ?></b></td>
 <td width="80%"><input type="text" name="products_name" size="50" maxlength="255" value="<?php echo($_POST['products_name']); ?>"><?php echo REQUIRED; ?></td>
 </tr>
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo PRODUCT_MODEL; ?></b></td>
 <td width="80%"><input type="text" name="products_model" size="15" value="<?php echo($_POST['products_model']); ?>"></td>
 </tr>
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo PRICE; ?></b></td>
 <td width="80%"><?php echo $default_currency_symbol; ?><input type="text" name="products_price" size="14" value="<?php echo(number_format(($_POST['products_price']), 2, '.', '')); ?>"><?php echo REQUIRED; ?></td>
 </tr>
 <tr>
 <td width="20%" class="tdBlue"><b><?php echo PRODUCT_QTY; ?></b></td>
 <td width="80%">
 <input type="text" name="products_quantity" size="4" maxlength="4" value="<?php echo($_POST['products_quantity']); ?>" onkeydown="if (event.keyCode == 13) document.ProductAdd.submit();">
 <?php echo NOTE; ?><?php echo REQUIRED; ?>
 </td>
 </tr>
 <?php
    $tax_array = GetTax($dummy_product_id);
    // echo('tax array: <br>');
    // print_r($tax_array); die();
    $numrows = count($tax_array);
    
    if ($numrows > 0) {
        for ($i = 0; $i < $numrows; $i++) {
        $tax_description = $tax_array[$i]['tax_description'];
        $tax_rate = number_format($tax_array[$i]['tax_rate'],2,'.','');
        $tax_priority = $tax_array[$i]['tax_priority'];
?>
 <tr>
    <td width="20%" class="tdBlue"><b><?php echo $tax_array[$i]['tax_description']; ?></b></td>
    <td width="80%">
        <input type="hidden" name="tax_description<?php echo $i; ?>" value="<?php echo $tax_array[$i]['tax_description']; ?>">
        <input type="hidden" name="tax_priority<?php echo $i; ?>" value="<?php echo $tax_array[$i]['tax_priority']; ?>">
        <select name="tax_rate<?php echo $i; ?>">
            <option value="<?php echo $tax_rate; ?>"><?php echo $tax_rate; ?>%</option>
            <option value="0.00">0.00%</option>
        </select>
    </td>
</tr>
<?php
        }
    }   
 ?>

 <tr height="45px">
   <td width="100%" class="tdBlue" colspan="2" align="center"><br>
      <a class="button" title="<?php echo ADD_TO_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.ProductAdd.submit();"><span><?php echo ADD_TO_ORDER; ?></span></a>
      <a class="button" title="<?php echo BACK_BUTTON_TITLE; ?>" href="#" onclick="this.blur();window.location.href='index.php';"><span><?php echo BACK; ?></span></a>
   </td>
 </tr>
 </form>
 </table>
 
 
  </td>
 </tr>
</table>


<?php include("includes/footer.php"); ?>
</body>
</html>
