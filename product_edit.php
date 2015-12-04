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
	<script src="ckeditor/ckeditor.js"></script>
</head>
<body>
  <div class="container">
    <?php include("includes/header.php"); ?>

<?php
  if (isset($_POST['update'])) { 
	  if ($SUCCESS=true) {
		  echo '<div class="alert alert-success text-center" role="alert"><h3>' . UPDATED_SUCCESS . '</h3></div>';
	  } else {
		  echo '<div class="alert alert-danger text-center" role="alert"><h3>' . UPDATED_FAILED . '</h3></div>';
	  }
  }
  
?>
<div class="alert alert-warning text-center" role="alert"><h3><?php echo PRODUCT_INFO; ?>"<?php echo($R_Product['products_name']); ?>"</h3></div>
 <table width="100%" align="center">
   <tr>
     <td>
	   <form name="ProductEdit" method="post" class="form-horizontal">
		  <div class="form-group">
			<label for="products_model" class="col-sm-2 control-label"><?php echo PRODUCT_MODEL; ?></label>
			<div class="col-sm-10">
			  <input  name="products_model" class="form-control" id="products_model" value="<?php echo($R_Product['products_model']); ?>" placeholder="<?php echo PRODUCT_MODEL; ?>">
			</div>
		  </div>
		  <div class="form-group">
			<label for="products_price" class="col-sm-2 control-label"><?php echo PRICE; ?></label>
			<div class="col-sm-10">
			  <input name="products_price" class="form-control" id="products_price" value="<?php echo(number_format(($R_Product['products_price']), 2, '.', '')); ?>" placeholder="<?php echo PRICE; ?>">
			</div>
		  </div>
		  <div class="form-group">
			<label for="products_quantity" class="col-sm-2 control-label"><?php echo QTY_IN_STOCK; ?></label>
			<div class="col-sm-10">
			  <input name="products_quantity" class="form-control" id="products_quantity" value="<?php echo($R_Product['products_quantity']); ?>" placeholder="<?php echo QTY_IN_STOCK; ?>">
			</div>
		  </div>
		  <div class="form-group">
			<label for="products_weight" class="col-sm-2 control-label"><?php echo WEIGHT; ?></label>
			<div class="col-sm-10">
			  <input name="products_weight" class="form-control" id="products_weight" value="<?php echo($R_Product['products_weight']); ?>" placeholder="<?php echo WEIGHT; ?>">
			</div>
		  </div>
		  <div class="form-group">
			<label for="products_description" class="col-sm-2 control-label"><?php echo PRODUCT_DESCRIPTION; ?></label>
			<div class="col-sm-10">
			  <textarea name="products_description" class="form-control ckeditor" id="products_description" placeholder="<?php echo PRODUCT_DESCRIPTION; ?>" rows="5"><?php echo($R_Product['products_description']); ?></textarea>
			</div>
		  </div>
		  <div class="form-group">
			<div class="col-sm-offset-4 col-sm-10">
			<input type="hidden" name="update" value="1">
			<a href="#" title="<?php echo UPDATE_PRODUCT_BUTTON_TITLE; ?>" onclick="this.blur(); document.ProductEdit.submit();" class="btn btn-success btn-default active" role="button"><?php echo UPDATE; ?></a>
			<a href="#" title="<?php echo BACK_BUTTON_TITLE; ?>" onclick="this.blur(); window.location.href='product.php?ProductID=<?php echo($R_Product['products_id']); ?>'" class="btn btn-danger btn-default active" role="button"><?php echo BACK; ?></a>
			
			</div>
		  </div>
		  <?php
		  //  Display attributes form elements
			if (use_attribs()) {
		?>	<div>
		<?php
			echo $R_all_attribs->config_form($R_product_attribs);
		?>
			</div>
		<?php	  
			} 
		?>
		</form>
    </td>
  </tr>
</table><br><br>
	<script>
       /*CKEDITOR.replace( 'products_description' );             
       // resize the editor after it has been fully initialized
       CKEDITOR.on('instanceLoaded', function(e) {e.editor.resize(700, 350)} );*/
    </script>
	  <footer class="footer">
        <?php include("includes/footer.php"); ?>
      </footer>
    </div> <!-- /container -->
</body>
</html>
