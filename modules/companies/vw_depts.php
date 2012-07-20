<?php /* COMPANIES $Id: vw_depts.php,v 1.15 2005/04/26 06:03:16 ajdonnison Exp $ */
##
##	Companies: View Projects sub-table
##
GLOBAL $AppUI, $company_id, $canEdit;

$q  = new DBQuery;
$q->addTable('departments');
$q->addQuery('departments.*, COUNT(contact_department) dept_users');
$q->addJoin('contacts', 'c', 'c.contact_department = dept_id');
$q->addWhere('dept_company = '.$company_id);
$q->addGroup('dept_id');
$q->addOrder('dept_parent, dept_name');
$sql = $q->prepare();
$q->clear();

function showchilddept( &$a, $level=0 ) {
	global $AppUI;
	$s = '';

	$s .= '<td>';
	$s .= '<a href="./index.php?m=departments&a=addedit&dept_id='.$a["dept_id"].'" title="'.$AppUI->_('edit').'">';
	$s .= dPshowImage( './images/icons/stock_edit-16.png', 16, 16, '' );
	$s .= '</td>';
	$s .= '<td>';

	for ($y=0; $y < $level; $y++) {
		if ($y+1 == $level) {
			$s .= '<img src="./images/corner-dots.gif" width="16" height="12" border="0">';
		} else {
			$s .= '<img src="./images/shim.gif" width="16" height="12" border="0">';
		}
	}

	$s .= '<a href="./index.php?m=departments&a=view&dept_id='.$a["dept_id"].'">'.$a["dept_name"].'</a>';
	$s .= '</td>';
	$s .= '<td align="center">'.($a["dept_users"] ? $a["dept_users"] : '').'</td>';

	echo "<tr>$s</tr>";
}

function findchilddept( &$tarr, $parent, $level=0 ){
	$level = $level+1;
	$n = count( $tarr );
	for ($x=0; $x < $n; $x++) {
		if($tarr[$x]["dept_parent"] == $parent && $tarr[$x]["dept_parent"] != $tarr[$x]["dept_id"]){
			showchilddept( $tarr[$x], $level );
			findchilddept( $tarr, $tarr[$x]["dept_id"], $level);
		}
	}
}


$s = '<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">';
$s .= '<tr>';
$rows = db_loadList( $sql, NULL );
if (count( $rows)) {
	$s .= '<th>&nbsp;</th>';
	$s .= '<th width="100%">'.$AppUI->_( 'Name' ).'</th>';
	$s .= '<th>'.$AppUI->_( 'Users' ).'</th>';
} else {
	$s .= $AppUI->_('No data available');
}

$s .= '</tr>';
echo $s;

foreach ($rows as $row) {
	if ($row["dept_parent"] == 0) {
		showchilddept( $row );
		findchilddept( $rows, $row["dept_id"] );
	}
}

echo '<td colspan="3" nowrap="nowrap" rowspan="99" align="right" valign="top" style="background-color:#ffffff">';
if ($canEdit) {
	echo '<input type="button" class=button value="'.$AppUI->_( 'new department' ).'" onClick="javascript:window.location=\'./index.php?m=departments&a=addedit&company_id='.$company_id.'\';">';
}
echo '</td>';

echo '</table>';
?>
