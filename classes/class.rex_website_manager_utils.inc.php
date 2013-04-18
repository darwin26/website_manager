<?php
class rex_website_manager_utils {
	public static function addFrontendLinkToMetaMenu($params) {
		global $REX, $I18N;

		// meta menu frontend link
		$params['subject'][] = '<li><a id="frontend-link" href="' . $REX['WEBSITE_MANAGER']->getCurrentWebsite()->getUrl() . '" target="_blank">' . $I18N->msg('website_manager_frontend_link') . '</a></li>';

		return $params['subject'];
	}

	public static function addToOutputFilter($params) {
		global $REX, $I18N;

		// website select
		if (rex_request('page') != 'mediapool' && rex_request('page') != 'linkmap') {
			$params['subject']  = str_replace('<div id="rex-website">', '<div id="rex-website">' . self::getWebsiteSelect(), $params['subject']);
		}

		// website name frontend link
		if ($REX['ADDON']['website_manager']['settings']['show_website_name_frontend_link']) {
			$params['subject']  = str_replace('<div id="rex-website">', '<div id="rex-website">' . self::getWebsiteNameFrontendLink(), $params['subject']);
		}

		// colorpicker
		if (rex_request('page') == 'website_manager') {
			if (rex_request('func') == 'add') {
				$color = 'color: "' . rex_website::defaultColor . '", ';
			} else {
				$color = '';
			}

			$replace = PHP_EOL . '<!-- BEGIN website_manager -->' . PHP_EOL;
			$replace .= '<link rel="stylesheet" type="text/css" href="../' . $REX['MEDIA_ADDON_DIR'] . '/website_manager/spectrum.css" />' . PHP_EOL;
			$replace .= '<script type="text/javascript" src="../' . $REX['MEDIA_ADDON_DIR'] . '/website_manager/spectrum.js"></script>' . PHP_EOL;
			$replace .= '<script type="text/javascript">jQuery("#color-picker").spectrum({ ' . $color . ' showInput: true,  preferredFormat: "hex", clickoutFiresChange: true, showPalette: true, palette: [ ["' . rex_website::defaultColor . '", "#8eb659", "#d1513c", "#cb41d2", "#dfaa3c"] ],  chooseText: "' . $I18N->msg('website_manager_website_colorpicker_choose') . '", cancelText: "' . $I18N->msg('website_manager_website_colorpicker_cancel') . '" });</script>' . PHP_EOL;
			$replace .= '<!-- END website_manager -->';

			$params['subject']  = str_replace('</body>', $replace . '</body>', $params['subject']);
		}

		// website specific favicon
		if ($REX['ADDON']['website_manager']['settings']['colorize_favicon'] && $REX['WEBSITE_MANAGER']->getCurrentWebsite()->getColor() != '') {
			$replace = '<link rel="shortcut icon" href="../' . $REX['MEDIA_ADDON_DIR'] . '/website_manager/' . $REX['WEBSITE_MANAGER']->getCurrentWebsite()->getIcon() . '" />' . PHP_EOL;

			$params['subject']  = str_replace('<link rel="shortcut icon" href="media/favicon.ico" />', $replace, $params['subject']);
		}
		
		return $params['subject'];
	}

	protected static function getWebsiteSelect() {
		global $REX;

		$websiteSelectOptions = '';

		foreach ($REX['WEBSITE_MANAGER']->getWebsites() as $website) {
			$selected = '';

			if ($REX['WEBSITE_MANAGER']->getCurrentWebsiteId() == $website->getId()) {
				$selected = 'selected="selected"';
			}

			if (isset($REX['USER']) && $REX['USER']->isAdmin() || isset($REX['USER']) && $REX['USER']->hasPerm($website->getPermission())) {
				$websiteSelectOptions .= '<option value="' . $website->getId() . '" ' . $selected . ' data-imagesrc="' . $website->getIconUrl() . '" data-description="' . $website->getTitle() . '">' . $website->getDomain() . '</option>';
			}
		}

		$websiteSelect = '
			<div id="website-select">
				<form method="post" action="index.php">
					<input type="hidden" name="page" value="' . rex_request('page') . '" />
					<input type="hidden" name="subpage" value="' . rex_request('subpage') . '" />
					<input type="hidden" name="chapter" value="' . rex_request('chapter') . '" />
					<input type="hidden" name="new_website_id" id="new_website_id" value="" />
					<fieldset>
						<select id="website-selector" size="1" name="website-selector">' . $websiteSelectOptions . '</select>			
					</fieldset>
				</form>
			</div>';

		return $websiteSelect;
	}

	protected static function getWebsiteNameFrontendLink() {
		global $REX;

		if (strlen($REX['WEBSITE_MANAGER']->getCurrentWebsite()->getTitle()) > 45) {
			$class = ' small';
		} else {
			$class = '';
		}

		return '<h1 class="website-name-frontend-link' . $class . '"><a href="' . $REX['WEBSITE_MANAGER']->getCurrentWebsite()->getUrl() . '" onclick="window.open(this.href); return false">' . $REX['SERVERNAME'] . '</a></h1>';
	}

	protected static function addJS() {
		// ddslick js
		return '
			<script type="text/javascript">
			jQuery(function($) {
				$("#website-selector").ddslick({
					truncateDescription: true,
					imagePosition: "left",
					onSelected: function(data) { 
						if (data.selectedIndex >= 0) {
							$("#new_website_id").val(data.selectedData.value); // fix as otherwise value of select field wont be accepted
							$("#website-select form").submit();
						}  
					}
				});

				$("#website-select").show();
			});
			</script>
		';
	}

	public static function fixArticlePreviewLink($params) {
		global $REX;

		$lastElement = count($params['subject']) - 1;

		$params['subject'][$lastElement] = preg_replace("/(?<=href=(\"|'))[^\"']+(?=(\"|'))/", $REX['WEBSITE_MANAGER']->getCurrentWebsite()->getUrl() . self::getTrimmedUrl(), $params['subject'][$lastElement]);

		return $params['subject'];
	}

	protected static function getTrimmedUrl($id = '', $clang = '', $params = '', $divider = '&amp;') {
		return ltrim(rex_getUrl($id, $clang, $params, $divider), "./");
	}

	public static function appendToPageHeader($params) {
		global $REX;

		$insert = '<!-- BEGIN website_manager -->' . PHP_EOL;

		// color bar
		if ($REX['ADDON']['website_manager']['settings']['show_color_bar']) { 
			$insert .= '<style>#rex-navi-logout { border-bottom: 10px solid ' . $REX['WEBSITE_MANAGER']->getCurrentWebsite()->getColor() . '; }</style>' . PHP_EOL;
		}

		// color of links in website select box
		$insert .= '<style>.dd-selected-text { color: ' . $REX['WEBSITE_MANAGER']->getCurrentWebsite()->getColor() . '; }</style>' . PHP_EOL;

		// general css file
		$insert .= '<link rel="stylesheet" type="text/css" href="../' . $REX['MEDIA_ADDON_DIR'] . '/website_manager/website_manager.css" />' . PHP_EOL;

		// ddslick js plugin for website select box
		$insert .= '<script type="text/javascript" src="../' . $REX['MEDIA_ADDON_DIR'] . '/website_manager/jquery.ddslick.min.js"></script>' . PHP_EOL;

		// js inits and stuff
		$insert .= self::addJS();

		$insert .= '<!-- END website_manager -->';
	
		return $params['subject'] . PHP_EOL . $insert;
	}

	public static function createDynFile($file) {
		$fileHandle = fopen($file, 'w');

		fwrite($fileHandle, "<?php\r\n");
		fwrite($fileHandle, "// --- DYN\r\n");
		fwrite($fileHandle, "// --- /DYN\r\n");

		fclose($fileHandle);
	}

	public static function sanitizeUrl($url) {
		return preg_replace('@^https?://|/.*|[^\w.-]@', '', $url);
	}

	public static function getFormValues($form, $fields) {
		$values = array();

		$elements = $form->getFieldsetElements();

		foreach($elements as $fieldsetName => $fieldsetElements) {
			foreach($fieldsetElements as $field) {
				if (in_array($field->getFieldName(), $fields)) {
					$values[$field->getFieldName()] = $field->getValue();
				}
			}
		}

		return $values;
	}

	public static function getLastInsertedId($sql) {
		return $sql->last_insert_id;
	}

	public static function rrmdir($dir) {
		foreach(glob($dir . '/*') as $file) {
		    if (is_dir($file)) {
		        self::rrmdir($file);
			} else {
		        unlink($file);
			}
		}

		rmdir($dir);
	}

	public static function initPrioSwitch() {
		global $REX;

		// include main class
		if (!class_exists('rex_prio_switch')) {
			include($REX['INCLUDE_PATH'] . '/addons/website_manager/classes/class.rex_prio_switch.inc.php');
		}

		// include extended class for use in this addon
		include($REX['INCLUDE_PATH'] . '/addons/website_manager/classes/class.rex_website_manager_prio_switch.inc.php');

		// for ajax call: update prio in db if necessary
		rex_website_manager_prio_switch::handleAjaxCall('update_websites_prio', 'rex_website', 'id', false);
	}

	public static function hex2rgb($hex) {
	   $hex = str_replace("#", "", $hex);

	   if(strlen($hex) == 3) {
		  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
		  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
		  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
		  $r = hexdec(substr($hex,0,2));
		  $g = hexdec(substr($hex,2,2));
		  $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);
	   //return implode(",", $rgb); // returns the rgb values separated by commas
	   return $rgb; // returns an array with the rgb values
	}

	public static function rgb2hex($rgb) {
	   $hex = "#";
	   $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

	   return $hex; // returns the hex value including the number sign (#)
	}
}
