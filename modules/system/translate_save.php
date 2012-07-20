<?php /* SYSTEM $Id: translate_save.php,v 1.11.6.2 2006/04/06 13:43:04 cyberhorse Exp $ */
/**
* Processes the entries in the translation form.
* @version $Revision: 1.11.6.2 $
* @author Andrew Eddie <users.sourceforge.net>
*/

$module = dPgetParam($_POST, 'module', 0);
$lang = dPgetParam($_POST, 'lang', 'en');

$trans = dPgetParam($_POST, 'trans', 0);
//echo '<pre>';print_r( $trans );echo '</pre>';die;

// save to core locales if a translation exists there, otherwise save
// into the module's local locale area if it exists.  If not then
// the core table is updated.
$core_filename = "$baseDir/locales/$lang/$module.inc";
if ( file_exists( $core_filename ) ) {
	$filename = $core_filename;
} else {
	$mod_locale = "$baseDir/modules/$module/locales";
	if ( is_dir($mod_locale))
		$filename = "$baseDir/modules/$module/locales/$lang.inc";
	else
		$filename = $core_filename;
}

$fp = fopen ($filename, "wt");

if (!$fp) {
	$AppUI->setMsg( "Could not open locales file ($filename) to save.", UI_MSG_ERROR );
	$AppUI->redirect( "m=system" );
}

$txt = "##\n## DO NOT MODIFY THIS FILE BY HAND!\n##\n";

if ($lang == 'en') {
// editing the english file
	foreach ($trans as $langs) {
		if ( (@$langs['abbrev'] || $langs['english']) && empty($langs['del']) ) {
			$langs['abbrev'] = addslashes( stripslashes( @$langs['abbrev'] ) );
			$langs['english'] = addslashes( stripslashes( $langs['english'] ) );
			if (!empty($langs['abbrev'])) {
				$txt .= "\"{$langs['abbrev']}\"=>";
			}
			$txt .= "\"{$langs['english']}\",\n";
		}
	}
} else {
// editing the translation
	foreach ($trans as $langs) {
		if ( empty($langs['del']) ) {
			$langs['english'] = stripslashes( $langs['english'] );
			$langs['lang'] = addslashes( stripslashes( $langs['lang'] ) );
			//fwrite( $fp, "\"{$langs['english']}\"=>\"{$langs['lang']}\",\n" );
			$txt .= "\"{$langs['english']}\"=>\"{$langs['lang']}\",\n";
		}
	}
}
//echo "<pre>$txt</pre>";
fwrite( $fp, $txt );
fclose( $fp );

$AppUI->setMsg( "Locales file saved", UI_MSG_OK );
$AppUI->redirect();
?>
