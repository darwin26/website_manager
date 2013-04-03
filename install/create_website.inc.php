<?php

if ($websiteId == 1) {
	echo rex_warning('Website mit ID = 1 darf nicht hinzugefügt werden!'); // just for security reasons
	exit;
}

// ***************************************************************************************************
// database tables
// ***************************************************************************************************

$sql = rex_sql::factory();

$sql->setQuery('CREATE TABLE `' . $tablePrefix . 'article` ( `pid` int(11) NOT NULL  auto_increment, `id` int(11) NOT NULL  , `re_id` int(11) NOT NULL  , `name` varchar(255) NOT NULL  , `catname` varchar(255) NOT NULL  , `catprior` int(11) NOT NULL  , `attributes` text NOT NULL  , `startpage` tinyint(1) NOT NULL  , `prior` int(11) NOT NULL  , `path` varchar(255) NOT NULL  , `status` tinyint(1) NOT NULL  , `createdate` int(11) NOT NULL  , `updatedate` int(11) NOT NULL  , `template_id` int(11) NOT NULL  , `clang` int(11) NOT NULL  , `createuser` varchar(255) NOT NULL  , `updateuser` varchar(255) NOT NULL  , `revision` int(11) NOT NULL  , PRIMARY KEY (`pid`)) ENGINE=MyISAM;');
$sql->setQuery('CREATE TABLE `' . $tablePrefix . 'article_slice` ( `id` int(11) NOT NULL  auto_increment, `clang` int(11) NOT NULL  , `ctype` int(11) NOT NULL  , `re_article_slice_id` int(11) NOT NULL  ,`value1` text  NULL  , `value2` text NULL  , `value3` text NULL  , `value4` text NULL  , `value5` text NULL  , `value6` text NULL  , `value7` text NULL  , `value8` text NULL  , `value9` text NULL  , `value10` text NULL  , `value11` text NULL  , `value12` text NULL  , `value13` text NULL  , `value14` text NULL  , `value15` text NULL  , `value16` text NULL  , `value17` text NULL  , `value18` text NULL  , `value19` text NULL  , `value20` text NULL  , `file1` varchar(255) NULL  , `file2` varchar(255) NULL  , `file3` varchar(255) NULL  , `file4` varchar(255) NULL  , `file5` varchar(255) NULL  , `file6` varchar(255) NULL  , `file7` varchar(255) NULL  , `file8` varchar(255) NULL  , `file9` varchar(255) NULL  , `file10` varchar(255) NULL  , `filelist1` text NULL  , `filelist2` text NULL  , `filelist3` text NULL  , `filelist4` text NULL  , `filelist5` text NULL  , `filelist6` text NULL  , `filelist7` text NULL  , `filelist8` text NULL  , `filelist9` text NULL  , `filelist10` text NULL  , `link1` varchar(10) NULL  , `link2` varchar(10) NULL  , `link3` varchar(10) NULL  , `link4` varchar(10) NULL  , `link5` varchar(10) NULL  , `link6` varchar(10) NULL  , `link7` varchar(10) NULL  , `link8` varchar(10) NULL  , `link9` varchar(10) NULL  , `link10` varchar(10) NULL  , `linklist1` text NULL  , `linklist2` text NULL  , `linklist3` text NULL  , `linklist4` text NULL  , `linklist5` text NULL  , `linklist6` text NULL  , `linklist7` text NULL  , `linklist8` text NULL  , `linklist9` text NULL  , `linklist10` text NULL  , `php` text NULL  , `html` text NULL  ,`article_id` int(11) NOT NULL  , `modultyp_id` int(11) NOT NULL  , `createdate` int(11) NOT NULL  , `updatedate` int(11) NOT NULL  , `createuser` varchar(255) NOT NULL  , `updateuser` varchar(255) NOT NULL  , `next_article_slice_id` int(11) NULL  , `revision` int(11) NOT NULL  , PRIMARY KEY (`id`,`re_article_slice_id`,`article_id`,`modultyp_id`)) ENGINE=MyISAM;');
$sql->setQuery('CREATE TABLE `' . $tablePrefix . 'clang` ( `id` int(11) NOT NULL  , `name` varchar(255) NOT NULL  , `revision` int(11) NOT NULL  , PRIMARY KEY (`id`)) ENGINE=MyISAM;');
$sql->setQuery('CREATE TABLE `' . $tablePrefix . 'file` ( `file_id` int(11) NOT NULL  auto_increment, `re_file_id` int(11) NOT NULL  , `category_id` int(11) NOT NULL  , `attributes` text NULL  , `filetype` varchar(255) NULL  , `filename` varchar(255) NULL  , `originalname` varchar(255) NULL  , `filesize` varchar(255) NULL  , `width` int(11) NULL  , `height` int(11) NULL  , `title` varchar(255) NULL  , `createdate` int(11) NOT NULL  , `updatedate` int(11) NOT NULL  , `createuser` varchar(255) NOT NULL  , `updateuser` varchar(255) NOT NULL  , `revision` int(11) NOT NULL  , PRIMARY KEY (`file_id`)) ENGINE=MyISAM;');
$sql->setQuery('CREATE TABLE `' . $tablePrefix . 'file_category` ( `id` int(11) NOT NULL  auto_increment, `name` varchar(255) NOT NULL  , `re_id` int(11) NOT NULL  , `path` varchar(255) NOT NULL  , `createdate` int(11) NOT NULL  , `updatedate` int(11) NOT NULL  , `createuser` varchar(255) NOT NULL  , `updateuser` varchar(255) NOT NULL  , `attributes` text NULL  , `revision` int(11) NOT NULL  , PRIMARY KEY (`id`,`name`)) ENGINE=MyISAM;');

$sql->setQuery('INSERT INTO `' . $tablePrefix . 'clang` VALUES ("0", "deutsch", 0);');

$sql->setQuery('ALTER TABLE ' . $tablePrefix . 'article ADD INDEX `id` (`id`), ADD INDEX `clang` (`clang`), ADD UNIQUE INDEX `find_articles` (`id`, `clang`), ADD INDEX `re_id` (`re_id`);');
$sql->setQuery('ALTER TABLE ' . $tablePrefix . 'article_slice ADD INDEX `id` (`id`), ADD INDEX `clang` (`clang`), ADD INDEX `re_article_slice_id` (`re_article_slice_id`), ADD INDEX `article_id` (`article_id`), ADD INDEX `find_slices` (`clang`, `article_id`);');
$sql->setQuery('ALTER TABLE ' . $tablePrefix . 'file ADD INDEX `re_file_id` (`re_file_id`), ADD INDEX `category_id` (`category_id`);');
$sql->setQuery('ALTER TABLE ' . $tablePrefix . 'file_category DROP PRIMARY KEY, ADD PRIMARY KEY (`id`), ADD INDEX `re_id` (`re_id`);');

$sql->setQuery('CREATE VIEW ' . $tablePrefix . 'user AS SELECT * FROM rex_user');
$sql->setQuery('CREATE VIEW ' . $tablePrefix . 'module AS SELECT * FROM rex_module');
$sql->setQuery('CREATE VIEW ' . $tablePrefix . 'module_action AS SELECT * FROM rex_module_action');
$sql->setQuery('CREATE VIEW ' . $tablePrefix . 'template AS SELECT * FROM rex_template');
$sql->setQuery('CREATE VIEW ' . $tablePrefix . 'action AS SELECT * FROM rex_action');

// ***************************************************************************************************
// directories
// ***************************************************************************************************

$includePath = realpath($REX['HTDOCS_PATH'] . 'redaxo/include/') . '/';

mkdir($includePath . $generatedDir, $REX['DIRPERM']);
mkdir($includePath . $generatedDir . '/articles', $REX['DIRPERM']);
mkdir($includePath . $generatedDir . '/files', $REX['DIRPERM']);
mkdir($includePath . $generatedDir . '/templates', $REX['DIRPERM']);

$includePath = realpath($REX['HTDOCS_PATH']) . '/';

mkdir($includePath . $filesDir, $REX['DIRPERM']);

// ***************************************************************************************************
// addons
// ***************************************************************************************************

$reinstallAddons = $REX['ADDON']['website_manager']['settings']['reinstall_addons'];

$REX['TABLE_PREFIX'] = $tablePrefix;
$REX['GENERATED_PATH'] = realpath($REX['HTDOCS_PATH'] . 'redaxo/include/' . $generatedDir);

global $curAddonCount, $I18N, $REX;

for ($curAddonCount = 0; $curAddonCount < count($reinstallAddons); $curAddonCount++) {
	if (OOAddon::isInstalled($reinstallAddons[$curAddonCount])) {
		require_once($REX['INCLUDE_PATH'] . '/addons/' . $reinstallAddons[$curAddonCount] . '/install.inc.php');

		$sqlFile = $REX['INCLUDE_PATH'] . '/addons/' . $reinstallAddons[$curAddonCount] . '/install.sql';
	
		if (file_exists($sqlFile)) {
			rex_install_dump($sqlFile);
		}
	}
}

$REX['TABLE_PREFIX'] = $REX['WEBSITE_MANAGER']->getWebsite(1)->getTablePrefix();
$REX['GENERATED_PATH'] = realpath($REX['HTDOCS_PATH'] . 'redaxo/include/' . $REX['WEBSITE_MANAGER']->getWebsite(1)->getGeneratedDir());


