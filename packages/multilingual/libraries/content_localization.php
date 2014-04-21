<?php defined('C5_EXECUTE') or die("Access Denied.");

class MultilingualContentLocalization {

	public function getLanguages() {
		$languages = array();
		foreach(Zend_Locale::getTranslationList('language', ACTIVE_LOCALE) as $key => $name) {
			if(strpos($key, '_') === false) {
				$languages[$key] = $name;
			}
		}
		return $languages;
	}
}
