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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title><?php echo($POSName) . ': ' . TITLE; ?></title>
       <link rel="Stylesheet" href="css/style.css">
       <script language="JavaScript" src="javascript.js" type="text/javascript"></script>
</head>
<body<?php echo($ONLOAD); ?>>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
 <table class="tableBorder" border="0" width="100%" cellpadding="2" cellspacing="1" align="center">
 <form name="AddComments" method="post">
 <tr>
 <td width="100%" class="tdBlue" align="center">
  <b><?php echo ORDER_COMMENTS; ?></b>
 </td>
 </tr>
 <tr>
 <td width="100%" align="center">
  <textarea name="<?php echo COMMENTS; ?>" cols="40" rows="5"><?php echo($_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->Comments); ?></textarea>
 </td>
 </tr>
 <tr height="45px">
   <td width="100%" class="tdBlue" align="center"><br>
      <a class="button" title="<?php echo UPDATE_COMMENTS_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.AddComments.submit();"><span><?php echo UPDATE_COMMENTS; ?></span></a>
      <a class="button" title="<?php echo CANCEL_BUTTON_TITLE; ?>" href="#" onclick="this.blur();window.close();"><span><?php echo CANCEL; ?></span></a>
   </td>
 </tr>
 </form>
 </table>
 
 
  </td>
 </tr>
</table>

</body>
</html>
