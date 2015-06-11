<?php 
// login.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);
include("includes/custom_header.php");


/**
 * User has already logged in, so display relavent links, including
 * a link to the admin center if the user is an administrator.
 */
if($session->logged_in) {
   header('Location: index.php');
} else {

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

    <title>Login</title>

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
<body onload="document.Login.user.focus()";>

<?php /* include("includes/header.php");  */?>

<?php 
/**
 * User not logged in, display the login form.
 * If user has already tried to login, but errors were
 * found, display the total number of errors.
 * If errors occurred, they will be displayed.
 */
?>
<div class="container">
 <div class="col-md-8 center-block">
 <?php if($form->num_errors > 0) echo '<div class="alert alert-danger" role="alert">Login Error. ' . $form->error("pass") . $form->error("user") . '.  Please try again.</div>'; ?>
   <div class="panel panel-default">
	  <div class="panel-heading">
		<h3 class="panel-title">Login</h3>
	  </div>
	  <div class="panel-body">
		<form action="process.php" method="POST" name="Login" class="form-horizontal">
		  <div class="form-group">
			<label for="user" class="col-sm-2 control-label"><?php echo USERNAME; ?></label>
			<div class="col-sm-10">
			  <input type="email" name="user" value="<?php echo $form->value("user"); ?>" class="form-control" id="user" placeholder="Username">
			</div>
		  </div>
		  <div class="form-group">
			<label for="pass" class="col-sm-2 control-label">Password</label>
			<div class="col-sm-10">
			  <input type="password" name="pass" class="form-control" value="<?php echo $form->value("pass"); ?>" id="pass" placeholder="Password">
			</div>
			<input type="hidden" name="sublogin" value="1">
		  </div>
		  <div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
			<a href="#" class="btn btn-success" role="button" onclick="this.blur(); document.Login.submit();"><?php echo LOGIN_BUTTON_TEXT; ?></a>
			<a href="forgotpass.php" class="btn btn-default" role="button"><?php echo FORGOT_PASSWORD; ?></a>  
			</div>
		  </div>
		</form>
	  </div>
  </div>
 </div>

<?php 
}
?>
    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
	<!-- include jquery and bootstrap -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="bootstrap-3.3.4/js/bootstrap.min.js"></script>
  </body>
</html>