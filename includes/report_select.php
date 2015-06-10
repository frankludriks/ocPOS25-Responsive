<?php
// includes/report_select.php


if (file_exists("includes/lang/$lang/includes/report_select.php")) {
	include("includes/lang/$lang/includes/report_select.php");
}

// Year Limit
$YearLimit = date("Y");
$YearLimit++;
?>
<table width="100%" border="0" cellpadding="2" cellspacing="0" align="center">
 <tr>
  <td width="100%">
  
 <table class="tableBorder" border="0" width="760" cellpadding="2" cellspacing="1" align="center">
 <form name="Report" action="report.php" method="post">
 <tr>
 <td width="100%" class="tdBlue" colspan="2" align="center">
  <b><?php echo INVOICE_REPORT; ?></b>
 </td>
 </tr>
 <tr>
 <td width="30%" class="tdBlue"><b><?php echo REPORT_START; ?></b></td>
 <td width="70%">
 <select name="Start_Month"><?php for($i=1; $i<13; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['Start_Month']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select> -
 <select name="Start_Day"><?php for($i=1; $i<32; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['Start_Day']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select> -
 <select name="Start_Year"><?php for($i=2004; $i<$YearLimit; $i++){  if($_REQUEST['Start_Year']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select>
 &nbsp; &nbsp; &nbsp;
 <select name="Start_Hour"><?php for($i=0; $i<24; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['Start_Hour']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select>
 :
 <select name="Start_Min"><?php for($i=0; $i<60; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['Start_Min']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select>
 </td>
 </tr>
 <tr>
 <td width="30%" class="tdBlue"><b><?php echo REPORT_END; ?></b></td>
 <td width="70%">
 <select name="End_Month"><?php for($i=1; $i<13; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['End_Month']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select> -
 <select name="End_Day"><?php for($i=1; $i<32; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['End_Day']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select> -
 <select name="End_Year"><?php for($i=2004; $i<$YearLimit; $i++){  if($_REQUEST['End_Year']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select>
 &nbsp; &nbsp; &nbsp;
 <select name="End_Hour"><?php for($i=0; $i<24; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['End_Hour']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select>
 :
 <select name="End_Min"><?php for($i=0; $i<60; $i++){  if($i < 10) $i = "0$i"; if($_REQUEST['End_Min']==$i){ $SELECT=" selected"; }else{ $SELECT=""; } echo("<option value=\"$i\"$SELECT>$i</option>\n");  } ?></select>
 </td>
 </tr>
 <tr height="45px">
 <td width="100%" class="tdBlue" colspan="2" align="center">
 <?php 
	if (isset($_POST['posonly']) && ($_POST['posonly'] == 1)) {
		$checked = 'checked';
	} else {
		$checked = '';
	}
?>
  <input type="checkbox" name="posonly" value="1" <?php echo($checked); ?>><?php echo POS_ONLY; ?>&nbsp;&nbsp;
  <a class="button" title="<?php echo SHOW_GRAPH_BUTTON_TITLE; ?>" href="#" onclick="this.blur(); document.Report.submit();"><span><?php echo VIEW_REPORT; ?></span><input type="hidden" name="CreateAssign" value="<?php echo VIEW_REPORT; ?>"></a>
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
