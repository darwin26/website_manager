<?php

// append lang file
$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/website_manager/lang/');

// check redaxo version
if (version_compare($REX['VERSION'] . '.' . $REX['SUBVERSION'] . '.' . $REX['MINORVERSION'], '4.4.1', '<=')) {
	// version incorrect
	$REX['ADDON']['installmsg']['website_manager'] = $I18N->msg('website_manager_install_rex_version'); 
} else {
	// version correct. proceed...
	require_once($REX['INCLUDE_PATH'] . '/addons/website_manager/classes/class.rex_website.inc.php');
	require_once($REX['INCLUDE_PATH'] . '/addons/website_manager/classes/class.rex_website_manager.inc.php');
	require_once($REX['INCLUDE_PATH'] . '/addons/website_manager/classes/class.rex_website_manager_utils.inc.php');

	$firstWebsiteId = 1;
	$firstWebsiteProtocol = 'http';
	$firstWebsiteStyleId = 1;
	$firstTablePrefix = 'rex_';

	$sql = new rex_sql();
	//$sql->debugsql = true;

	$sql->setQuery('CREATE TABLE IF NOT EXISTS `rex_website` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`domain` varchar(255) NOT NULL, 
		`title` varchar(255) NOT NULL,
		`start_article_id` int(11) NOT NULL,
		`notfound_article_id` int(11) NOT NULL,
		`default_template_id` int(11) NOT NULL,
		`table_prefix` varchar(255) NOT NULL,
		`protocol` varchar(255) NOT NULL,
		`style_id` int(11) NOT NULL,
		`priority` INT(11) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM;');

	$sql->setQuery('INSERT INTO `rex_website` VALUES (1, "' . rex_website_manager_utils::sanitizeUrl($REX['SERVER']) . '", "' . $REX['SERVERNAME'] . '", ' . $REX['START_ARTICLE_ID'] . ', ' . $REX['NOTFOUND_ARTICLE_ID'] . ', ' . $REX['DEFAULT_TEMPLATE_ID'] . ', "' . $firstTablePrefix . '", "' . $firstWebsiteProtocol  . '", ' . $firstWebsiteStyleId . ', 1)');                                                                                

	$sql->setQuery('CREATE TABLE IF NOT EXISTS `rex_website_style` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`name` varchar(255) NOT NULL,
		`icon` varchar(255) NOT NULL,
		`color` varchar(255) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM;');

	$sql->setQuery('INSERT INTO `rex_website_style` VALUES (1, "blue", "website.ico", "#47a0ce")');
	$sql->setQuery('INSERT INTO `rex_website_style` VALUES (2, "green", "website2.ico", "#8eb659")');
	$sql->setQuery('INSERT INTO `rex_website_style` VALUES (3, "red", "website3.ico", "#d1513c")');
	$sql->setQuery('INSERT INTO `rex_website_style` VALUES (4, "violet", "website4.ico", "#cb41d2")');
	$sql->setQuery('INSERT INTO `rex_website_style` VALUES (5, "orange", "website5.ico", "#dfaa3c")');

	$error = $sql->getError();

	if ($error == '') {
		rex_website_manager::updateInitFile();
		rex_website_manager::fixClang(null);

		$REX['ADDON']['install']['website_manager'] = 1;
	} else {
		$REX['ADDON']['installmsg']['website_manager'] = $error;
	}
}
?>
