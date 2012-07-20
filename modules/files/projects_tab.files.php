<?php /* PROJECTS $Id: projects_tab.files.php,v 1.1 2004/09/17 15:56:54 cyberhorse Exp $ */
GLOBAL $AppUI, $project_id, $deny, $canRead, $canEdit, $dPconfig;

$showProject = false;
require( dPgetConfig('root_dir') . '/modules/files/index_table.php' );
?>
