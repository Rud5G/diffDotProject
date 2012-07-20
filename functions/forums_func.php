<?php /* FUNCTIONS $Id: forums_func.php,v 1.6 2003/03/05 22:40:34 eddieajau Exp $ */
$filters = array( '- Filters -' );

if ($a == 'viewer') {
	array_push( $filters,
		'My Watched',
		'Last 30 days'
	);
} else {
	array_push( $filters,
		'My Forums',
		'My Watched',
		'My Projects',
		'My Company',
		'Inactive Projects'
	);
}
?>