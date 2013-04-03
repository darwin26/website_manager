<?php

// ***************************************************************************************************
// database tables
// ***************************************************************************************************

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

// ***************************************************************************************************
// directories
// ***************************************************************************************************

$includePath = realpath($REX['HTDOCS_PATH'] . 'redaxo/include/') . '/';

rex_website_manager_utils::rrmdir($includePath . $generatedDir);

$includePath = realpath($REX['HTDOCS_PATH']) . '/';

rex_website_manager_utils::rrmdir($includePath . $filesDir);

// ***************************************************************************************************
// addons
// ***************************************************************************************************

$tables = $sql->showTables(1, $tablePrefix);

for ($i = 0; $i < count($tables); $i++) {
	$sql->setQuery('DROP TABLE ' . $dbName . '.' . $tables[$i]);
}

