<?php

$func = rex_request('func', 'string');
$style_id = rex_request('style_id', 'int');

// delete issuu pdf
if($func == 'delete' && $style_id > 0) {
	$sql = rex_sql::factory();
	//  $sql->debugsql = true;
	$sql->setTable('rex_website_style');
	$sql->setWhere('id='. $style_id . ' LIMIT 1');

	if ($sql->delete()) {
		// update init file to reflect changes
		$REX['WEBSITE_MANAGER']->updateInitFile();

		echo rex_info($I18N->msg('website_manager_style_deleted'));
	} else {
		echo rex_warning($sql->getErrro());
	}
	
	$func = '';
}

// add or edit style
rex_register_extension('REX_FORM_SAVED', function ($params) {
	global $REX;

	// update init file to reflect changes
	$REX['WEBSITE_MANAGER']->updateInitFile();

	return true;
});

// delete style
rex_register_extension('REX_FORM_DELETED', function ($params) {
	global $REX;

	// update init file to reflect changes
	$REX['WEBSITE_MANAGER']->updateInitFile();

	return true;
});

// output
echo '<div class="rex-addon-output-v2">';

if ($func == '') {
	$query = 'SELECT * FROM rex_website_style ORDER BY id';

	$list = rex_list::factory($query);
	$list->setNoRowsMessage($I18N->msg('website_manager_style_no_sytles_available'));
	$list->setCaption($I18N->msg('website_manager_style_list_of_styles'));
	$list->addTableAttribute('summary', $I18N->msg('website_manager_style_list_of_styles'));
	$list->addTableColumnGroup(array(40, '*', 80, 80));

	$list->removeColumn('id');
	$list->removeColumn('icon');
	$list->removeColumn('color');

	$list->setColumnLabel('name', $I18N->msg('website_manager_style_name'));
	$list->setColumnParams('name', array('func' => 'edit', 'style_id' => '###id###'));

	// icon column
	$thIcon = '<a class="rex-i-element rex-i-generic-add" href="'. $list->getUrl(array('func' => 'add')) .'"><span class="rex-i-element-text">' . $I18N->msg('website_manager_style_create') . '</span></a>';
	$tdIcon = '<span class="rex-i-element rex-i-generic"><span class="rex-i-element-text">###name###</span></span>';
	$list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
	$list->setColumnParams($thIcon, array('func' => 'edit', 'style_id' => '###id###'));

	// functions column spans 2 data-columns
	$funcs = $I18N->msg('website_manager_style_functions');
	$list->addColumn($funcs, $I18N->msg('website_manager_style_edit'), -1, array('<th colspan="2">###VALUE###</th>','<td>###VALUE###</td>'));
	$list->setColumnParams($funcs, array('func' => 'edit', 'style_id' => $style_id, 'style_id' => '###id###'));

	$delete = 'deleteCol';
	$list->addColumn($delete, $I18N->msg('website_manager_style_delete'), -1, array('','<td>###VALUE###</td>'));
	$list->setColumnParams($delete, array('style_id' => '###id###', 'func' => 'delete'));
	$list->addLinkAttribute($delete, 'onclick', 'return confirm(\'' . $I18N->msg('website_manager_style_delete_confirm') . '\');');

	$list->show();
} elseif ($func == 'add' || $func == 'edit' && $style_id > 0) {
	if ($func == 'edit') {
		$formLabel = $I18N->msg('website_manager_style_style_edit');
	} elseif ($func == 'add') {
		$formLabel = $I18N->msg('website_manager_style_style_add');
	}

	$form = rex_form::factory('rex_website_style', $formLabel, 'id=' . $style_id);

	$form->addErrorMessage(REX_FORM_ERROR_VIOLATE_UNIQUE_KEY, $I18N->msg('website_manager_style_style_exists'));

	$field =& $form->addTextField('name'); 
	$field->setLabel($I18N->msg('website_manager_style_name'));

	$field =& $form->addTextField('icon'); 
	$field->setLabel($I18N->msg('website_manager_style_icon'));

	$field =& $form->addTextField('color'); 
	$field->setLabel($I18N->msg('website_manager_style_color'));

	if ($func == 'edit') {
		$form->addParam('style_id', $style_id);
	} elseif ($func == 'add') {
		// do nothing
	}

	$form->show();
}

echo '</div>';
