<?php /* $Id: index.php,v 1.7 2004/07/25 18:15:35 gregorerhardt Exp $ */

$hid = dPgetParam( $_GET, 'hid', 'help.toc' );

$inc = "{$dPconfig['root_dir']}/modules/help/{$AppUI->user_locale}/$hid.hlp";

if (!file_exists( $inc )) {
	$inc = "{$dPconfig['root_dir']}/modules/help/en/$hid.hlp";
	if (!file_exists( $inc )) {
		$hid = "help.toc";
		$inc = "{$dPconfig['root_dir']}/modules/help/{$AppUI->user_locale}/$hid.hlp";
		if (!file_exists( $inc )) {
		  $inc = "{$dPconfig['root_dir']}/modules/help/en/$hid.hlp";
		}
	}
}
if ($hid != 'help.toc') {
	echo '<a href="?m=help&dialog=1">' . $AppUI->_( 'index' ) . '</a>';
}
readfile( $inc );
?>
