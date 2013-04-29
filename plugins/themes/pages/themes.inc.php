<?php

if (!class_exists('scssc')) {
	require_once($REX['INCLUDE_PATH'] . '/addons/website_manager/plugins/themes/classes/class.scss.inc.php');
}

$func = rex_request('func', 'string');
$theme_id = rex_request('theme_id', 'int');

// delete theme (when link clicked from rex list)
if($func == 'delete' && $theme_id > 0) {
	$sql = rex_sql::factory();
	//  $sql->debugsql = true;
	$sql->setTable('rex_website_theme');
	$sql->setWhere('id='. $theme_id . ' LIMIT 1');

	if ($sql->delete()) {
		echo rex_info($I18N->msg('website_manager_theme_deleted'));
	} else {
		echo rex_warning($sql->getErrro());
	}

	// delete css file
	$cssFile = rex_website_theme::constructCSSFileWithPathForBackend($theme_id);

	if (file_exists($cssFile)) {
		unlink($cssFile);
	}
	
	$func = '';
}

// add or edit theme (after form submit)
rex_register_extension('REX_FORM_SAVED', function ($params) {
	global $REX;

	// get theme id
	if (isset($params['form']->params['theme_id'])) {
		// edit
		$themeId = $params['form']->params['theme_id'];
	} else {
		// add
		$themeId = rex_website_manager_utils::getLastInsertedId($params['sql']);
	}

	// vars
	$scssPhpFile = $REX['INCLUDE_PATH'] . '/addons/website_manager/plugins/themes/css/' . $REX['ADDON']['themes']['settings']['theme_file'];
	$cssFile = rex_website_theme::constructCSSFileWithPathForBackend($themeId);

	// get sql for scss php file
	$theme = rex_sql::factory();
	$theme->setQuery('SELECT * FROM rex_website_theme WHERE id = ' . $themeId);

	// interpret php to scss
	ob_start();
	include($scssPhpFile);
	$interpretedPhp = ob_get_contents();
	ob_end_clean();

	// compile scss to css
	try {
		$scss = new scssc();
		$scss->setFormatter('scss_formatter');
		$compiledScss = $scss->compile($interpretedPhp);
	} catch (Exception $e) {
		echo "<strong>SCSS Compile Error:</strong> <br/>";
        echo $e->getMessage();
		exit;
    }	

	// write css
	$fileHandle = fopen($cssFile, 'w');
	fwrite($fileHandle, $compiledScss);
	fclose($fileHandle);

	// use exit statement, if you want to debug
	return true;
});

// delete theme (after form submit)
rex_register_extension('REX_FORM_DELETED', function ($params) {
	global $REX;

	$themeId = $params['form']->params['theme_id'];
	$cssFile = rex_website_theme::constructCSSFileWithPathForBackend($themeId);

	if (file_exists($cssFile)) {
		unlink($cssFile);
	}

	// use exit statement, if you want to debug
	return true;
});

// output
echo '<div class="rex-addon-output-v2">';

if ($func == '') {
	// rex list
	$query = 'SELECT * FROM rex_website_theme ORDER BY id';

	$list = rex_list::factory($query);
	$list->setNoRowsMessage($I18N->msg('website_manager_theme_no_sytles_available'));
	$list->setCaption($I18N->msg('website_manager_theme_list_of_themes'));
	$list->addTableAttribute('summary', $I18N->msg('website_manager_theme_list_of_themes'));
	$list->addTableColumnGroup(array(40, 40, '*', 80, 80));

	$list->removeColumn('icon');
	$list->removeColumn('color1');

	$list->setColumnLabel('id', $I18N->msg('website_manager_website_id'));
	$list->setColumnLabel('name', $I18N->msg('website_manager_theme_name'));
	$list->setColumnParams('name', array('func' => 'edit', 'theme_id' => '###id###'));

	// icon column
	$thIcon = '<a class="rex-i-element rex-i-generic-add" href="'. $list->getUrl(array('func' => 'add')) .'"><span class="rex-i-element-text">' . $I18N->msg('website_manager_theme_create') . '</span></a>';
	$tdIcon = '<span class="rex-i-element rex-i-generic"><span class="rex-i-element-text">###name###</span></span>';
	$list->addColumn($thIcon, $tdIcon, 0, array('<th class="rex-icon">###VALUE###</th>','<td class="rex-icon">###VALUE###</td>'));
	$list->setColumnParams($thIcon, array('func' => 'edit', 'theme_id' => '###id###'));

	// functions column spans 2 data-columns
	$funcs = $I18N->msg('website_manager_theme_functions');
	$list->addColumn($funcs, $I18N->msg('website_manager_theme_edit'), -1, array('<th colspan="2">###VALUE###</th>','<td>###VALUE###</td>'));
	$list->setColumnParams($funcs, array('func' => 'edit', 'theme_id' => $theme_id, 'theme_id' => '###id###'));

	$delete = 'deleteCol';
	$list->addColumn($delete, $I18N->msg('website_manager_theme_delete'), -1, array('','<td>###VALUE###</td>'));
	$list->setColumnParams($delete, array('theme_id' => '###id###', 'func' => 'delete'));
	$list->addLinkAttribute($delete, 'onclick', 'return confirm(\'' . $I18N->msg('website_manager_theme_delete_confirm') . '\');');

	$list->show();
} elseif ($func == 'add' || $func == 'edit' && $theme_id > 0) {
	// rex form
	if ($func == 'edit') {
		$formLabel = $I18N->msg('website_manager_theme_theme_edit');
	} elseif ($func == 'add') {
		$formLabel = $I18N->msg('website_manager_theme_theme_add');
	}

	$form = rex_form::factory('rex_website_theme', $formLabel, 'id=' . $theme_id);
	$form->addErrorMessage(REX_FORM_ERROR_VIOLATE_UNIQUE_KEY, $I18N->msg('website_manager_theme_theme_exists'));

	// name
	$field =& $form->addTextField('name'); 
	$field->setLabel($I18N->msg('website_manager_theme_name')); 

	// color1
	$field =& $form->addTextField('color1'); 
	$field->setLabel($I18N->msg('website_manager_theme_color1'));
	$field->setAttribute('class', 'colorpicker');
	$field->setAttribute('style', 'visibility: hidden; height: 20px;');

	// add here more stuff

	if ($func == 'edit') {
		$form->addParam('theme_id', $theme_id);
	} elseif ($func == 'add') {
		// do nothing
	}

	// show generated css file
	$msg = rex_website_theme::constructCSSFileWithPathForFrontend($theme_id);

	if ($func == 'edit') {
		if (!file_exists(rex_website_theme::constructCSSFileWithPathForBackend($theme_id))) {
			$msg .= ' ' . $I18N->msg('website_manager_theme_css_not_found');
		}
	} elseif ($func == 'add') {
		$msg = rex_website_theme::constructCSSFileWithPathForFrontend('X');
	}

	$field =& $form->addReadOnlyField('css_file', $msg);
	$field->setLabel($I18N->msg('website_manager_theme_css_file'));

	$form->show();
}

echo '</div>';
?>

<link rel="stylesheet" type="text/css" href="../<?php echo $REX['MEDIA_ADDON_DIR']; ?>/website_manager/spectrum.css" />
<script type="text/javascript" src="../<?php echo $REX['MEDIA_ADDON_DIR']; ?>/website_manager/spectrum.js"></script>
<script type="text/javascript">jQuery(".colorpicker").spectrum({ showInput: true,  preferredFormat: "hex", clickoutFiresChange: true, showPalette: false, /* palette: [ ["#d1513c", "#8eb659", "#dfaa3c", "#cb41d2"] ], */ chooseText: "<?php echo $I18N->msg('website_manager_website_colorpicker_choose'); ?>", cancelText: "<?php echo $I18N->msg('website_manager_website_colorpicker_cancel'); ?>" });</script>

