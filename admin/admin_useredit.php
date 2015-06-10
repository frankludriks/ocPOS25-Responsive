<?php 

// admin/user_edit.php

include("../includes/db.php");
include("../includes/functions_values.php");
include("../includes/session.php");

if (file_exists("../includes/lang/$lang/includes/header.php")) {
	include("../includes/lang/$lang/includes/header.php");
}
if (file_exists("../includes/lang/$lang/includes/footer.php")) {
	include("../includes/lang/$lang/includes/footer.php");
}

if (file_exists("../includes/lang/$lang/common.php")) {
	include("../includes/lang/$lang/common.php");
}

$user = $_GET['user'];
$userinfo  = $database->getUserInfo($user);

if (isset($_POST['useredit_process']) && $_POST['useredit_process'] == 1) {
    // echo('<pre>$userinfo: '); print_r($userinfo);

    if(isset($_POST['newpass']) && strlen($_POST['newpass']) > 3) {
        $pass_retval = $database->updateUserField($user,"password",md5($_POST['newpass']));
        if (!$pass_retval) {
            $pass_error = '<span class="error">Error setting new password.  Please verify and try again</span>';
        } else {
            $pass_error = '<span class="success">Password successfully changed.</span>';
        }
    }

    /* Change Email */
    if(isset($_POST['newemail']) && $_POST['newemail'] != $userinfo['email']) {
        $email_retval = $database->updateUserField($user,"email",$_POST['newemail']);
        if (!$email_retval) {
            $email_error = '<span class="error">Error setting new email address.  Please verify and try again</span>';
        } else {
            $email_error = '<span class="success">Email address successfully changed.</span>';
        }
    }
    $userinfo  = $database->getUserInfo($user);
    
    // echo('<pre>user info: '); print_r($userinfo);
    // echo("</pre><br><br>");
    // echo('email_error: ' . $email_error . '<br><br>');
    // echo('pass_error: ' . $pass_error . '<br><br>');
}
if($session->logged_in) {
    $logged_in_message = '<b>' . LOGGED_IN_AS . '  ' . $session->username . '</b><br>';
       if($session->isAdmin()) {
            $logged_in_message .= '<a href="../index.php">  ' . HOME . '</a>&nbsp;&nbsp;';
        } 
        $logged_in_message .= '<a href="../process.php">  ' . LOGOUT . '</a>';
} 

// if not logged in, redirect to login
if(!$session->logged_in) {
   header('Location: login.php');
} else {
 
    /**
     * User not an administrator, redirect to main page
     * automatically.
     */
    if(!$session->isAdmin()) {
       header("Location: ../main.php");
    } else {
    /**
     * Administrator is viewing page, so display all
     * forms.
     */
     
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title>Edit User: <?php echo $user; ?></title>
    <link rel="Stylesheet" href="../css/style.css">
    <script LANGUAGE="JavaScript">
        <!--
        function validatePwd() {
            var pw1 = document.UserEdit.newpass.value;
            var pw2 = document.UserEdit.newpass_confirm.value;
            var minLength = 4; // Minimum length -- save this value in database
            
            // if password is set, check for a value in both fields.
            if (pw1 != '') {
                if (pw1 == '' || pw2 == '') {
                    alert('Please enter the password into both password fields.');
                    return false;
                }
            
                // check for minimum length
                if (pw2.length < minLength) {
                    alert('Password must be at least ' + minLength + ' characters long. Please re-enter the passwords.');
                    return false;
                }
                
                if (pw1 != pw2) {
                    alert ("The passwords do not match each other. Please carefully re-enter the passwords.");
                    return false;
                }
            }
        }
    //  End -->
    </script>
</head>

<body>
<table cellspacing="2" width="100%"><tr>
    <td width="80%" align="left"><a href="index.php"><img src="../images/<?php echo APPLICATION_LOGO_IMAGE; ?>" height=62px alt="OllaCart Point of Sale" title="OllaCart Point of Sale" border="0"></a></td>
    <td align="right"><?php echo $logged_in_message; ?></td>
</tr></table>


<table width="100%" height="1" border="0" cellpadding="0" cellspacing="0">
<tr><td style="background-color: #606060;" width="100%" height="1"></td></tr>
</table>

<h1>Editing User: <?php echo $user; ?></h1>

<table align="left" border="0" cellspacing="5" cellpadding="5">
    <tr>
        <td><br><form method="POST" name="UserEdit" onsubmit="return validatePwd()">
           <table align="middle" border="0" cellspacing="0" cellpadding="3">
            <tr>
                <td>New Password:</td>
                <td><input type="password" name="newpass" size="30" maxlength="30" value="">(at least 4 characters)</td>
                <td>&nbsp;&nbsp;<?php echo $pass_error; ?></td>
            </tr>
            <tr>
                <td>Confirm Password:</td>
                <td><input type="password" name="newpass_confirm" size="30" maxlength="30" value=""></td>
                <td>&nbsp;&nbsp;</td>
            </tr>
            <tr>
                <td>New Email:</td>
                <td><input type="text" name="newemail" size="30" maxlength="50" value="<?php echo $userinfo['email']; ?>">(at least 4 characters)</td>
                <td>&nbsp;&nbsp;<?php echo $email_error; ?></td>
            </tr>
            <tr>
                <td colspan="2" align="left">
                    <input type="button" value="Back" onclick="history.go(-1)" >
                </td>
                <td align="right">
                    <input type="hidden" name="useredit_process" value="1">
                    <input type="submit" value="Submit" onclick="return validatePwd();" >
                </td>
            </tr>

          </table>
        </form>
        </td>
    </tr>
</table>

</body>
</html>
<?php 
    }
}
?>

<?php include("../includes/footer.php"); ?>
</body>
</html>