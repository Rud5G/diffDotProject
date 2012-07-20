<?php /* TASKS $Id: vw_logs.php,v 1.23.8.1 2006/04/09 10:51:33 cyberhorse Exp $ */
global $AppUI, $task_id, $df, $canEdit, $m;

$perms =& $AppUI->acl();
if (! $perms->checkModuleItem('task_log', 'view', $task_id)) {
	$AppUI->redirect("m=public&a=access_denied");
}

$problem = intval( dPgetParam( $_GET, 'problem', null ) );
// get sysvals
$taskLogReference = dPgetSysVal( 'TaskLogReference' );
$taskLogReferenceImage = dPgetSysVal( 'TaskLogReferenceImage' );
?>
<script language="JavaScript">
<?php
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
$canDelete = $perms->checkModuleItem('task_log', 'delete', $task_id);
if ($canDelete) {
?>
function delIt2(id) {
	if (confirm( "<?php echo $AppUI->_('doDelete', UI_OUTPUT_JS).' '.$AppUI->_('Task Log', UI_OUTPUT_JS).'?';?>" )) {
		document.frmDelete2.task_log_id.value = id;
		document.frmDelete2.submit();
	}
}
<?php } ?>
</script>

<table border="0" cellpadding="2" cellspacing="1" width="100%" class="tbl">
<form name="frmDelete2" action="./index.php?m=tasks" method="post">
	<input type="hidden" name="dosql" value="do_updatetask">
	<input type="hidden" name="del" value="1" />
	<input type="hidden" name="task_log_id" value="0" />
</form>

<tr>
	<th></th>
	<th><?php echo $AppUI->_('Date');?></th>
        <th title="<?php echo $AppUI->_('Reference');?>"><?php echo $AppUI->_('Ref');?></th>
	<th width="100"><?php echo $AppUI->_('Summary');?></th>
    <th><?php echo $AppUI->_('URL');?></th>
	<th width="100"><?php echo $AppUI->_('User');?></th>
	<th width="100"><?php echo $AppUI->_('Hours');?></th>
	<th width="100"><?php echo $AppUI->_('Cost Code');?></th>
	<th width="100%"><?php echo $AppUI->_('Comments');?></th>
	<th></th>
</tr>
<?php
// Pull the task comments
$sql = "
SELECT task_log.*, user_username, billingcode_name as task_log_costcode
FROM task_log
LEFT JOIN billingcode ON task_log.task_log_costcode = billingcode_id
LEFT JOIN users ON user_id = task_log_creator
WHERE task_log_task = $task_id". ($problem ? " AND task_log_problem > '0'" : '') .
" ORDER BY task_log_date
";
$logs = db_loadList( $sql );

$s = '';
$hrs = 0;
foreach ($logs as $row) {
	$task_log_date = intval( $row['task_log_date'] ) ? new CDate( $row['task_log_date'] ) : null;
        $style = $row['task_log_problem'] ? 'background-color:#cc6666;color:#ffffff' :'';

	$s .= '<tr bgcolor="white" valign="top">';
	$s .= "\n\t<td>";
	if ($perms->checkModuleItem('task_log', 'edit', $task_id)) {
		if ($tab == -1) {
		 	$s .= "\n\t\t<a href=\"?m=tasks&a=view&task_id=$task_id&tab=".$AppUI->getState( 'TaskLogVwTab' );
		} else {
			$s .= "\n\t\t<a href=\"?m=tasks&a=view&task_id=$task_id&tab=1";

		}
		 $s .= "&task_log_id=".@$row['task_log_id']."#log\">"
			. "\n\t\t\t". dPshowImage( './images/icons/stock_edit-16.png', 16, 16, '' )
			. "\n\t\t</a>";
	}
        $s .= "\n\t</td>";
	$s .= '<td nowrap="nowrap">'.($task_log_date ? $task_log_date->format( $df ) : '-').'</td>';
        //$s .= '<td align="center" valign="middle">'.($row['task_log_problem'] ?  dPshowImage( './images/icons/mark-as-important-16.png', 16, 16, 'Problem', 'Problem' ) : '').'</td>';
        $reference_image = "-";
        if($row["task_log_reference"] > 0){
            if(isset($taskLogReferenceImage[$row["task_log_reference"]])){
                $reference_image = dPshowImage( $taskLogReferenceImage[$row["task_log_reference"]], 16, 16, $taskLogReference[$row["task_log_reference"]], $taskLogReference[$row["task_log_reference"]] );
            } else if (isset($taskLogReference[$row["task_log_reference"]])){
                $reference_image = $taskLogReference[$row["task_log_reference"]];
            }
        }
        $s .= '<td align="center" valign="middle">'.$reference_image.'</td>';
	$s .= '<td width="30%" style="'.$style.'">'.@$row["task_log_name"].'</td>';
    $s .= !empty($row["task_log_related_url"]) ? '<td><a href="'.@$row["task_log_related_url"].'" title="'.@$row["task_log_related_url"].'">'.$AppUI->_('URL').'</a></td>' : '<td></td>';
	$s .= '<td width="100">'.$row["user_username"].'</td>';
	$s .= '<td width="100" align="right">'.sprintf( "%.2f", $row["task_log_hours"] ) . '</td>';
	$s .= '<td width="100">'.$row["task_log_costcode"].'</td>';
	$s .= '<td>'.'<a name="tasklog'.@$row['task_log_id'].'"></a>';

// dylan_cuthbert: auto-transation system in-progress, leave these lines
	$transbrk = "\n[translation]\n";
	$descrip = str_replace( "\n", "<br />", htmlspecialchars($row['task_log_description']) );
	$tranpos = strpos( $descrip, str_replace( "\n", "<br />", $transbrk ) );
	if ( $tranpos === false) $s .= $descrip;
	else
	{
		$descrip = substr( $descrip, 0, $tranpos );
		$tranpos = strpos( $row['task_log_description'], $transbrk );
		$transla = substr( $row['task_log_description'], $tranpos + strlen( $transbrk ) );
		$transla = trim( str_replace( "'", '"', $transla ) );
		$s .= $descrip."<div style='font-weight: bold; text-align: right'><a title='$transla' class='hilite'>[".$AppUI->_("translation")."]</a></div>";
	}
// end auto-translation code
			
	$s .= '</td>';
	$s .= "\n\t<td>";
	if ($canDelete) {
		$s .= "\n\t\t<a href=\"javascript:delIt2({$row['task_log_id']});\" title=\"".$AppUI->_('delete log')."\">"
			. "\n\t\t\t". dPshowImage( './images/icons/stock_delete-16.png', 16, 16, '' )
			. "\n\t\t</a>";
	}
	$s .= "\n\t</td>";
	$s .= '</tr>';
	$hrs += (float)$row["task_log_hours"];
}
$s .= '<tr bgcolor="white" valign="top">';
$s .= '<td colspan="6" align="right">' . $AppUI->_('Total Hours') . ' =</td>';
$s .= '<td align="right">' . sprintf( "%.2f", $hrs ) . '</td>';
$s .= '<td align="right" colspan="3"><form action="?m=tasks&a=view&tab=1&task_id=' . $task_id . '" method="post">';
$s .= '<input type="submit" class="button" value="'.$AppUI->_('new log').'"></form></td>';
$s .= '</tr>';
echo $s;
?>
</table>
<table>
<tr>
	<td><?php echo $AppUI->_('Key');?>:</td>
	<td>&nbsp; &nbsp;</td>
	<td bgcolor="#ffffff">&nbsp; &nbsp;</td>
	<td>=<?php echo $AppUI->_('Normal Log');?></td>
	<td bgcolor="#CC6666">&nbsp; &nbsp;</td>
	<td>=<?php echo $AppUI->_('Problem Report');?></td>
</tr>
</table>
