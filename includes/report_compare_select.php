<?php
// includes/report_compare_select.php


if (file_exists("includes/lang/$lang/includes/report_compare_select.php")) {
	include("includes/lang/$lang/includes/report_compare_select.php");
}


// Year Limit
$YearLimit = date("Y");
$YearLimit++;
?>
<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <form action="report_compare.php" name="CompareForm" method="get">
 <tr>
 <td width="100%" class="tdBlue" colspan="3" align="center">
  <b><?php echo COMPARISON_REPORT; ?></b>
 </td>
 </tr>
 <tr>
 <td width="100%" class="tdBlue" colspan="3" align="center">
  <input type="radio" name="RangeType" value="<?php echo TO; ?>"<?php if($_REQUEST['RangeType']!="Through"){ echo 
  ' checked'; } ?>> <?php echo ' ' . TO .  '   '; ?>
  <input type="radio" name="RangeType" value="<?php echo THROUGH; ?>"<?php if($_REQUEST['RangeType']=="Through"){ echo(" checked"); } ?>><?php echo THROUGH; ?>
 </td>
 </tr>
 <tr>
 <td width="30%" class="tdBlue">
 <input type="radio" name="CompareType" value="<?php echo DAY; ?>"<?php if($_REQUEST['CompareType']=="Day"){ echo(" checked"); } ?>>
 <b><?php echo COMPARE_DAYS; ?></b>
 </td>
 <td width="35%">
 <select name="Day1_Month"><?php for($i=1; $i<13; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['Day1_Month']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select> /
 <select name="Day1_Day"><?php for($i=1; $i<32; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['Day1_Day']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select> /
 <select name="Day1_Year"><?php for($i=2004; $i<$YearLimit; $i++){  if($_REQUEST['Day1_Year']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select>
 </td>
 <td width="35%">
 <select name="Day2_Month"><?php for($i=1; $i<13; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['Day2_Month']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select> /
 <select name="Day2_Day"><?php for($i=1; $i<32; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['Day2_Day']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select> /
 <select name="Day2_Year"><?php for($i=2004; $i<$YearLimit; $i++){  if($_REQUEST['Day2_Year']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select>
 </td>
 </tr>
 
 <tr>
 <td width="30%" class="tdBlue">
 <input type="radio" name="CompareType" value="Month"<?php if($_REQUEST['CompareType']=="Month"){ echo(" checked"); } ?>>
 <b><?php echo COMPARE_MONTHS; ?></b>
 </td>
 <td width="35%">
 <select name="Month1_Month"><?php for($i=1; $i<13; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['Month1_Month']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select> /
 <select name="Month1_Year"><?php for($i=2004; $i<$YearLimit; $i++){  if($_REQUEST['Month1_Year']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select>
 </td>
 <td width="35%">
 <select name="Month2_Month"><?php for($i=1; $i<13; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['Month2_Month']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select> /
 <select name="Month2_Year"><?php for($i=2004; $i<$YearLimit; $i++){  if($_REQUEST['Month2_Year']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select>
 </td>
 </tr>
 
 <tr>
 <td width="30%" class="tdBlue">
 <input type="radio" name="CompareType" value="Year"<?php if($_REQUEST['CompareType']=="Year"){ echo(" checked"); } ?>>
 <b><?php echo COMPARE_YEARS; ?></b>
 </td>
 <td width="35%">
 <select name="Year1_Year"><?php for($i=2004; $i<$YearLimit; $i++){  if($_REQUEST['Year1_Year']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select>
 </td>
 <td width="35%">
 <select name="Year2_Year"><?php for($i=2004; $i<$YearLimit; $i++){  if($_REQUEST['Year2_Year']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select>
 </td>
 </tr>
 <tr height="45px">
 <td width="100%" class="tdBlue" colspan="3" align="center">
  <?php $checked = '';
	if ($_GET['posonly'] == 1) {
	$checked = 'checked';
	}  
?>
  <input type="checkbox" name="posonly" value="1" <?php echo($checked); ?>><?php echo POS_ONLY; ?>&nbsp;&nbsp;
  <a class="button" title="<?php echo SHOW_GRAPH_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.CompareForm.submit();"><span><?php echo SHOW_GRAPH; ?></span><input type="hidden" name="CreateAssign" value="<?php echo SHOW_GRAPH; ?>"></a>
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
