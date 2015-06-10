<?php 

// useredit.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
   <title>Manage Account</title>
   <link rel="Stylesheet" href="css/style.css">
</head>
<body>

<?php include("includes/header.php"); ?>
<center>
<?php 
/**
 * User has submitted form without errors and user's
 * account has been edited successfully.
 */
if(isset($_SESSION['useredit'])) {
    unset($_SESSION['useredit']);

    echo "<h1>User Account Edit Success!</h1>";
    echo "<p><b>$session->username</b>, your account has been successfully updated. ";
} else {
?>

<?php 
    /**
     * If user is not logged in, then do not display anything.
     * If user is logged in, then display the form to edit
     * account information, with the current email address
     * already in the field.
     */
    if($session->logged_in) {
?>

<h1>User Account Edit : <?php  echo $session->username; ?></h1>
<?php 
        if($form->num_errors > 0) {
           echo "<font size=\"2\" color=\"#ff0000\">".$form->num_errors." error(s) found</font>";
        }
?>
<form action="process.php" method="POST" name="UserEdit">
<table align="middle" border="0" cellspacing="0" cellpadding="3">
<tr>
<td>Current Password:</td>
<td><input type="password" name="curpass" maxlength="30" value="<?php echo $form->value("curpass"); ?>"></td>
<td><?php  echo $form->error("curpass"); ?></td>
</tr>
<tr>
<td>New Password:</td>
<td><input type="password" name="newpass" maxlength="30" value="<?php  echo $form->value("newpass"); ?>" onkeydown="if (event.keyCode == 13) document.UserEdit.submit();"></td>
<td><?php  echo $form->error("newpass"); ?></td>
</tr>
<tr>
<td>Email:</td>
<td><input type="text" name="email" maxlength="50" value="
<?php 
        if($form->value("email") == "") {
           echo $session->userinfo['email'];
        } else {
           echo $form->value("email");
        }
?>" onkeydown="if (event.keyCode == 13) document.UserEdit.submit();">
</td>
<td><?php  echo $form->error("email"); ?></td>
</tr>
<tr><td colspan="2" align="right">
<input type="hidden" name="subedit" value="1">
<a class="button" title="Edit Account" href="#"  onclick="this.blur(); document.UserEdit.submit();"><span>Submit</span></a>
</td></tr>

</table>
</form>

<?php 
    }
}

?>

<?php include("includes/footer.php"); ?>
</body>
</html>
