<?php

$func = rex_request('func', 'string');
$style_id = rex_request('style_id', 'int');

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
	$query = 'SELECT * FROM rex_website_style';

	$list = rex_list::factory($query);
	$list->setNoRowsMessage('Keine Styles vorhanden');
	$list->setCaption('Liste der angelegten Styles');
	$list->addTableAttribute('summary','Auflistung aller angelegten Styles');
	$list->addTableColumnGroup(array(40, '*', 80, 80));

	$list->removeColumn('id');
	$list->removeColumn('icon');
	$list->removeColumn('color');

	$list->setColumnLabel('name', 'Name');
	$list->setColumnParams('name', array('func' => 'edit', 'style_id' => '###id###'));

	// icon column
	$thIcon = '<a class="rex-i-element rex-i-generic-add" href="'. $list->getUrl(array('func' => 'add')) .'"><span class="rex-i-element-text">Style erstellen</span></a>';
	$tdIcon = '<span class="rex-i-element rex-i-generic"><span class="rex-i-element-text">###name###</span></span>';
	$list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
	$list->setColumnParams($thIcon, array('func' => 'edit', 'style_id' => '###id###'));

	// functions column spans 2 data-columns
	$funcs = 'Funktionen';
	$list->addColumn($funcs, 'bearbeiten', -1, array('<th colspan="2">###VALUE###</th>','<td>###VALUE###</td>'));
	$list->setColumnParams($funcs, array('func' => 'edit', 'style_id' => $style_id, 'style_id' => '###id###'));

	$delete = 'deleteCol';
	$list->addColumn($delete, 'l&ouml;schen', -1, array('','<td>###VALUE###</td>'));
	$list->setColumnParams($delete, array('style_id' => '###id###', 'func' => 'delete'));
	//$list->addLinkAttribute($delete, 'onclick', 'alert(\'Bitte löschen Sie das Issuu PDF über die Bearbeitungsansicht.\r\n\r\nKlicken Sie dazu auf den Bearbeiten-Link und dann auf den Löschen-Button unten.\'); return false;');

	$list->show();
} elseif ($func == 'add' || $func == 'edit' && $style_id > 0) {
	if ($func == 'edit') {
		$formLabel = 'Style bearbeiten';
	} elseif ($func == 'add') {
		$formLabel = 'Style anlegen';
	}

	$form = rex_form::factory('rex_website_style', $formLabel, 'id=' . $style_id);

	$form->addErrorMessage(REX_FORM_ERROR_VIOLATE_UNIQUE_KEY, 'Eine Style mit dieser ID existiert bereits!');

	$field =& $form->addTextField('name'); 
	$field->setLabel('Name');

	$field =& $form->addTextField('icon'); 
	$field->setLabel('Icon');

	$field =& $form->addTextField('color'); 
	$field->setLabel('Color');

	if ($func == 'edit') {
		$form->addParam('style_id', $style_id);
	} elseif ($func == 'add') {
		// do nothing
	}

	$form->show();
}

echo '</div>';
