<?php defined('C5_EXECUTE') or die("Access Denied.");

class MultilingualContentLocalization {

	public function getLanguages() {
		$r = Zend_Locale::getTranslationList('language',ACTIVE_LOCALE);
		return $r;
	}		
}