<?php /* DEPARTMENTS $Id: index.php,v 1.1 2003/03/05 07:08:17 eddieajau Exp $ */
$titleBlock = new CTitleBlock( 'Departments', 'users.gif', $m, '' );
$titleBlock->addCrumb( "?m=companies", "companies list" );
$titleBlock->show();

echo $AppUI->_( 'deptIndexPage' );
?>