<?php

$sql = new rex_sql();
//$sql->debugsql = true;

$sql->setQuery('DROP TABLE IF EXISTS `rex_website`');
$sql->setQuery('DROP TABLE IF EXISTS `rex_website_style`');

$REX['ADDON']['install']['website_manager'] = 0;
?>
