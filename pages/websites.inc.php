<?php

$func = rex_request('func', 'string');
$website_id = rex_request('website_id', 'int');

$info = '';
$warning = '';

// add
rex_register_extension('REX_FORM_SAVED', function ($params) {
	global $REX;

	$status = rex_request('status', 'string');

	if ($status == 'website_added') {
		// first time added
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
		// form updated
	}

	$REX['WEBSITE_MANAGER']->updateInitFile();

	return true;
});

// delete
rex_register_extension('REX_FORM_DELETED', function ($params) {
	global $REX;

	$websiteId = $params['form']->params['website_id'];
	$tablePrefix = rex_website::constructTablePrefix($websiteId);
	$generatedDir = rex_website::constructGeneratedDir($websiteId);
	$filesDir = rex_website::constructMediaDir($websiteId);

	require_once($REX['INCLUDE_PATH'] . '/addons/website_manager/install/destroy_website.inc.php');

	$REX['WEBSITE_MANAGER']->updateInitFile();

	return true;
});

// output messages
if ($info != '') {
	echo rex_info($info);
}

if ($warning != '') {
	echo rex_warning($warning);
}

// output
echo '<div class="rex-addon-output-v2">';

if ($REX['WEBSITE_MANAGER']->getCurrentWebsiteId() > 1) {
	echo rex_info('Bitte wechseln Sie auf die Master-Website (ID = 1) um Ihre Websites verwalten zu können.');
} elseif ($func == '') {
	$query = 'SELECT * FROM rex_website';

	$list = rex_list::factory($query);
	$list->setNoRowsMessage('Keine Websites vorhanden');
	$list->setCaption('Liste der angelegten Websites');
	$list->addTableAttribute('summary','Auflistung aller angelegten Websites');
	$list->addTableColumnGroup(array(40, 40, '*', 240, 80, 80, 80));

	$list->removeColumn('start_article_id');
	$list->removeColumn('notfound_article_id');
	$list->removeColumn('default_template_id');
	$list->removeColumn('table_prefix');
	$list->removeColumn('protocol');
	$list->removeColumn('style_id');

	$list->setColumnLabel('id','ID');
	$list->setColumnLabel('domain', 'Domain');
	$list->setColumnLabel('title', 'Titel');
	$list->setColumnParams('domain', array('func' => 'edit', 'website_id' => '###id###'));

	// icon column
	$thIcon = '<a class="rex-i-element rex-i-generic-add" href="'. $list->getUrl(array('func' => 'add')) .'"><span class="rex-i-element-text">Issuu PDF erstellen</span></a>';
	$tdIcon = '<span class="rex-i-element rex-i-generic"><span class="rex-i-element-text">###name###</span></span>';
	$list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
	$list->setColumnParams($thIcon, array('func' => 'edit', 'website_id' => '###id###'));

	// style column
	$viewerType = 'Style';
	$list->addColumn($viewerType, '', -1, array('<th>###VALUE###</th>','<td>###VALUE###</td>'));
	$list->setColumnFormat($viewerType, 'custom',
		create_function(
			'$params',
			'global $REX;
			$list = $params["list"];

			$query =  \'SELECT name FROM rex_website_style WHERE id=\' . $list->getValue("style_id");
			$sql = new sql();
			$sql->setQuery($query);
		
			if ($sql->getRows() == 0) {
				return "Style nicht vorhanden!";
			} else {
				return $sql->getValue(\'name\');
			}'
		)
	  );

	// functions column spans 2 data-columns
	$funcs = 'Funktionen';
	$list->addColumn($funcs, 'bearbeiten', -1, array('<th colspan="2">###VALUE###</th>','<td>###VALUE###</td>'));
	$list->setColumnParams($funcs, array('func' => 'edit', 'website_id' => $website_id, 'website_id' => '###id###'));

	$delete = 'deleteCol';
	$list->addColumn($delete, 'l&ouml;schen', -1, array('','<td>###VALUE###</td>'));
	$list->setColumnParams($delete, array('website_id' => '###id###', 'func' => 'delete'));
	//$list->addLinkAttribute($delete, 'onclick', 'alert(\'Bitte löschen Sie das Issuu PDF über die Bearbeitungsansicht.\r\n\r\nKlicken Sie dazu auf den Bearbeiten-Link und dann auf den Löschen-Button unten.\'); return false;');

	$list->show();
} elseif ($func == 'add' || $func == 'edit' && $website_id > 0) {
	if ($func == 'edit') {
		$formLabel = 'Website bearbeiten';
	} else if ($func == 'add') {
		$formLabel = 'Website anlegen';
	}

	$form = rex_form::factory('rex_website', $formLabel, 'id=' . $website_id);

	$form->addErrorMessage(REX_FORM_ERROR_VIOLATE_UNIQUE_KEY, 'Eine Website mit dieser ID existiert bereits!');

	$field =& $form->addTextField('domain'); 
	$field->setLabel('Domain');

	$field =& $form->addTextField('title'); 
	$field->setLabel('Titel');

	$field =& $form->addTextField('start_article_id'); 
	$field->setLabel('Startartikel');

	$field =& $form->addTextField('notfound_article_id'); 
	$field->setLabel('Fehlerartikel');

	$field =& $form->addTextField('default_template_id'); 
	$field->setLabel('Standardtemplate');

	$field =& $form->addSelectField('protocol'); 
	$field->setLabel('Protokoll');
	$select =& $field->getSelect();
	$select->setSize(1);
	$select->addOption('http', 'http');
	$select->addOption('https', 'https');

	$field =& $form->addSelectField('style_id'); 
	$field->setLabel('Style');
	$select =& $field->getSelect();
	$select->setSize(1);
	$query = 'SELECT name as label, id FROM rex_website_style';
	$select->addSqlOptions($query);

	if ($func == 'edit') {
		$form->addParam('website_id', $website_id);
	} elseif ($func == 'add') {
		$form->addParam('status', 'website_added');
	}

	$form->show();
}

echo '</div>';
