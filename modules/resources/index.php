<?php
$AppUI->savePlace();

$obj =& new CResource;

$perms =& $AppUI->acl();
$canEdit = $perms->checkModule($m, "edit");

$titleBlock =& new CTitleBlock('Resources', 'handshake.png', $m, "$m.$a");
if ($canEdit) {
  $titleBlock->addCell(
    '<input type="submit" class="button" value="'. $AppUI->_('new resource').'">', '',
    '<form action="?m=resources&a=addedit" method="post">','</form>'
  );
}
$titleBlock->show();

if (isset($_GET['tab'])) {
  $AppUI->setState('ResourcesIdxTab', $_GET['tab']);
}
$resourceTab = $AppUI->getState('ResourcesIdxTab', 0);
$tabBox =& new CTabBox("?m=resources", "{$dPconfig['root_dir']}/modules/resources/", $resourceTab);
$tabbed = $tabBox->isTabbed();
foreach ($obj->loadTypes() as $type) {
	if ($type['resource_type_id'] == 0 && ! $tabbed)
		continue;
  $tabBox->add('vw_resources', $type['resource_type_name']);
}

$tabBox->show();
?>
