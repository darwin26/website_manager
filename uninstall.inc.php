<?php

if (!isset($REX['WEBSITE_MANAGER_UNINSTALL'])) {
	echo rex_warning($I18N->msg('website_manager_uninstall_codeline_hint'));
	exit;
}

$sql = new rex_sql();
//$sql->debugsql = true;

$sql->setQuery('DROP TABLE IF EXISTS `rex_website`');

$REX['ADDON']['install']['website_manager'] = 0;
?>
