<?php
class rex_website {
	protected $id;
	protected $domain;
	protected $title;
	protected $startArticleId;
	protected $notFoundArticleId;
	protected $defaultTemplateId;
	protected $color;
	protected $tablePrefix;
	protected $protocol;
	protected $permission;

	const firstId = 1;
	const mediaDir = 'files';
	const generatedDir = 'generated';
	const tablePrefix = 'rex_';
	const permissionPrefix = 'website';
	const defaultColor = '#47a0ce';
	const defaultProtocol = 'http';

	public function __construct($id, $domain, $title, $startArticleId, $notFoundArticleId, $defaultTemplateId, $color, $tablePrefix = self::tablePrefix, $protocol = self::defaultProtocol) {
		$this->id = $id;
		$this->domain = $domain;
		$this->title = $title;
		$this->startArticleId = $startArticleId;
		$this->notFoundArticleId = $notFoundArticleId;
		$this->defaultTemplateId = $defaultTemplateId;
		$this->color = $color;
		$this->tablePrefix = $tablePrefix;
		$this->protocol = $protocol;
		$this->permission = self::permissionPrefix . '[' . $domain . ']';
	}

	public function getId() {
		return $this->id;
	}

	public function getDomain() {
		return $this->domain;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getStartArticleId() {
		return $this->startArticleId;
	}

	public function getNotFoundArticleId() {
		return $this->notFoundArticleId;
	}

	public function getDefaultTemplateId() {
		return $this->defaultTemplateId;
	}

	public function getGeneratedDir() {
		return self::constructGeneratedDir($this->id);
	}

	public function getMediaDir() {
		return self::constructMediaDir($this->id);
	}

	public function getClangFile() {
		return self::constructClangFile($this->id);
	}

	public function getTablePrefix() {
		return $this->tablePrefix;
	}

	public function getProtocol() {
		return $this->protocol;
	}

	public function getUrl() {
		return $this->getProtocol() . '://' . $this->getDomain() . '/';
	}

	public function getIcon() {
		return self::constructIconFile($this->color);
	}

	public function getIconUrl() {
		global $REX;

		return '../' . $REX['MEDIA_ADDON_DIR'] . '/website_manager/' . $this->getIcon();
	}

	public function getColor() {
		return $this->color;
	}

	public function getPermission() {
		return $this->permission;
	}

	public function generateAll() {
		global $REX;

		// backup rex var
		$tempGeneratedPath = $REX['GENERATED_PATH'];

		// set path for current website
		$REX['GENERATED_PATH'] = realpath($REX['HTDOCS_PATH'] . 'redaxo/include/' . $this->getGeneratedDir());

		// generate all
		rex_generateAll();

		// restore rex var
		$REX['GENERATED_PATH'] = $tempGeneratedPath;
	}

	public static function constructGeneratedDir($websiteId) {
		if ($websiteId == self::firstId) {
			return self::generatedDir;
		} else {
			return self::generatedDir . $websiteId;
		}
	}

	public static function constructMediaDir($websiteId) {
		global $REX;

		if ($websiteId == self::firstId || $REX['WEBSITE_MANAGER_SETTINGS']['identical_media']) {
			return self::mediaDir;
		} else {
			return self::mediaDir . $websiteId;
		}
	}

	public static function constructClangFile($websiteId) {
		global $REX;

		if ($websiteId == self::firstId || $REX['WEBSITE_MANAGER_SETTINGS']['identical_clangs']) {
			return 'init.clang.inc.php';
		} else {
			return 'init.clang' . $websiteId . '.inc.php';
		}
	}

	public static function constructIconFile($color) {
		return 'favicon-' . ltrim($color, '#') . '.png';
	}

	public static function constructTablePrefix($websiteId) {
		if ($websiteId == self::firstId) {
			return self::tablePrefix;
		} else {
			return str_replace('_', $websiteId . '_', self::tablePrefix);
		}
	}
}
