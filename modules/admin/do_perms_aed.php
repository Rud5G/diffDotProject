<?php /* ADMIN $Id: do_perms_aed.php,v 1.4 2004/10/15 01:32:52 ajdonnison Exp $ */
$del = isset($_POST['del']) ? $_POST['del'] : 0;

$obj =& $AppUI->acl();

$AppUI->setMsg( 'Permission' );
if ($del) {
	if ($obj->del_acl($_REQUEST['permission_id'])) {
		$AppUI->setMsg( "deleted", UI_MSG_ALERT, true );
		$AppUI->redirect();
	} else {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	}
} else {
	if ($obj->addUserPermission()) {
		$AppUI->setMsg( $isNotNew ? 'updated' : 'added', UI_MSG_OK, true );
	} else {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	}
	$AppUI->redirect();
}
?>
