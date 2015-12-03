<?php
// comments_add.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}


if(isset($_POST['Comments'])) {  //post comments, then close pop-up and refresh parent page
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Comments = $_POST['Comments'];
	$ONLOAD = " onload='opener.window.location.reload(); self.close();'";
}else{
	$ONLOAD = " onload='document.AddComments.Comments.focus();'";
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
<body<?php echo($ONLOAD); ?>>
  <div class="container">
    <div class="text-center"><h4><?php echo ORDER_COMMENTS; ?></h4></div>
    <form class="form-horizontal" name="AddComments" method="post">
	  <div class="form-group">
		<div class="col-sm-10">
		  <textarea  name="<?php echo COMMENTS; ?>" class="form-control" rows="3" placeholder="Comments for this order"><?php echo($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Comments); ?></textarea>
		</div>
	  </div>
	  <div class="text-center">
	    <a href="#" class="btn btn-success btn-sm" role="button" onclick="this.blur(); document.AddComments.submit();"><?php echo UPDATE_COMMENTS; ?></a>
		<a href="#" class="btn btn-default btn-sm" role="button" onclick="this.blur();window.close();"><?php echo CANCEL; ?></a>
	  </div>
	</form>

    </div> <!-- /container -->
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
	<!-- include jquery and bootstrap -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="bootstrap-3.3.4/js/bootstrap.min.js"></script>
  </body>
</html>
