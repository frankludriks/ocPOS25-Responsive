<?php 
// forgotpass.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);
include("includes/custom_header.php");

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
   <title><?php echo TITLE; ?></title>
   <link rel="Stylesheet" href="css/style.css">
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
      echo "<h1>New Password Generated</h1>";
      echo "<p>Your new password has been generated "
          ."and sent to the email <br>associated with your account.</p>";
   }
   /**
    * Email could not be sent, therefore password was not
    * edited in the database.
    */
   else {
      echo "<h1>New Password Failure</h1>";
      echo "<p>There was an error sending you the "
          ."email with the new password,<br> so your password has not been changed.</p>";
   }
       
   unset($_SESSION['forgotpass']);
} else {

/**
 * Forgot password form is displayed, if error found
 * it is displayed.
 */
?>
<br><br>
<form action="process.php" method="POST" name="Forgot">
<table align="middle" border="1" cellspacing="0" cellpadding="3" width="500px">
    <tr><td class="tdBlue" align="middle">
<h1><?php echo FORGOT_PASSWORD; ?></h1>
<?php echo FORGOT_PASSWORD_MESSAGE; ?><br><br>
<?php if($form->num_errors > 0) echo LOGIN_ERROR; ?>
    </td></tr>
    <tr height="150px"><td width="50%" align="middle">
        <?php echo USERNAME; ?><input type="text" name="user" maxlength="30" value="<?php echo $form->value("user"); ?>">
        <br><br>
<?php  echo $form->error("user"); ?>
        <br><br>
<input type="hidden" name="subforgot" value="1">
        <a class="button" title="<?php echo SUBMIT_BUTTON_TITLE; ?>" href="#"  onclick="this.blur(); document.Forgot.submit();"><span><?php echo SUBMIT; ?></span></a>
    </td></tr>
</table>

<?php 
}
?>

<?php include("includes/footer.php"); ?>
</body>
</html>
