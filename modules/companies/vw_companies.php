<?php /* COMPANIES $Id: vw_companies.php,v 1.16 2005/03/11 20:13:50 gregorerhardt Exp $ */

global $search_string;
global $owner_filter_id;
global $currentTabId;
global $currentTabName;
global $tabbed;
global $type_filter;
global $orderby;
global $orderdir;

// load the company types

$types = dPgetSysVal( 'CompanyType' );
// get any records denied from viewing

$obj = new CCompany();
$allowedCompanies = $obj->getAllowedRecords($AppUI->user_id, 'company_id, company_name');

$company_type_filter = $currentTabId;
//Not Defined
$companiesType = true;
if ($currentTabName == "All Companies")
	$companiesType = false;
if ($currentTabName == "Not Applicable")
	$company_type_filter = 0;

// retrieve list of records
$q  = new DBQuery;
$q->addTable('companies', 'c');
$q->addQuery('c.company_id, c.company_name, c.company_type, c.company_description, count(distinct p.project_id) as countp, count(distinct p2.project_id) as inactive, con.contact_first_name, con.contact_last_name');
$q->addJoin('projects', 'p', 'c.company_id = p.project_company AND p.project_active <> 0');
$q->addJoin('users', 'u', 'c.company_owner = u.user_id');
$q->addJoin('contacts', 'con', 'u.user_contact = con.contact_id');
$q->addJoin('projects', 'p2', 'c.company_id = p2.project_company AND p2.project_active = 0');
if (count($allowedCompanies) > 0) { $q->addWhere('c.company_id IN (' . implode(',', array_keys($allowedCompanies)) . ')'); }
if ($companiesType) { $q->addWhere('c.company_type = '.$company_type_filter); }
if ($search_string != "") { $q->addWhere("c.company_name LIKE '%$search_string%'"); }
if ($owner_filter_id > 0) { $q->addWhere("c.company_owner = $owner_filter_id "); }
$q->addGroup('c.company_id');
$q->addOrder($orderby.' '.$orderdir);
$rows = $q->loadList();
?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<td nowrap="nowrap" width="60" align="right">&nbsp;<?php echo $AppUI->_('sort by');?>:&nbsp;</td>
	<th nowrap="nowrap">
		<a href="?m=companies&orderby=company_name" class="hdr"><?php echo $AppUI->_('Company Name');?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=companies&orderby=countp" class="hdr"><?php echo $AppUI->_('Active Projects');?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=companies&orderby=inactive" class="hdr"><?php echo $AppUI->_('Archived Projects');?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=companies&orderby=company_type" class="hdr"><?php echo $AppUI->_('Type');?></a>
	</th>
</tr>
<?php
$s = '';
$CR = "\n"; // Why is this needed as a variable?

$none = true;
foreach ($rows as $row) {
	$none = false;
	$s .= $CR . '<tr>';
	$s .= $CR . '<td>&nbsp;</td>';
	$s .= $CR . '<td><a href="./index.php?m=companies&a=view&company_id=' . $row["company_id"] . '" title="'.$row['company_description'].'">' . $row["company_name"] .'</a></td>';
	$s .= $CR . '<td width="125" align="center" nowrap="nowrap">' . $row["countp"] . '</td>';
	$s .= $CR . '<td width="125" align="center" nowrap="nowrap">' . @$row["inactive"] . '</td>';
	$s .= $CR . '<td width="125" align="center" nowrap="nowrap">' . $AppUI->_($types[@$row["company_type"]]) . '</td>';
	$s .= $CR . '</tr>';
}
echo "$s\n";
if ($none) {
	echo $CR . '<tr><td colspan="5">' . $AppUI->_( 'No companies available' ) . '</td></tr>';
}
?>
</table>
