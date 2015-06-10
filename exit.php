<?php
// exit.php



include("includes/functions.php");
include("includes/session.php");
// ORDER VARIABLES
unset($_SESSION['CurrentOrderIndex']);
unset($_SESSION['NextOrderIndex']);
unset($_SESSION['Orders']);
?>
<script language="JavaScript">
window.close();
</script>
