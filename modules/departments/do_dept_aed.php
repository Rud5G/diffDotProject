<?php /* DEPARTMENTS $Id: do_dept_aed.php,v 1.2.12.1 2006/02/18 18:12:20 gregorerhardt Exp $ */
$del = isset($_POST['del']) ? $_POST['del'] : 0;

$dept = new CDepartment();
if (($msg = $dept->bind( $_POST ))) {
	$AppUI->setMsg( $msg, UI_MSG_ERROR );
	$AppUI->redirect();
}

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg( 'Department' );
if ($del) {
	$dep = new CDepartment();
	$msg = $dep->load( $dept->dept_id );
	if (($msg = $dept->delete())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	} else {
		$AppUI->setMsg( "deleted", UI_MSG_ALERT, true );
		$AppUI->redirect( 'm=companies&a=view&company_id='.$dep->dept_company );
	}
} else {
	if (($msg = $dept->store())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$isNotNew = @$_POST['department_id'];
		$AppUI->setMsg( $isNotNew ? 'updated' : 'inserted', UI_MSG_OK, true );
	}
$AppUI->redirect();
}
?>
