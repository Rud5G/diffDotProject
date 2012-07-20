<?php /* SYSKEYS $Id: do_syskey_aed.php,v 1.4 2003/04/24 19:08:28 eddieajau Exp $ */
$del = isset($_POST['del']) ? $_POST['del'] : 0;

$obj = new CSysKey();

if (!$obj->bind( $_POST )) {
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}

$AppUI->setMsg( "System Lookup Keys", UI_MSG_ALERT );
if ($del) {
	if (($msg = $obj->delete())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$AppUI->setMsg( "deleted", UI_MSG_ALERT, true );
	}
} else {
	if (($msg = $obj->store())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$AppUI->setMsg( @$_POST['syskey_id'] ? 'updated' : 'inserted', UI_MSG_OK, true );
	}
}
$AppUI->redirect( "m=system&u=syskeys&a=keys" );
?>