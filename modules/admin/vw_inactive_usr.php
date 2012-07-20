<?php /* ADMIN $Id: vw_inactive_usr.php,v 1.15 2005/04/02 02:22:35 ajdonnison Exp $ */
GLOBAL $dPconfig, $canEdit, $stub, $where, $orderby;

$q  = new DBQuery;
$q->addTable('users', 'u');
$q->addQuery('DISTINCT(user_id), user_username, contact_last_name, contact_first_name,
	permission_user, contact_email, company_name, contact_company');
$q->addJoin('contacts', 'con', 'user_contact = contact_id');
$q->addJoin('companies', 'com', 'contact_company = company_id');
$q->addJoin('permissions', 'per', 'user_id = permission_user');


if ($stub) {
	$q->addWhere("(UPPER(user_username) LIKE '$stub%' or UPPER(contact_first_name) LIKE '$stub%' OR UPPER(contact_last_name) LIKE '$stub%')");
} else if ($where) {
	$where = $q->quote("%$where%");
	$q->addWhere("(UPPER(user_username) LIKE $where or UPPER(contact_first_name) LIKE $where OR UPPER(contact_last_name) LIKE $where)");
}

$q->addOrder($orderby);
$users = $q->loadList();
$canLogin = false;

require "{$dPconfig['root_dir']}/modules/admin/vw_usr.php";
?>
