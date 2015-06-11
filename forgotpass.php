<?php 
// forgotpass.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);
include("includes/custom_header.php");

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

    <title>Forgot Password</title>

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
<body>
<center>

<?php /* include("includes/header.php"); */ ?>

<?php 
/**
 * Forgot Password form has been submitted and no errors
 * were found with the form (the username is in the database)
 */
if(isset($_SESSION['forgotpass'])) {
   /**
    * New password was generated for user and sent to user's
    * email address.
    */
   if($_SESSION['forgotpass']) {
      echo '<div class="alert alert-success" role="alert"><h1>New Password Generated</h1>';
      echo '<p>Your new password has been generated '
          .'and sent to the email <br>associated with your account.</p></div>';
   }
   /**
    * Email could not be sent, therefore password was not
    * edited in the database.
    */
   else {
      echo '<div class="alert alert-danger" role="alert"><h1>New Password Failure</h1>';
      echo '<p>There was an error sending you the '
          .'email with the new password,<br> so your password has not been changed.</p></div>';
   }
       
   unset($_SESSION['forgotpass']);
} else {

/**
 * Forgot password form is displayed, if error found
 * it is displayed.
 */
?>


<div class="container">
 <div class="col-md-10 center-block">
   <?php if($form->num_errors > 0) echo '<div class="alert alert-danger" role="alert">' . LOGIN_ERROR . ' ' . $form->error("user") . '</div>'; ?>
   <div class="panel panel-default">
	  <div class="panel-heading">
		<h3 class="panel-title"><?php echo FORGOT_PASSWORD; ?></h3>
	  </div>
	  <div class="panel-body">
		<form action="process.php" method="POST" name="Forgot" class="form-horizontal">
		<div class="text-center">
			<?php echo FORGOT_PASSWORD_MESSAGE; ?>
			</div>
		  <div class="form-group">
			<label for="user" class="col-sm-2 control-label"><?php echo USERNAME; ?></label>
			<div class="col-sm-8">
			  <input type="text" name="user" value="<?php echo $form->value("user"); ?>" class="form-control" id="user" placeholder="Username">
			</div>
		  </div>
		  <input type="hidden" name="subforgot" value="1">
		  <div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
			<a href="#" class="btn btn-success" role="button" onclick="this.blur(); document.Forgot.submit();"><?php echo SUBMIT; ?></a>
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
