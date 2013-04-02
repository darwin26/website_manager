<?php

$tablePrefix = 'rex' . $website_uninstall['id'] . '_';
$dbName = $website_uninstall['db_name'];

$sql = rex_sql::factory();

$sql->setQuery('DROP TABLE ' . $dbName . '.' . $tablePrefix . 'article');
$sql->setQuery('DROP TABLE ' . $dbName . '.' . $tablePrefix . 'article_slice');
$sql->setQuery('DROP TABLE ' . $dbName . '.' . $tablePrefix . 'clang');
$sql->setQuery('DROP TABLE ' . $dbName . '.' . $tablePrefix . 'file');
$sql->setQuery('DROP TABLE ' . $dbName . '.' . $tablePrefix . 'file_category');

$sql->setQuery('DROP VIEW ' . $dbName . '.' . $tablePrefix . 'user');
$sql->setQuery('DROP VIEW ' . $dbName . '.' . $tablePrefix . 'module');
$sql->setQuery('DROP VIEW ' . $dbName . '.' . $tablePrefix . 'module_action');
$sql->setQuery('DROP VIEW ' . $dbName . '.' . $tablePrefix . 'template');
$sql->setQuery('DROP VIEW ' . $dbName . '.' . $tablePrefix . 'action');

echo $sql->getError();

