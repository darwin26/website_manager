<?php

$func = rex_request('func', 'string');
$website_id = rex_request('website_id', 'int');

// add or edit website (after form submit)
rex_register_extension('REX_FORM_SAVED', function ($params) {
	global $REX;

	// get values from submitted form
	$formValues = rex_website_manager_utils::getFormValues($params['form'], array('color'));
	$status = rex_request('status', 'string');

	if ($status == 'website_added') {
		// add website
		$websiteId = rex_website_manager_utils::getLastInsertedId($params['sql']);

		$tablePrefix = rex_website::constructTablePrefix($websiteId);
		$generatedDir = rex_website::constructGeneratedDir($websiteId);
		$filesDir = rex_website::constructMediaDir($websiteId);

		// update table prefix in db
		$sql = new rex_sql();
		$sql->debugsql = true;
		$sql->setQuery("UPDATE rex_website SET table_prefix = '" . $tablePrefix . "' WHERE id = " . $websiteId);

		// create clang file for clang fix
		rex_website_manager::createClangFile($websiteId);

		// add tables, folders and addon stuff
		require_once($REX['INCLUDE_PATH'] . '/addons/website_manager/install/create_website.inc.php');
	} else {
		// edit website
	}

	// favicon create/update
	if ($REX['ADDON']['website_manager']['settings']['colorize_favicon'] && $formValues['color'] != '') {
		rex_website_manager_utils::createIcon($formValues['color']);
	}

	// update init file to reflect changes
	$REX['WEBSITE_MANAGER']->updateInitFile();

	return true;
});

// delete website (after form submit)
rex_register_extension('REX_FORM_DELETED', function ($params) {
	global $REX;

	// get values from submitted form
	$formValues = rex_website_manager_utils::getFormValues($params['form'], array('color'));
	$websiteId = $params['form']->params['website_id'];

	$tablePrefix = rex_website::constructTablePrefix($websiteId);
	$generatedDir = rex_website::constructGeneratedDir($websiteId);
	$filesDir = rex_website::constructMediaDir($websiteId);

	// delete tables, folders and addon stuff
	require_once($REX['INCLUDE_PATH'] . '/addons/website_manager/install/destroy_website.inc.php');

	// delete clang file for clang fix
	rex_website_manager::deleteClangFile($websiteId);

	// update init file to reflect changes
	$REX['WEBSITE_MANAGER']->updateInitFile();

	return true;
});

// output
echo '<div class="rex-addon-output-v2">';

if ($REX['WEBSITE_MANAGER']->getCurrentWebsiteId() > 1) {
	// user can only edit websites if selected website is master website
	echo rex_info($I18N->msg('website_manager_website_master_msg'));
} elseif ($func == '') {
	// show website list
	$query = 'SELECT * FROM rex_website ORDER BY priority';

	$list = rex_list::factory($query);
	$list->setNoRowsMessage($I18N->msg('website_manager_website_no_websites_available'));
	$list->setCaption($I18N->msg('website_manager_website_list'));
	$list->addTableAttribute('summary', $I18N->msg('website_manager_website_list'));
	$list->addTableColumnGroup(array(40, 40, '*', 280, 80, 80));

	$list->removeColumn('start_article_id');
	$list->removeColumn('notfound_article_id');
	$list->removeColumn('default_template_id');
	$list->removeColumn('table_prefix');
	$list->removeColumn('color');
	$list->removeColumn('protocol');
	$list->removeColumn('priority');

	$list->setColumnLabel('id', $I18N->msg('website_manager_website_id'));
	$list->setColumnLabel('domain', $I18N->msg('website_manager_website_domain'));
	$list->setColumnLabel('title', $I18N->msg('website_manager_website_title'));
	$list->setColumnParams('domain', array('func' => 'edit', 'website_id' => '###id###'));

	// icon column
	$thIcon = '<a class="rex-i-element rex-i-generic-add" href="'. $list->getUrl(array('func' => 'add')) .'"><span class="rex-i-element-text">' . $I18N->msg('website_manager_website_add_website') . '</span></a>';
	$tdIcon = '<span class="rex-i-element rex-i-generic"><span class="rex-i-element-text">###name###</span></span>';
	$list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
	$list->setColumnParams($thIcon, array('func' => 'edit', 'website_id' => '###id###'));

	// functions column spans 2 data-columns
	$funcs = $I18N->msg('website_manager_website_functions');
	$list->addColumn($funcs, $I18N->msg('website_manager_website_edit'), -1, array('<th colspan="2">###VALUE###</th>','<td>###VALUE###</td>'));
	$list->setColumnParams($funcs, array('func' => 'edit', 'website_id' => $website_id, 'website_id' => '###id###'));

	$delete = 'deleteCol';
	$list->addColumn($delete, $I18N->msg('website_manager_website_delete'), -1, array('','<td>###VALUE###</td>'));
	$list->setColumnParams($delete, array('website_id' => '###id###', 'func' => 'delete'));
	$list->addLinkAttribute($delete, 'onclick', 'alert(\'' . $I18N->msg('website_manager_website_delete_editmode') . '\'); return false;');

	$list->show();

	// prio switch
	rex_website_manager_prio_switch::printSwitch(array($I18N->msg('website_manager_prio_mode'), $I18N->msg('website_manager_prio_mode_on'), $I18N->msg('website_manager_prio_mode_off')));
} elseif ($func == 'add' || $func == 'edit' && $website_id > 0) {
	if ($func == 'edit') {
		$formLabel = $I18N->msg('website_manager_website_website_edit');
		$defaultId = null;
	} elseif ($func == 'add') {
		$formLabel = $I18N->msg('website_manager_website_website_add');
		$defaultId = '1';
	}

	$form = rex_form::factory('rex_website', $formLabel, 'id=' . $website_id);

	$form->addErrorMessage(REX_FORM_ERROR_VIOLATE_UNIQUE_KEY, $I18N->msg('website_manager_website_id_exists'));

	// domain
	$field =& $form->addTextField('domain'); 
	$field->setLabel($I18N->msg('website_manager_website_domain'));

	// title
	$field =& $form->addTextField('title'); 
	$field->setLabel($I18N->msg('website_manager_website_title'));

	// start article id
	$field =& $form->addTextField('start_article_id', $defaultId); // addLinkmapField
	$field->setLabel($I18N->msg('website_manager_website_start_article_id'));

	// not found article id
	$field =& $form->addTextField('notfound_article_id', $defaultId);
	$field->setLabel($I18N->msg('website_manager_website_notfound_article_id'));

	// templates
	$field =& $form->addSelectField('default_template_id'); 
	$field->setLabel($I18N->msg('website_manager_website_default_template'));
	$select =& $field->getSelect();
	$select->setSize(1);

	$sql = rex_sql::factory();
	$sql->setQuery('select id, name from ' . $REX['TABLE_PREFIX'] . 'template where active = 1 order by name');
	$templates = $sql->getArray();

	foreach ($templates as $template) {
		$select->addOption($template['name'], $template['id']);
	}

	// protocol
	$field =& $form->addSelectField('protocol'); 
	$field->setLabel($I18N->msg('website_manager_website_protocol'));
	$select =& $field->getSelect();
	$select->setSize(1);
	$select->addOption($I18N->msg('website_manager_website_http'), 'http');
	$select->addOption($I18N->msg('website_manager_website_https'), 'https');

	// color
	$field =& $form->addTextField('color'); 
	$field->setLabel($I18N->msg('website_manager_website_color'));
	$field->setAttribute('id', 'color-picker');
	$field->setAttribute('style', 'visibility: hidden; height: 20px;');


	if ($func == 'edit') {
		$key = $I18N->msg('website_manager_edit_button_key');

		if ($REX['ADDON']['website_manager']['settings']['allow_website_delete']) {
			if ($website_id == 1) {
				$form->elements[$key][0]->deleteElement->setAttribute('onclick', "alert('" . $I18N->msg('website_manager_website_master_website_disallow_delete') . "'); return false;");
			} else {
				$form->elements[$key][0]->deleteElement->setAttribute('onclick', "return confirm('" . $I18N->msg('website_manager_website_delete_confirm') . "');");
			}
		} else {
			$form->elements[$key][0]->deleteElement->setAttribute('onclick', "alert('" . $I18N->msg('website_manager_website_delete_trunedoff') . "'); return false;");
		}

		$form->addParam('website_id', $website_id);
	} elseif ($func == 'add') {
		$form->addParam('status', 'website_added');
		
		$form->addHiddenField('priority', $REX['WEBSITE_MANAGER']->getWebsiteCount() + 1);
	}

	$form->show();
}

echo '</div>';
?>
