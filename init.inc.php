<?php
require_once($REX['INCLUDE_PATH'] . '/addons/website_manager/classes/class.rex_website.inc.php');
require_once($REX['INCLUDE_PATH'] . '/addons/website_manager/classes/class.rex_website_style.inc.php');
require_once($REX['INCLUDE_PATH'] . '/addons/website_manager/classes/class.rex_website_manager.inc.php');

/*if (!OOAddon::isInstalled('website_manager')) {
	exit;
}*/

// init website manager
$REX['WEBSITE_MANAGER'] = new rex_website_manager();

// --- DYN
$websiteStyles[1] = new rex_website_style('blue', 'website.ico', '#47a0ce');
$websiteStyles[2] = new rex_website_style('green', 'website2.ico', '#8eb659');
$websiteStyles[3] = new rex_website_style('red', 'website3.ico', '#d1513c');
$websiteStyles[4] = new rex_website_style('violet', 'website4.ico', '#cb41d2');
$websiteStyles[5] = new rex_website_style('orange', 'website5.ico', '#dfaa3c');

$REX['WEBSITE_MANAGER']->addWebsite(new rex_website(1, 'addonfactory.local', 'AddonFactory', 1, 1, 1, $websiteStyles[1], 'rex_', 'http'));
// --- /DYN

// init current website
$REX['WEBSITE_MANAGER']->init();

