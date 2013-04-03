<?php
// init addon
$REX['ADDON']['name']['website_manager'] = 'Website Manager';
$REX['ADDON']['page']['website_manager'] = 'website_manager';
$REX['ADDON']['version']['website_manager'] = '1.0.0 ALPHA';
$REX['ADDON']['author']['website_manager'] = "RexDude";
$REX['ADDON']['supportpage']['website_manager'] = 'forum.redaxo.de';
$REX['ADDON']['perm']['website_manager'] = 'website_manager[]';

// permissions
$REX['PERM'][] = 'website_manager[]';

// includes
require_once($REX['INCLUDE_PATH'] . '/addons/website_manager/classes/class.rex_website_manager_utils.inc.php');
require_once($REX['INCLUDE_PATH'] . '/addons/website_manager/settings.inc.php');

if ($REX['REDAXO']) {
	// add lang file
	$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/website_manager/lang/');

	// add subpages
	$REX['ADDON']['website_manager']['SUBPAGES'] = array(
		array('', $I18N->msg('website_manager_websites')),
		array('styles', $I18N->msg('website_manager_styles')),
		array('tools', $I18N->msg('website_manager_tools')),
		array('options', $I18N->msg('website_manager_options')),
		array('setup', $I18N->msg('website_manager_setup')),
		array('help', $I18N->msg('website_manager_help'))
	);

	if (rex_request('page') != '') { // login
		// add css/js to page header
		rex_register_extension('PAGE_HEADER', 'rex_website_manager_utils::appendToPageHeader');

		if (rex_request('install') != '1') { // this shoudn't go off when addon gets installed
			// add website select and other stuff
			rex_register_extension('OUTPUT_FILTER', 'rex_website_manager_utils::addToOutputFilter');
		}

		// frontend link in meta menu 
		if ($REX['ADDON']['website_manager']['settings']['show_metamenu_frontend_link']) {
			rex_register_extension('META_NAVI', 'rex_website_manager_utils::addFrontendLinkToMetaMenu');
		}

		// fix article preview link
		rex_register_extension('PAGE_CONTENT_MENU', 'rex_website_manager_utils::fixArticlePreviewLink');

		// fix clang
		rex_register_extension('CLANG_ADDED', 'rex_website_manager::fixClang');
		rex_register_extension('CLANG_DELETED', 'rex_website_manager::fixClang');
	}
}
