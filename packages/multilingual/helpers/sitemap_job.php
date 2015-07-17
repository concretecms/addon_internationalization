<?php defined('C5_EXECUTE') or die('Access Denied.');

class SitemapJobHelper {

	/** Can we use the standard sitemap.xml generation job?
	* @return bool
	*/
	public function canUseStandardSitemapJob() {
		if(version_compare(APP_VERSION, '5.6.3') >= 0) {
			$job = Job::getByHandle('generate_sitemap');
			if(is_object($job)) {
				switch($job->getJobStatus()) {
					case 'ENABLED':
					case 'RUNNING':
					case 'ERROR':
						return true;
				}
			}
		}
		return false;
	}

	/** Extends a sitemap node by adding multilingual-related info
	* @param $xmlNode SimpleXMLElement
	* @param $page Page
	*/
	public function extendSitemapNode($xmlNode, $page) {
		static $tp;
		if(!isset($tp)) {
			$tp = Loader::helper('translated_pages', 'multilingual');
		}
		$translatedPages = $tp->getTranslatedPages($page);
		if(count($translatedPages)) {
			$rootNode = current($xmlNode->xpath('/*'));
			if(empty($rootNode['xmlns:xhtml'])) {
				$rootNode['xmlns:xhtml'] = 'http://www.w3.org/1999/xhtml';
			}
		}
		foreach($translatedPages as $locale => $translatedPage) {
			$altMeta = $tp->getAltMeta($locale, $translatedPage, 'xmlns:xhtml:link');
			$xmlSubNode = $xmlNode->addChild(array_shift($altMeta));
			foreach($altMeta as $attribute => $value) {
				$xmlSubNode->addAttribute($attribute, $value);
			}
		}
	}
}
