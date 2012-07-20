<?php /* TASKS $Id: index.php,v 1.46.6.1 2006/03/12 16:03:57 cyberhorse Exp $ */
$AppUI->savePlace();
$perms =& $AppUI->acl();
// retrieve any state parameters
$user_id = $AppUI->user_id;
if($perms->checkModule("admin", "view")){ // Only sysadmins are able to change users
	if(dPgetParam($_POST, "user_id", 0) != 0){ // this means that 
		$user_id = dPgetParam($_POST, "user_id", 0);
		$AppUI->setState("user_id", $_POST["user_id"]);
	} else if ($AppUI->getState("user_id")){
		$user_id = $AppUI->getState("user_id");
	} else {
		$AppUI->setState("user_id", $user_id);
	}
}

if (isset( $_POST['f'] )) {
	$AppUI->setState( 'TaskIdxFilter', $_POST['f'] );
}
$f = $AppUI->getState( 'TaskIdxFilter' ) ? $AppUI->getState( 'TaskIdxFilter' ) : 'myunfinished';

if (isset( $_POST['f2'] )) {
	$AppUI->setState( 'CompanyIdxFilter', $_POST['f2'] );
}
$f2 = $AppUI->getState( 'CompanyIdxFilter' ) ? $AppUI->getState( 'CompanyIdxFilter' ) : 'all';

if (isset( $_GET['project_id'] )) {
	$AppUI->setState( 'TaskIdxProject', $_GET['project_id'] );
}
$project_id = $AppUI->getState( 'TaskIdxProject' ) ? $AppUI->getState( 'TaskIdxProject' ) : 0;

// get CCompany() to filter tasks by company
require_once( $AppUI->getModuleClass( 'companies' ) );
$obj = new CCompany();
$companies = $obj->getAllowedRecords( $AppUI->user_id, 'company_id,company_name', 'company_name' );
$filters2 = arrayMerge(  array( 'all' => $AppUI->_('All Companies', UI_OUTPUT_RAW) ), $companies );

// setup the title block
$titleBlock = new CTitleBlock( 'Tasks', 'applet-48.png', $m, "$m.$a" );

// patch 2.12.04 text to search entry box
if (isset( $_POST['searchtext'] )) {
	$AppUI->setState( 'searchtext', $_POST['searchtext']);
}

$search_text = $AppUI->getState('searchtext') ? $AppUI->getState('searchtext'):'';
$search_text = dPformSafe($search_text, true);

$titleBlock->addCell( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $AppUI->_('Search') . ':' );
$titleBlock->addCell(
	'<input type="text" class="text" SIZE="20" name="searchtext" onChange="document.searchfilter.submit();" value=' . "'$search_text'" .
	'title="'. $AppUI->_('Search in name and description fields') . '"/>
       	<!--<input type="submit" class="button" value=">" title="'. $AppUI->_('Search in name and description fields') . '"/>-->', '',
	'<form action="?m=tasks" method="post" id="searchfilter">', '</form>'
);


// Let's see if this user has admin privileges
if(!getDenyRead("admin")){
	$titleBlock->addCell();
	$titleBlock->addCell( $AppUI->_("User") . ":" );
	
	$user_list = $perms->getPermittedUsers('tasks');
	$titleBlock->addCell(
		arraySelect($user_list, "user_id", "size='1' class='text' onChange='document.userIdForm.submit();'", $user_id, false), "",
		"<form action='?m=tasks' method='post' name='userIdForm'>","</form>"
	);
}

$titleBlock->addCell();
$titleBlock->addCell( $AppUI->_('Company') . ':' );
$titleBlock->addCell(
	arraySelect( $filters2, 'f2', 'size=1 class=text onChange="document.companyFilter.submit();"', $f2, false ), '',
	'<form action="?m=tasks" method="post" name="companyFilter">', '</form>'
);



$titleBlock->addCell();
if ($canEdit && $project_id) {
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new task').'">', '',
		'<form action="?m=tasks&a=addedit&task_project=' . $project_id . '" method="post">', '</form>'
	);
}

$titleBlock->show();

if ( dPgetParam( $_GET, 'inactive', '' ) == 'toggle' )
	$AppUI->setState( 'inactive', $AppUI->getState( 'inactive' ) == -1 ? 0 : -1 );
$in = $AppUI->getState( 'inactive' ) == -1 ? '' : 'in';

// use a new title block (a new row) to prevent from oversized sites
$titleBlock = new CTitleBlock('', 'shim.gif');
$titleBlock->showhelp = false;
$titleBlock->addCell( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $AppUI->_('Task Filter') . ':' );
$titleBlock->addCell(
	arraySelect( $filters, 'f', 'size=1 class=text onChange="document.taskFilter.submit();"', $f, true ), '',
	'<form action="?m=tasks" method="post" name="taskFilter">', '</form>'
);
$titleBlock->addCell();

$titleBlock->addCrumb( "?m=tasks&a=todo&user_id=$user_id", "my todo" );
if (dPgetParam($_GET, 'pinned') == 1)
        $titleBlock->addCrumb( '?m=tasks', 'all tasks' );
else
        $titleBlock->addCrumb( '?m=tasks&pinned=1', 'my pinned tasks' );
$titleBlock->addCrumb( "?m=tasks&inactive=toggle", "show ".$in."active tasks" );
$titleBlock->addCrumb( "?m=tasks&a=tasksperuser", "tasks per user" );

$titleBlock->show();

// include the re-usable sub view
	$min_view = false;
	include("{$dPconfig['root_dir']}/modules/tasks/tasks.php");

?>
