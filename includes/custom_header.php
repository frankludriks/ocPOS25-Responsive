<?php 
// includes/custom_header.php

/* This file is designed for your store logo or whatever else you'd like at the top of the POS page.
   This is your custom POS header file.
*/
?>
<table cellspacing="2" width="100%"><tr>
    <td width="80%" align="left"><a href="index.php"><img src="images/<?php echo APPLICATION_LOGO_IMAGE; ?>" height=62px alt="OllaCart Point of Sale" title="OllaCart Point of Sale" border="0"></a></td>
    <td align="right">
<?php
    if (!strstr($_SERVER["PHP_SELF"], 'login.php') && !strstr($_SERVER["PHP_SELF"], 'forgotpass.php')) {

        if($session->logged_in) {
            echo '<b>' . LOGGED_IN_AS . '  ' . $session->username . '</b><br>';
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
    </td>
</tr></table>


<table width="100%" height="1" border="0" cellpadding="0" cellspacing="0">
<tr><td style="background-color: #606060;" width="100%" height="1"></td></tr>
</table>
