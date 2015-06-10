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

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <title><?php echo($POSName) . ': ' . TITLE; ?></title>
    <link rel="Stylesheet" href="css/style.css">
    <script language="JavaScript" src="javascript.js" type="text/javascript"></script>
</head>
<body<?php if($_SESSION['CurrentOrderIndex'] != -1){ echo(" onload=\"document.AddProductOrder.ProductQuery.focus();\""); } ?>>

<?php 
    // HEADER BARS
    include("includes/header.php"); 
?>

<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
<?php
if($_SESSION['CurrentOrderIndex'] != -1){
	$_SESSION['Orders'][$_SESSION['CurrentOrderIndex']]->PrintFull(false);
}else{
?>
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <tr>
 <td width="100%" class="tdBlue" align="center">
                <b>
            <?php 
                if (isset($_GET['error']) && $_GET['error']=='no_product_found') {
                    echo '<font color="red">' . PRODUCT_NOT_FOUND . '</font>';
                } elseif (isset($_GET['error'])) {
                    echo '<font color="red">' . $_GET['error'] . '</font>';
                } else {
                    echo NO_ORDER_SELECTED; 
                }
            ?>
                </b>
 </td>
 </tr>
 </table>
<?php } ?>
 
 
  </td>
 </tr>
</table>

<?php include("includes/footer.php"); ?>
</body>
</html>
