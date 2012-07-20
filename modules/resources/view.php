<?php

$obj =& new CResource;
$resource_id = dPgetParam($_GET, 'resource_id', 0);
$perms =& $AppUI->acl();

$canView = $perms->checkModuleItem('resources', 'view', $resource_id);
$canEdit = $perms->checkModuleItem('resources', 'edit', $resource_id);
$canDelete = $perms->checkModuleItem('resources', 'delete', $resource_id);
$canAdd = $perms->checkModule('resources', 'add');

if (! $canView) {
  $AppUI->redirect("m=public&a=access_denied");
}

if (! $resource_id) {
  $AppUI->setMsg("invalid ID", UI_MSG_ERROR);
  $AppUI->redirect();
}
// TODO: tab stuff

$obj =& new CResource;

if (! $obj->load($resource_id)) {
  $AppUI->setMsg('Resource');
  $AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
  $AppUI->redirect();
} else {
  $AppUI->savePlace();
}

$titleBlock =& new CTitleBlock('View Resource', 'handshake.png', $m, "$m.$a");
if ($canAdd) {
  $titleBlock->addCell(
    '<input type="submit" class="button" value="'. $AppUI->_('new resource').'" />', '',
    '<form action="?m=resources&a=addedit" method="post">', '</form>'
  );
}

$titleBlock->addCrumb('?m=resources', 'resource list');
if ($canEdit) {
  $titleBlock->addCrumb("?m=resources&a=addedit&resource_id=$resource_id", "edit this resource");
}
if ($canDelete) {
  $titleBlock->addCrumbDelete('delete resource', $canDelete, 'no delete permission');
}
$titleBlock->show();

if ($canDelete) {
?>
<script language="javascript">
  can_delete = true;
  delete_msg = "<?php echo $AppUI->_('doDelete').' '.$AppUI->_('Resource').'?';?>";
</script>
<form name="frmDelete" action="./index.php?m=resources" method="post">
  <input type="hidden" name="dosql" value="do_resource_aed" />
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="resource_id" value="<?php echo $resource_id;?>" />
</form>
<?php
}
?>
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
<tr>
  <td valign="top" width="100%">
		<strong><?php echo $AppUI->_('Details');?></strong>
		<table cellspacing="1" cellpadding="2" width="100%">
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Resource ID');?>:</td>
			<td class="hilite" width="100%"><?php echo $obj->resource_key;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Resource Name');?>:</td>
			<td class="hilite" width="100%"><?php echo $obj->resource_name;?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Type');?>:</td>
			<td class="hilite" width="100%"><?php echo $obj->getTypeName();?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Max Allocation %');?>:</td>
			<td class="hilite"><?php echo @$obj->resource_max_allocation;?></td>
		</table>

	</td>
</tr>
<tr>

	<td width="100%" valign="top">
		<strong><?php echo $AppUI->_('Description');?></strong>
		<table cellspacing="0" cellpadding="2" border="0" width="100%">
		<tr>
			<td class="hilite">
				<?php echo str_replace( chr(10), "<br />", $obj->resource_note);?>&nbsp;
			</td>
		</tr>
		
		</table>
	</td>

</tr>
</table>
