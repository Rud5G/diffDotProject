<?php
// $Id: ae_dates.php,v 1.9.2.2 2005/11/17 10:27:32 cyberhorse Exp $

global $AppUI, $dPconfig, $task_parent_options, $loadFromTab;
global $can_edit_time_information, $locale_char_set, $obj;
global $durnTypes, $task_project, $task_id, $tab;

//Time arrays for selects
$start = intval(dPgetConfig('cal_day_start'));
$end   = intval(dPgetConfig('cal_day_end'));
$inc   = intval(dPgetConfig('cal_day_increment'));
if ($start === null ) $start = 8;
if ($end   === null ) $end = 17;
if ($inc   === null)  $inc = 15;
$hours = array();
for ( $current = $start; $current < $end + 1; $current++ ) {
	if ( $current < 10 ) { 
		$current_key = "0" . $current;
	} else {
		$current_key = $current;
	}
	
	if ( stristr($AppUI->getPref('TIMEFORMAT'), "%p") ){
		//User time format in 12hr
		$hours[$current_key] = ( $current > 12 ? $current-12 : $current );
	} else {
		//User time format in 24hr
		$hours[$current_key] = $current;
	}
}

$minutes = array();
$minutes["00"] = "00";
for ( $current = 0 + $inc; $current < 60; $current += $inc ) {
	$minutes[$current] = $current;
}

// format dates
$df = $AppUI->getPref('SHDATEFORMAT');

if (intval($obj->task_start_date))
  $start_date = new CDate($obj->task_start_date);
else if ($task_id != 0)
  $start_date = null;
else
  $start_date = new CDate();
//$start_date = intval( $obj->task_start_date ) ? new CDate( $obj->task_start_date ) : new CDate();
$end_date = intval( $obj->task_end_date ) ? new CDate( $obj->task_end_date ) : null;

// convert the numeric calendar_working_days config array value to a human readable output format
$cwd = explode(',', $dPconfig['cal_working_days']);

$cwd_conv = array_map( 'cal_work_day_conv', $cwd );
$cwd_hr = implode(', ', $cwd_conv);

function cal_work_day_conv($val) {
	GLOBAL $locale_char_set;
	$wk = Date_Calc::getCalendarWeek( null, null, null, "%a", LOCALE_FIRST_DAY );
	
	$day_name = $wk[($val - LOCALE_FIRST_DAY)%7];
	if ($locale_char_set == "utf-8" && function_exists("utf8_encode")) {
	    $day_name = utf8_encode($day_name);
	}
	return htmlentities($day_name, ENT_COMPAT, $locale_char_set);
}
?>
<form name="datesFrm" action="?m=tasks&a=addedit&task_project=<?php echo $task_project;?>" method="post">
<input name="dosql" type="hidden" value="do_task_aed" />
<input name="task_id" type="hidden" value="<?php echo $task_id;?>" />
<input name="sub_form" type="hidden" value="1" />
<table width="100%" border="0" cellpadding="4" cellspacing="0" class="std">
<?php
	if($can_edit_time_information){
?>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Start Date' );?></td>
	<td nowrap="nowrap">
		<input type="hidden" name="task_start_date" id="task_start_date" value="<?php echo $start_date ? $start_date->format( FMT_TIMESTAMP_DATE ) : "" ;?>" />
		<input type="text" name="start_date" id="start_date" value="<?php echo $start_date ? $start_date->format( $df ) : "" ;?>" class="text" disabled="disabled" />
		<a href="#" onClick="popCalendar(document.datesFrm.start_date)">
						<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0">
					</a>
	</td>
	<td>
		<table><tr>
						
	<?php
		echo "<td>" . arraySelect($hours, "start_hour",'size="1" onchange="setAMPM(this)" class="text"', $start_date ? $start_date->getHour() : $start ) . "</td><td>" . " : " . "</td>";
		echo "<td>" . arraySelect($minutes, "start_minute",'size="1" class="text"', $start_date ? $start_date->getMinute() : "0" ) . "</td>";
		if ( stristr($AppUI->getPref('TIMEFORMAT'), "%p") ) {
			echo '<td><input type="text" name="start_hour_ampm" id="start_hour_ampm" value="' . ( $start_date ? $start_date->getAMPM() : ( $start > 11 ? "pm" : "am" ) ) . '" disabled="disabled" class="text" size="2" /></td>';
		}
	?>
		</tr></table>
	</td>
</tr>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Finish Date' );?></td>
	<td nowrap="nowrap">
		<input type="hidden" name="task_end_date" id="task_end_date" value="<?php echo $end_date ? $end_date->format( FMT_TIMESTAMP_DATE ) : '';?>" />
		<input type="text" name="end_date" id="end_date" value="<?php echo $end_date ? $end_date->format( $df ) : '';?>" class="text" disabled="disabled" />
		<a href="#" onClick="popCalendar(document.datesFrm.end_date)">
						<img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar');?>" border="0">
					</a>
	</td>
        <td>
<table><tr>
	<?php
		echo "<td>" . arraySelect($hours, "end_hour",'size="1" onchange="setAMPM(this)" class="text"', $end_date ? $end_date->getHour() : $end ) . "</td><td>" . " : " . "</td>";
		echo "<td>" .arraySelect($minutes, "end_minute",'size="1" class="text"', $end_date ? $end_date->getMinute() : "00" ) . "</td>";
		if ( stristr($AppUI->getPref('TIMEFORMAT'), "%p") ) {
			echo '<td><input type="text" name="end_hour_ampm" id="end_hour_ampm" value="' . ( $end_date ? $end_date->getAMPM() : ( $end > 11 ? "pm" : "am" ) ) . '" disabled="disabled" class="text" size="2" /></td>';
		}
	?>
	</tr></table>
	</td>
</tr>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Expected Duration' );?>:</td>
	<td nowrap="nowrap">
		<input type="text" class="text" name="task_duration" maxlength="8" size="6" value="<?php echo isset($obj->task_duration) ? $obj->task_duration : 1;?>" />
	<?php
		echo arraySelect( $durnTypes, 'task_duration_type', 'class="text"', $obj->task_duration_type, true );
	?>
	</td>
	<td><?php echo $AppUI->_('Daily Working Hours').': '.$dPconfig['daily_working_hours']; ?></td>

</tr>
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Calculate' );?>:</td>
	<td nowrap="nowrap">
		<input type="button" value="<?php echo $AppUI->_('Duration');?>" onclick="calcDuration(document.datesFrm)" class="button" />
		<input type="button" value="<?php echo $AppUI->_('Finish Date');?>" onclick="calcFinish(document.datesFrm)" class="button" />
	</td>
	<td><?php echo $AppUI->_('Working Days').': '.$cwd_hr; ?></td>
</tr>
        <?php
        } else {  
        ?>
<tr>
        <td colspan='2'>
                <?php echo $AppUI->_("Only the task owner, project owner, or system administrator is able to edit time related information."); ?>
        </td>
</tr>
        <?php
        }// end of can_edit_time_information
        ?>
</table>
</form>
<script language="javascript">
 subForm.push(new FormDefinition(<?php echo $tab;?>, document.datesFrm, checkDates, saveDates));
</script>
