<?php
// includes/report_hours_select.php


if (file_exists("includes/lang/$lang/includes/report_hours_select.php")) {
	include("includes/lang/$lang/includes/report_hours_select.php");
}


// Year Limit
$YearLimit = date("Y");
$YearLimit++;
?>
<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <form name="HoursReport" action="report_hours.php" method="get">
 <tr>
 <td width="100%" colspan="3" class="tdBlue" align="center">
  <b><?php echo HOURS_REPORT; ?></b>
 </td>
 </tr>
 <tr>
 <td width="30%" class="tdBlue">
 <b><?php echo TIMEFRAME; ?></b>
 </td>
 <td width="35%">
 <select name="Day1_Month"><?php for($i=1; $i<13; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['Day1_Month']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select> /
 <select name="Day1_Day"><?php for($i=1; $i<32; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['Day1_Day']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select> /
 <select name="Day1_Year"><?php for($i=2004; $i<$YearLimit; $i++){  if($_REQUEST['Day1_Year']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select>
 <?php echo '        ' . TO; ?>
 </td>
 <td width="35%">
 <select name="Day2_Month"><?php for($i=1; $i<13; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['Day2_Month']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select> /
 <select name="Day2_Day"><?php for($i=1; $i<32; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['Day2_Day']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select> /
 <select name="Day2_Year"><?php for($i=2004; $i<$YearLimit; $i++){  if($_REQUEST['Day2_Year']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select>
 </td>
 </tr>
 
 <tr>
    <td class="tdBlue"><b><?php echo DAY_OF_WEEK; ?></b></td>
    <td><select name="weekday">
        <option value="Any Day"><?php echo ANY_DAY; ?></option>
        <option value="<?php echo SUN; ?>"<?php $selected = ''; if ($_REQUEST['weekday'] == SUN) $selected = " selected"; echo $selected; ?>><?php echo SUN; ?></option>
        <option value="<?php echo MON; ?>"<?php $selected = ''; if ($_REQUEST['weekday'] == MON) $selected = " selected"; echo $selected; ?>><?php echo MON; ?></option>
        <option value="<?php echo TUE; ?>"<?php $selected = ''; if ($_REQUEST['weekday'] == TUE) $selected = " selected"; echo $selected; ?>><?php echo TUE; ?></option>
        <option value="<?php echo WED; ?>"<?php $selected = ''; if ($_REQUEST['weekday'] == WED) $selected = " selected"; echo $selected; ?>><?php echo WED; ?></option>
        <option value="<?php echo THU; ?>"<?php $selected = ''; if ($_REQUEST['weekday'] == THU) $selected = " selected"; echo $selected; ?>><?php echo THU; ?></option>
        <option value="<?php echo FRI; ?>"<?php $selected = ''; if ($_REQUEST['weekday'] == FRI) $selected = " selected"; echo $selected; ?>><?php echo FRI; ?></option>
        <option value="<?php echo SAT; ?>"<?php $selected = ''; if ($_REQUEST['weekday'] == SAT) $selected = " selected"; echo $selected; ?>><?php echo SAT; ?></option>  
 </tr> 
 
 <tr height="45px">
 <td width="100%" colspan="3" class="tdBlue" align="center">
  <?php $checked = '';
	if ($_GET['posonly'] == 1) {
	$checked = 'checked';
	}  
?>
  <input type="checkbox" name="posonly" value="1" <?php echo($checked); ?>><?php echo POS_ONLY; ?>&nbsp;&nbsp;
  <a class="button" title="<?php echo SHOW_GRAPH_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.HoursReport.submit();"><span><?php echo SHOW_GRAPH; ?></span><input type="hidden" name="CreateAssign" value="<?php echo SHOW_GRAPH; ?>"></a>
  <?php
  if(basename($_SERVER['PHP_SELF'])!="reporting.php"){ ?>
   <a class="button" title="<?php echo BACK_TO_REPORTS_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); window.location.href='reporting.php';"><span><?php echo BACK_TO_REPORTS; ?></span><input type="hidden" name="CreateAssign" value="<?php echo BACK_TO_REPORTS; ?>"></a>
  <?php 
  }
  ?>
 </td>
 </tr>
 </form>
 </table>
