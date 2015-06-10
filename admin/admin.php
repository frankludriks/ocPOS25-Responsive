<?php 

// admin/admin.php


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

/**
 * displayUsers - Displays the users database table in
 * a nicely formatted html table.
 */
function displayUsers() {
   global $database;
   $q = "SELECT username,userlevel,email,timestamp "
       ."FROM ".POS_USERS." ORDER BY userlevel DESC,username";
   $result = $database->query($q);
   /* Error occurred, return given name by default */
   $num_rows = mysql_numrows($result);
   if(!$result || ($num_rows < 0)){
      echo "Error displaying info";
      return;
   }
   if($num_rows == 0){
      echo "Database table empty";
      return;
   }
   /* Display table contents */
   echo "<table align=\"left\" border=\"1\" cellspacing=\"2\" cellpadding=\"5\">\n";
   echo "<tr><td><b>Username</b></td><td><b>Level</b></td><td><b>Email</b></td><td><b>Last Active</b></td></tr>\n";
   for($i=0; $i<$num_rows; $i++){
      $uname  = mysql_result($result,$i,"username");
      $ulevel_numerical = mysql_result($result,$i,"userlevel");
      $ulevel = ($ulevel_numerical == 9) ? 'Admin' : 'normal';
      $email  = mysql_result($result,$i,"email");
      $time   = date('Y-m-d H:i:s', mysql_result($result,$i,"timestamp"));

      echo '<tr><td><a href="admin_useredit.php?user=' . $uname . '">' . $uname . '</a></td><td>' . $ulevel. '</td><td>' . $email . '</td><td>' . $time . '</td></tr>' . "\r\n";
   }
   echo "</table><br>\n";
}
 

// MAIN PAGE

// if not logged in, redirect to login
if(!$session->logged_in) {
   header('Location: ../login.php');
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
    <title>User Account Administration</title>
    <link rel="Stylesheet" href="../css/style.css">
    <script LANGUAGE="JavaScript" type="text/javascript">
        <!--
        function confirmPost() {
            var agree=confirm("Are you sure you want to delete this user?");
            if (agree)
                return true ;
            else
                return false ;
        }
        
        function confirmDeleteUsers() {
            var days = document.DeleteUsers.inactdays.value;
            var agree=confirm("Are you sure you want to delete all users inactive in the past " + days + " days?");
            if (agree)
                return true ;
            else
                return false ;
        }
        
        function validatePwd() {
            // var invalid = " "; // Invalid character is a space (change to array to check for series of invalid characters?)
            var minLength = 4; // Minimum length -- save this value in database
            var pw1 = document.create_user.adduserpass.value;
            var pw2 = document.create_user.adduserpass_confirm.value;
            
            // check for a value in both fields.
            if (pw1 == '' || pw2 == '') {
                alert('Please enter the password into both password fields.');
                return false;
            }
            
            // check for minimum length
            if (document.create_user.password.value.length < minLength) {
                alert('Password must be at least ' + minLength + ' characters long. Please re-enter the passwords.');
                return false;
            }
            
            // check for spaces
            // if (document.create_user.password.value.indexOf(invalid) > -1) {
                // alert("Sorry, spaces are not allowed in passwords.");
                // return false;
            // }
            
            if (pw1 != pw2) {
                alert ("The passwords do not match each other. Please carefully re-enter the passwords.");
                return false;
            }
        }
//  End -->
</script>
</head>

<body>
<table cellspacing="2" width="100%"><tr>
    <td width="80%" align="left"><a href="index.php"><img src="../images/<?php echo APPLICATION_LOGO_IMAGE; ?>" height=62px alt="OllaCart Point of Sale" title="OllaCart Point of Sale" border="0"></a></td>
    <td align="right">
<?php  
    if($session->logged_in) {
        echo '<b>' . LOGGED_IN_AS . '  ' . $session->username . '</b><br>';
           if($session->isAdmin()) {
                echo  '<a href="../index.php">  ' . HOME . '</a>&nbsp;&nbsp;';
            } 
            echo '<a href="../process.php">  ' . LOGOUT . '</a>';
    } 
?>
    </td>
</tr></table>


<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><td style="background-color: #606060;" width="100%" height="1"></td></tr>
</table>

<h1>User Account Administration</h1>

<?php 
if($form->num_errors > 0){
   echo "<font size=\"4\" color=\"#ff0000\">"
       ."*** Error submitting form.  Please check all required fields.<br><br>";   
   echo"</font>";
}
?>
<table align="left" border="0" cellspacing="5" cellpadding="5">
<tr><td>

<h3>Users Table Contents:</h3>
<?php 
displayUsers();
?>
</td></tr>
<tr>
<td>
<br>

<h3>Update User Level</h3>
<form action="adminprocess.php" method="POST">
<?php  echo $form->error("upduser"); ?>
<table>
<tr><td>
Username:<br>
<input type="text" name="upduser" maxlength="30" value="<?php  echo $form->value("upduser"); ?>">
</td>
<td>
Level:<br>
<select name="updlevel">
<option value="1">normal
<option value="9">Admin
</select>
</td>
<td>
<br>
<input type="hidden" name="subupdlevel" value="1">
<input type="submit" value="Update Level">
</td></tr>
</table>
</form>
</td>
</tr>
<tr>
<td><hr></td>
</tr>


<tr>
<td>

<h3>Delete User</h3>
<?php  echo $form->error("deluser"); ?>
<form action="adminprocess.php" method="POST">
Username:<br>
<input type="text" name="deluser" maxlength="30" value="<?php  echo $form->value("deluser"); ?>">
<input type="hidden" name="subdeluser" value="1">
<input type="submit" value="Delete User" onclick="return confirmPost()">
</form>
</td>
</tr>
<tr>
<td><hr></td>
</tr>

<tr>
<td>

<h3>Create User</h3>
<form name="create_user" action="adminprocess.php" method="POST" onSubmit="return validatePwd()">
<table>
    <tr><td>Username</td><td><input type="text" name="adduser" maxlength="30" value="<?php  echo $form->value("adduser"); ?>">
        <?php  echo $form->error("adduser"); ?>(at least 4 characters)
    </td></tr>
    <tr><td>Password</td><td><input type="password" name="adduserpass" maxlength="30" value="<?php  echo $form->value("adduserpass"); ?>">
        <?php  echo $form->error("adduserpass"); ?>(at least 4 characters)
        </td></tr>
    <tr><td>Confirm Password</td><td><input type="password" name="adduserpass_confirm" maxlength="30" value="">
        </td></tr>
    <tr><td>Email</td><td><input type="text" name="adduseremail" maxlength="30" value="<?php  echo $form->value("adduseremail"); ?>">
        <?php  echo $form->error("adduseremail"); ?>
        </td></tr>
    <tr><td>User Level</td> <td> <select name="adduserlevel"><option value="1">normal<option value="9">Admin</select></td></tr>
</table>
<input type="hidden" name="subcreateuser" value="1">
<input type="submit" value="Create User">
</form>
</td>
</tr>
<tr>
<td><hr></td>
</tr>
<tr>
    <td>
        <h3>Delete Inactive Users</h3>
        This will delete all users (not administrators), who have not logged in to the site<br>
        within a certain time period. You specify the days spent inactive.<br><br>
        <table>
            <form name="DeleteUsers" action="adminprocess.php" method="POST">
            <tr>
                <td>
                Days:<br>
                    <select name="inactdays">
                        <option value="3">3
                        <option value="7">7
                        <option value="14">14
                        <option value="30">30
                        <option value="100">100
                        <option value="365">365
                    </select>
                </td>
                <td>
                    <br>
                    <input type="hidden" name="subdelinact" value="1">
                    <input type="submit" value="Delete All Inactive" onclick="return confirmDeleteUsers()">
                </td>
            </tr>
            </form>
        </table>
    </td>
</tr>
<tr>
<td><hr></td>
</tr>

</table>
<?php 
    }
}
?>

<?php /* include("../includes/footer.php"); */ ?>
</body>
</html>