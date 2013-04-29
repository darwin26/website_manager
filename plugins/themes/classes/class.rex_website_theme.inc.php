<?php
class rex_website_theme {
	protected $id;
	protected $name;
	protected $themeSql;

	const cssFileStart = 'theme';

	public function __construct($id) {
		$this->id = $id;

		// gets set by $this->init() called from website manager
		$this->name = 'undifiend';
		$this->themeSql = null;
	}

	public function getValue($key) {
		if ($this->themeSql != null) {
			return $this->themeSql->getValue($key);
		} else {
			return '';
		}
	}

	public function init() {
		if ($this->id > 0) {
			$sql = rex_sql::factory();
			//$sql->debugsql = true;
			$sql->setQuery('SELECT * FROM rex_website_theme WHERE id = ' . $this->id);

			if ($sql->getRows() > 0) {
				$this->themeSql = $sql;
			}
		}
	}

	public function getCSSFile() {
		return self::constructCSSFile($this->id);
	}

	public function getCSSFileWithPath() {
		return self::constructCSSFileWithPathForFrontend($this->id);
	}

	public static function constructCSSFile($themeId) {
		return self::cssFileStart . $themeId . '.css';
	}

	public static function constructCSSFileWithPathForBackend($themeId) {
		global $REX;

		return $REX['FRONTEND_PATH'] .self::constructCSSFileWithPathForFrontend($themeId);
	}

	public static function constructCSSFileWithPathForFrontend($themeId) {
		global $REX;

		return '/' . trim($REX['ADDON']['themes']['settings']['css_dir'], '/') . '/' . self::constructCSSFile($themeId);
	}
}
