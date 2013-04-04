<?php

class rex_website_manager_prio_switch extends rex_prio_switch {
	public static function handleAjaxCall($func, $table, $idField, $useLike) {
		global $REX;

		self::$ajaxFunctionName = $func;
		
		if (rex_request('func') == self::$ajaxFunctionName) {
			// update prio in db
			self::updatePrio(rex_request('order'), $table, $idField, $useLike);

			// update init file to reflect changes
			$REX['WEBSITE_MANAGER']->updateInitFile();
		}
	}
}
