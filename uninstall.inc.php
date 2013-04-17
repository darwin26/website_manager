<?php

$sql = new rex_sql();
//$sql->debugsql = true;

$sql->setQuery('DROP TABLE IF EXISTS `rex_website`');

$REX['ADDON']['install']['website_manager'] = 0;
?>
