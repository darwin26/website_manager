<?php
class rex_website {
	protected $id;
	protected $domain;
	protected $title;
	protected $startArticleId;
	protected $notFoundArticleId;
	protected $defaultTemplateId;
	protected $dbName;
	protected $style;
	protected $tablePrefix;
	protected $protocol;

	const firstId = 1;
	const mediaDir = 'files';
	const generatedDir = 'generated';
	const settingsFile = 'settings.inc.php';

	public function __construct($id, $domain, $title, $startArticleId, $notFoundArticleId, $defaultTemplateId, $dbName, $style, $tablePrefix = 'rex_', $protocol = 'http') {
		$this->id = $id;
		$this->domain = $domain;
		$this->title = $title;
		$this->startArticleId = $startArticleId;
		$this->notFoundArticleId = $notFoundArticleId;
		$this->defaultTemplateId = $defaultTemplateId;
		$this->dbName = $dbName;
		$this->style = $style;
		$this->tablePrefix = $tablePrefix;
		$this->protocol = $protocol;
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
		if ($this->id == self::firstId) {
			// first website has normal generated dir
			return self::generatedDir;
		} else {
			// all other have generated2, generated3, etc.
			return self::generatedDir . $this->id;
		}
	}

	public function getMediaDir() {
		if ($this->id == self::firstId) {
			return self::mediaDir;
		} else {
			return self::mediaDir . $this->id;
		}
	}

	public function getSettingsFile() {
		if ($this->id == self::firstId) {
			return self::settingsFile;
		} else {
			return str_replace('.inc.php', '' . $this->id . '.inc.php', self::settingsFile);
		}
	}

	public function getDatabaseName() {
		return $this->dbName;
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
}
