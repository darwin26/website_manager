<?php
class rex_website_theme {
	protected $id;
	protected $name;
	protected $themeSql;

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
}
