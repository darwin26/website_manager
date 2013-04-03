<?php
class rex_website_manager_utils {
	public static function addFrontendLinkToMetaMenu($params) {
		global $REX, $I18N;

		// meta menu frontend link
		$params['subject'][] = '<li><a id="frontend-link" href="' . $REX['WEBSITE_MANAGER']->getCurrentWebsite()->getUrl() . '" target="_blank">' . $I18N->msg('website_manager_frontend_link') . '</a></li>';

		return $params['subject'];
	}

	public static function addToOutputFilter($params) {
		global $REX;

		// website select
		if (rex_request('page') != 'mediapool' && rex_request('page') != 'linkmap') {
			$params['subject']  = str_replace('<div id="rex-website">', '<div id="rex-website">' . self::getWebsiteSelect(), $params['subject']);
		}

		// website name frontend link
		if ($REX['ADDON']['website_manager']['settings']['show_website_name_frontend_link']) {
			$params['subject']  = str_replace('<div id="rex-extra">', '<div id="rex-extra">' . self::getWebsiteNameFrontendLink(), $params['subject']);
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

			$websiteSelectOptions .= '<option value="' . $website->getId() . '" ' . $selected . ' data-imagesrc="' . $website->getStyle()->getIconUrl() . '" data-description="' . $website->getTitle() . '">' . $website->getDomain() . '</option>';
		}

		$websiteSelect = '
			<div id="website-select">
				<form method="post" action="index.php">
					<input type="hidden" name="page" value="' . rex_request('page') . '" />
					<input type="hidden" name="subpage" value="' . rex_request('subpage') . '" />
					<input type="hidden" name="chapter" value="' . rex_request('chapter') . '" />
					<input type="hidden" name="new_website_id" id="new_website_id" value="" />
					<fieldset>
						<select onchange="this.form.submit();" id="website-selector" size="1" name="website-selector">' . $websiteSelectOptions . '</select>			
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

		$curWbesiteStyle = $REX['WEBSITE_MANAGER']->getCurrentWebsite()->getStyle();

		$insert = '<!-- BEGIN website_manager -->' . PHP_EOL;

		if ($REX['ADDON']['website_manager']['settings']['show_color_bar']) { 
			$insert .= '<style>#rex-navi-logout { border-bottom: 10px solid ' . $curWbesiteStyle->getColor() . '; }</style>' . PHP_EOL;
		}

		$insert .= '<style>.dd-selected-text { color: ' . $curWbesiteStyle->getColor() . '; }</style>' . PHP_EOL;
		$insert .= '<link rel="shortcut icon" href="../' . $REX['MEDIA_ADDON_DIR'] . '/website_manager/' . $REX['WEBSITE_MANAGER']->getCurrentWebsite()->getStyle()->getIcon() . '" />' . PHP_EOL;
		$insert .= '<link rel="stylesheet" type="text/css" href="../' . $REX['MEDIA_ADDON_DIR'] . '/website_manager/website_manager.css" />' . PHP_EOL;
		$insert .= '<script type="text/javascript" src="../' . $REX['MEDIA_ADDON_DIR'] . '/website_manager/jquery.ddslick.js"></script>' . PHP_EOL;
		//$insert .= '<script type="text/javascript" src="../' . $REX['MEDIA_ADDON_DIR'] . '/website_manager/website_manager.js"></script>' . PHP_EOL;
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
}
