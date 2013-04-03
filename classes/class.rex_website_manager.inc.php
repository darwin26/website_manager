<?php
class rex_website_manager {
	protected $websites;
	protected $currentWebsiteId;
	
	public function __construct() {
		$this->websites = array();
		$this->currentWebsiteId = 1;
	}

	public function setCurrentWebsiteId($id) {
		$this->currentWebsiteId = $id;
	}

	public function getCurrentWebsiteId() {
		return $this->currentWebsiteId;
	}

	public function getWebsiteCount() {
		return count($this->websites);
	}

	function addWebsite($site) {
		$this->websites[$site->getId()] = $site;
	}

	public function getWebsites() {
		return $this->websites;
	}

	public function getWebsite($id) {
		 return $this->websites[$id];
	}

	public function getCurrentWebsite() {
		return $this->websites[$this->currentWebsiteId];
	}

	public function init() {
		global $REX;

		if (rex_request('rex_img_file') != '') {
			// at the moment: all image manager files will be in first dir if backend
			$websiteId = $this->getWebsiteIdForFrontend();
		} else {
			if ($REX['REDAXO']) {
				// backend
				$websiteId = $this->getWebsiteIdForBackend();
			} else {
				// frontend
				$websiteId = $this->getWebsiteIdForFrontend();
			}
		}

		$this->setCurrentWebsiteId($websiteId);
		$this->setRexVars();
		$this->includeClangFile();
	}

	protected function includeClangFile() {
		global $REX;

		require($REX['INCLUDE_PATH'] . '/addons/website_manager/' . $this->getCurrentWebsite()->getClangFile());
	}

	protected function getWebsiteIdForFrontend() {
		foreach ($this->websites as $website) {
			if ($website->getDomain() == $_SERVER['SERVER_NAME']) {
				// found!				
				return $website->getId();
			}
		}

		// not found. return id of first website
		$websiteId = rex_website::firstId;
	}

	protected function getWebsiteIdForBackend() {
		$this->initSessionVar();

		return rex_session('current_website_id');
	}

	protected function initSessionVar() {
		if (session_id() == '') {
	    	session_start();
		}

		if (rex_request('new_website_id') >= rex_website::firstId) {
			// user switched website
			rex_set_session('current_website_id', rex_request('new_website_id'));
		} elseif (rex_session('current_website_id') < rex_website::firstId) {
			// first time running
			rex_set_session('current_website_id', rex_website::firstId);
		} else {
			// session var is set correctly, nothing todo
		}
	}

	protected function setRexVars() {
		global $REX;

		$curWebsite = $this->getCurrentWebsite();

		$REX['SERVER'] = $curWebsite->getUrl();
		$REX['SERVERNAME'] = $curWebsite->getTitle();
		$REX['START_ARTICLE_ID'] = $curWebsite->getStartArticleId();
		$REX['NOTFOUND_ARTICLE_ID'] = $curWebsite->getNotFoundArticleId();
		$REX['DEFAULT_TEMPLATE_ID'] = $curWebsite->getDefaultTemplateId();
		$REX['MEDIA_DIR'] = $curWebsite->getMediaDir();
		$REX['GENERATED_PATH'] = realpath($REX['HTDOCS_PATH'] . 'redaxo/include/' . $curWebsite->getGeneratedDir()); // path needs to exist, otherwise realpath won't return correct path
		$REX['MEDIAFOLDER'] = realpath($REX['HTDOCS_PATH'] . $curWebsite->getMediaDir());
		$REX['TABLE_PREFIX'] = $curWebsite->getTablePrefix();
	}

	public static function updateInitFile() {
		global $REX;

		$initFile = $REX['INCLUDE_PATH'] . '/addons/website_manager/init.inc.php';
		$initContent = '';

		if (!file_exists($initFile)) {
			rex_website_manager_utils::createDynFile($initFile);
		}

		$sql = rex_sql::factory();
		//$sql->debugsql = true;
		$sql->setQuery('SELECT * from rex_website');

		for ($i = 0; $i < $sql->getRows(); $i++) {
			$initContent .= '$REX[\'WEBSITE_MANAGER\']->addWebsite(new rex_website(' . $sql->getValue('id') . ', \'' . $sql->getValue('domain') . '\', \'' . $sql->getValue('title') . '\', ' . $sql->getValue('start_article_id') . ', ' . $sql->getValue('notfound_article_id') . ', ' . $sql->getValue('default_template_id') . ', $websiteStyles[' . $i . '], \'' . $sql->getValue('table_prefix') . '\', \'' . $sql->getValue('protocol') . '\'));' . PHP_EOL;
			$sql->next();	
		}

	  	rex_replace_dynamic_contents($initFile, $initContent);
	}

	public static function fixClang($params) {
		global $REX;

		// get clangs from db
		$sql = new rex_sql();
		//$sql->debugsql = true;
		$sql->setQuery('SELECT * FROM `' . $REX['TABLE_PREFIX'] . 'clang`');

		unset($REX['CLANG']);

		for ($i = 0; $i < $sql->getRows(); $i++) {
			$REX['CLANG'][$sql->getValue('id')] = $sql->getValue('name');

			$sql->next();
		}

		// write clangs to website specific clang file
		$clangFile = $REX['INCLUDE_PATH'] . '/addons/website_manager/' . $REX['WEBSITE_MANAGER']->getCurrentWebsite()->getClangFile();

		if (!file_exists($clangFile)) {
			rex_website_manager_utils::createDynFile($clangFile);
		}

	  	rex_replace_dynamic_contents($clangFile, "\$REX['CLANG'] = ". var_export($REX['CLANG'], TRUE) .";\n");
	}

	public static function createClangFile($websiteId) {
		global $REX;

		$clangFile = $REX['INCLUDE_PATH'] . '/addons/website_manager/' . rex_website::constructClangFile($websiteId);

		if (!file_exists($clangFile)) {
			rex_website_manager_utils::createDynFile($clangFile);
		}

	  	rex_replace_dynamic_contents($clangFile, "\$REX['CLANG'] = array (0 => 'deutsch');");
	}
}



