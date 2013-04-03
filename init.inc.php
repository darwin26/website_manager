<?php
require_once($REX['INCLUDE_PATH'] . '/addons/website_manager/classes/class.rex_website.inc.php');
require_once($REX['INCLUDE_PATH'] . '/addons/website_manager/classes/class.rex_website_style.inc.php');
require_once($REX['INCLUDE_PATH'] . '/addons/website_manager/classes/class.rex_website_manager.inc.php');

// styles
$websiteStyles[] = new rex_website_style('blue', 'website.ico', '#47a0ce');
$websiteStyles[] = new rex_website_style('green', 'website2.ico', '#8eb659');
$websiteStyles[] = new rex_website_style('red', 'website3.ico', '#d1513c');
$websiteStyles[] = new rex_website_style('violet', 'website4.ico', '#cb41d2');
$websiteStyles[] = new rex_website_style('orange', 'website5.ico', '#dfaa3c');

// init website manager
$REX['WEBSITE_MANAGER'] = new rex_website_manager();

// --- DYN
$REX['WEBSITE_MANAGER']->addWebsite(new rex_website(1, 'addonfactory.local', 'AddonFactory', 1, 1, 1, 'addonfactory', $websiteStyles[0], 'rex_', 'http'));
// --- /DYN

// init current website
$REX['WEBSITE_MANAGER']->init();
