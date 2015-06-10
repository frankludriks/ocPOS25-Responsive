<?php 
// includes/footer.php


if (file_exists("includes/lang/$lang/includes/footer.php")) {
	include("includes/lang/$lang/includes/footer.php");
}

?>

<br><br>
<table width="100%" height="1" border="0" cellpadding="0" cellspacing="0">
<tr><td style="background-color: #606060;" width="100%"></td></tr>
</table>

<table width="100%" border="0" cellpadding="2" cellspacing="0">
 <tr>
  <td class="tdBlue" width="30%" align="left">
   &nbsp;&nbsp;<a target="_blank" href="doc/index.php"><?php echo DOCUMENTATION; ?></a>
  </td>
  <td class="tdBlue" width="70%" align="right">
   <b><?php echo($StoreName) . ' ' . POWERED_BY; ?><?php echo APPLICATION_NAME . ' ' . APPLICATION_VERSION; ?></a>&nbsp;&nbsp;</b>
  </td>
 </tr>
</table>

<!-- <table width="100%" height="1" border="0" cellpadding="0" cellspacing="0">
<tr><td style="background-color: #606060;" width="100%"></td></tr>
<tr><td align="right"><a href="http://www.webcart-consulting.com" target="_blank">www.webcart-consulting.com</a>&nbsp;&nbsp;</td></tr>
</table>
-->
