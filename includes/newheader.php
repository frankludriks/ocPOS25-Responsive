<?php 
// includes/header.php
// error level defined in include/functions.php

if (file_exists("includes/lang/$lang/includes/header.php")) {
	include("includes/lang/$lang/includes/header.php");
}

include 'functions_header.php';

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
} else {
    $logged_in = 1;
}

if (isset($_GET['Error'])) {
	echo '<table width="100%" bgcolor="red"><tr><td align="middle" width="100%"><font color="white" size="3"><b>' . $_GET['Error'] . '</b></font></td></tr></table>';
}

?>
<!-- header -->
			<!--top part start -->
				<div id="topDiv">
					<a href="index.php"><img src="images/<?php echo APPLICATION_LOGO_IMAGE; ?>" alt="" width="368" height="113" /></a>
				
<?php
    if (!strstr($_SERVER["PHP_SELF"], 'login.php') && !strstr($_SERVER["PHP_SELF"], 'forgotpass.php')) {

        if($session->logged_in) {
            echo '<b>' . LOGGED_IN_AS . '  ' . $session->username . '</b>&nbsp;&nbsp;&nbsp;&nbsp;';
            echo  '<a href="useredit.php">  ' . USER_PANEL . '</a>&nbsp;&nbsp;';
               if($session->isAdmin()) {
                    echo  '<a href="admin/admin.php">  ' . ADMIN_PANEL . '</a>&nbsp;&nbsp;';
                }
                echo '<a href="process.php">  ' . LOGOUT . '</a>&nbsp;&nbsp;';
                } else {
            echo '<a href="login.php">  ' . LOGIN . '</a>&nbsp;&nbsp;';
        }
    }
?>


                  <form name="ProductHeaderSearch" action="product_search.php" method="get">
                   &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                   <b><?php echo PRODUCT; ?></b>
                   <input type="text" name="Query" size="10" value="<?php echo($PRODUCT_SEARCH); ?>">
                   <a class="headerbutton" title="<?php echo SEARCH_PROD_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.ProductHeaderSearch.submit();"><span><?php echo SEARCH_BUTTON_TEXT; ?></span></a>

                  </form>

                  <form name="Orders">

                   <a class="button" title="<?php echo NEW_ORDER_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); window.location.href='action.php?Action=NewOrder'"><span><?php echo NEW_ORDER; ?></span></a>
                   <a class="button" title="<?php echo RETURN_EXCHANGE_BUTTON_TITLE; ?>" style="color: #993333" href="#" onclick="this.blur(); window.location.href='action.php?Action=ReturnOrder'"><span><?php echo RETURN_EXCHANGE; ?></span><input type="hidden" value="<?php echo RETURN_EXCHANGE; ?>"></a>
                   
                  </form>

                </div>
			<!--top part end -->

<!-- end header -->