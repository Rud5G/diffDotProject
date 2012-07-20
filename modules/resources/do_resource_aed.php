<?php /* RESOURCES $Id: do_resource_aed.php,v 1.1 2004/10/15 01:38:26 ajdonnison Exp $ */

$del = dPgetParam($_POST, 'del', 0);
$obj =& new CResource;
$msg = '';

if (! $obj->bind($_POST)) {
  $AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
  $AppUI->redirect();
}

$AppUI->setMsg('Resource');
if ($del) {
  if (! $obj->canDelete($msg)) {
    $AppUI->setMsg($msg, UI_MSG_ERROR);
    $AppUI->redirect();
  }
  if (($msg = $obj->delete())) {
    $AppUI->setMsg($msg, UI_MSG_ERROR);
    $AppUI->redirect();
  } else {
    $AppUI->setMsg('deleted', UI_MSG_ALERT, true);
    $AppUI->redirect('', -1);
  }
} else {
  if (($msg = $obj->store())) {
    $AppUI->setMsg($msg, UI_MSG_ERROR);
  } else {
    $AppUI->setMsg($_POST['resource_id'] ? 'updated' : 'added', UI_MSG_OK, true);
  }
  $AppUI->redirect();
}
?>
