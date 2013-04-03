<?php

// ***************************************************************************************************
// database tables
// ***************************************************************************************************

$sql = rex_sql::factory();

$sql->setQuery('DROP TABLE ' $tablePrefix . 'article');
$sql->setQuery('DROP TABLE ' $tablePrefix . 'article_slice');
$sql->setQuery('DROP TABLE ' $tablePrefix . 'clang');
$sql->setQuery('DROP TABLE ' $tablePrefix . 'file');
$sql->setQuery('DROP TABLE ' $tablePrefix . 'file_category');

$sql->setQuery('DROP VIEW ' $tablePrefix . 'user');
$sql->setQuery('DROP VIEW ' $tablePrefix . 'module');
$sql->setQuery('DROP VIEW ' $tablePrefix . 'module_action');
$sql->setQuery('DROP VIEW ' $tablePrefix . 'template');
$sql->setQuery('DROP VIEW ' $tablePrefix . 'action');

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
	$sql->setQuery('DROP TABLE ' . $tables[$i]);
}

