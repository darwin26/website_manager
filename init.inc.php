<?php
require_once($REX['INCLUDE_PATH'] . '/addons/website_manager/classes/class.rex_website.inc.php');
require_once($REX['INCLUDE_PATH'] . '/addons/website_manager/classes/class.rex_website_manager.inc.php');

// create website manager
$REX['WEBSITE_MANAGER'] = new rex_website_manager();

// --- DYN
$REX['WEBSITE_MANAGER']->addWebsite(new rex_website(1, 'addonfactory.local', 'AddonFactory', 1, 1, 1, '#47a0ce', 'rex_', 'http'));
// --- /DYN

// init current website
$REX['WEBSITE_MANAGER']->init();

