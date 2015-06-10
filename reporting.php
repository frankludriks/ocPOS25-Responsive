<?php
// reporting.php


include("includes/db.php");
include("includes/functions.php");
include("includes/session.php");

LoadLangFiles($lang);

// check if valid login session, else require login
$session->logged_in = $session->checkLogin();

if(!$session->logged_in) {
    header('Location: login.php');
}

$_REQUEST['CompareType'] = "Day";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
       <title><?php echo($POSName) . ': ' . TITLE; ?></title>
       <link rel="Stylesheet" href="css/style.css">
       <script language="JavaScript" src="javascript.js" type="text/javascript"></script>
       <meta http-equiv="Content-Type" content="text/html; charset=us-ascii">
	   <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
</head>
<body>
<?php
// starting defaults
$_REQUEST['Start_Month'] = date("m",time()-604800);
$_REQUEST['Start_Day'] = date("d",time()-604800);
$_REQUEST['Start_Year'] = date("Y",time()-604800);

$_REQUEST['End_Month'] = date("m");
$_REQUEST['End_Day'] = date("d");
$_REQUEST['End_Year'] = date("Y");
$_REQUEST['End_Hour'] = date("H");
$_REQUEST['End_Min'] = intval(date("i"));

$_REQUEST['Day1_Month'] = date("m",time()-604800);
$_REQUEST['Day1_Day'] = date("d",time()-604800);
$_REQUEST['Day1_Year'] = date("Y",time()-604800);

$_REQUEST['Day2_Month'] = date("m");
$_REQUEST['Day2_Day'] = date("d");
$_REQUEST['Day2_Year'] = date("Y");

$_REQUEST['Month1_Month'] = date("m",mktime(1,0,0,date("m")-1,date("d"),date("Y")));
$_REQUEST['Month1_Year'] = date("Y",mktime(1,0,0,date("m")-1,date("d"),date("Y")));

$_REQUEST['Month2_Month'] = date("m");
$_REQUEST['Month2_Year'] = date("Y");

$_REQUEST['Year1_Year'] = date("Y")-1;
$_REQUEST['Year2_Year'] = date("Y");

?>

<?php include("includes/header.php"); ?>

<?php include("includes/report_select.php"); ?><br>

<?php include("includes/report_compare_select.php"); ?><br>

<?php include("includes/report_days_select.php"); ?><br>

<?php include("includes/report_hours_select.php"); ?><br>
 
  </td>
 </tr>
</table>


<?php include("includes/footer.php"); ?>
</body>
</html>
