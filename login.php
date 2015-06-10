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

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
   <title><?php echo($POSName) . ': ' . TITLE; ?></title>
   <link rel="Stylesheet" href="css/style.css">
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
<form action="process.php" method="POST" name=Login>
<center><br><br>
<table align="middle" border="1" cellspacing="0" cellpadding="3" width="500px">
    <tr><td class="tdBlue" align="middle">
    <h1><?php echo LOGIN; ?></h1>
<?php if($form->num_errors > 0) echo('Login Error.  Please try again.'); ?>
    </td></tr>
    <tr height="150px"><td width="50%" align="middle">
        Username:<input type="text" name="user" maxlength="30" value="<?php echo $form->value("user"); ?>" onkeydown="if (event.keyCode == 13) document.Login.submit();">
        <br><br>
        Password:<input type="password" name="pass" maxlength="30" value="<?php echo $form->value("pass"); ?>" onkeydown="if (event.keyCode == 13) document.Login.submit();">
        <br><br>
        <?php  echo $form->error("user"); ?>
        <br>
        <?php echo $form->error("pass"); ?>
        <br><br>
<?php 
/* <tr><td colspan="2" align="left"><input type="checkbox" name="remember" <?php  if($form->value("remember") != ""){ echo "checked"; } ?>>
<font size="2">Remember me next time &nbsp;&nbsp;&nbsp;&nbsp;
*/
?>
        <input type="hidden" name="sublogin" value="1">
        <a class="button" title="<?php echo LOGIN; ?>" href="#"  onclick="this.blur(); document.Login.submit();"><span><?php echo LOGIN_BUTTON_TEXT; ?></span></a>
        <font size="2">&nbsp;&nbsp;<a href="forgotpass.php"><?php echo FORGOT_PASSWORD; ?></a></font>
    </td></tr>
</table>
</form>

<?php 
}
?>


<?php include("includes/footer.php"); ?>
</body>
</html>
