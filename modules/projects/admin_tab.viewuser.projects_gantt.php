<?php /* TASKS $Id: admin_tab.viewuser.projects_gantt.php,v 1.1.2.3 2006/02/27 09:34:10 gregorerhardt Exp $gantt.php,v 1.30 2004/08/06 22:56:54 gregorerhardt Exp $ */
GLOBAL  $company_id, $dept_ids, $department, $min_view, $m, $a, $user_id, $tab;

// reset the department and company filter info
// which is not used here
$company_id = $department = 0;

require("{$dPconfig['root_dir']}/modules/projects/viewgantt.php");
?>
