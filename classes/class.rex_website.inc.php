<?php
class rex_website {
	protected $id;
	protected $domain;
	protected $title;
	protected $startArticleId;
	protected $notFoundArticleId;
	protected $defaultTemplateId;
	protected $style;
	protected $tablePrefix;
	protected $protocol;
	protected $permission;

	const firstId = 1;
	const mediaDir = 'files';
	const generatedDir = 'generated';
	const tablePrefix = 'rex_';
	const clangFile = 'init.clang.inc.php';
	const permissionPrefix = 'website';

	public function __construct($id, $domain, $title, $startArticleId, $notFoundArticleId, $defaultTemplateId, $style, $tablePrefix = 'rex_', $protocol = 'http') {
		$this->id = $id;
		$this->domain = $domain;
		$this->title = $title;
		$this->startArticleId = $startArticleId;
		$this->notFoundArticleId = $notFoundArticleId;
		$this->defaultTemplateId = $defaultTemplateId;
		$this->style = $style;
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

	public function getStyle() {
		return $this->style;
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
		if ($websiteId == self::firstId) {
			return self::mediaDir;
		} else {
			return self::mediaDir . $websiteId;
		}
	}

	public static function constructClangFile($websiteId) {
		if ($websiteId == self::firstId) {
			return self::clangFile;
		} else {
			return str_replace('clang', 'clang' . $websiteId, self::clangFile);
		}
	}

	public static function constructTablePrefix($websiteId) {
		if ($websiteId == self::firstId) {
			return self::tablePrefix;
		} else {
			return str_replace('_', $websiteId . '_', self::tablePrefix);
		}
	}
}
