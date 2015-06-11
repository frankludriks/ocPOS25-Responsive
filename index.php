<?php 
// index.php


if (file_exists("install.php")) {
    header("Location: install.php");
    die();
}

include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);
// if not logged in, redirect to login
if(!$session->logged_in) {
   header('Location: login.php');
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
  </head>

  <body<?php if($_SESSION['CurrentOrderIndex'] != -1){ echo(" onload=\"document.AddProductOrder.ProductQuery.focus();\""); } ?>>

    <div class="container">
      
		<?php include("includes/header.php"); ?>
      <div class="row marketing">
        <?php
			if($_SESSION['CurrentOrderIndex'] != -1){
				$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->PrintFull(false);
			}else{
		  ?>
		  <div>
		    <div>
			  <?php 
                if (isset($_GET['error']) && $_GET['error']=='no_product_found') {
                    echo '<div class="alert alert-danger" role="alert">' . PRODUCT_NOT_FOUND . '</div>';
                } elseif (isset($_GET['error'])) {
                    echo '<div class="alert alert-danger" role="alert">' . $_GET['error'] . '</div>';
                } else {
                    echo '<div class="alert alert-info" role="alert">' . NO_ORDER_SELECTED . '</div>'; 
                }
              ?>
			</div>
		  </div>
		  <?php 
		  } 
		  ?>
      </div>

      <footer class="footer">
        <?php include("includes/footer.php"); ?>
      </footer>

    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
	<!-- include jquery and bootstrap -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="bootstrap-3.3.4/js/bootstrap.min.js"></script>
  </body>
</html>