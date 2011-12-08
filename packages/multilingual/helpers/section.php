<?

Loader::Model('section', 'multilingual');

class SectionHelper {

	protected $lang = false;
	protected $section = false;
	
	/**
	 * if no argument is specified, returns the first portion of the current page's path
	 * ex: path is /en/about/contact-us this function would return 'en'
	 * if $s argument (a string page path) is specified this function tests to see if the current page is within the path 
	 * @param string $s 
	 * @return string | boolean
	 */
	public function section($s = false) {
		if (!$this->section) {
			$c = Page::getCurrentPage();
			$cparts = explode('/', $c->getCollectionPath());
			$this->section = $cparts[1];
		}
		if ($s == false) {
			return $this->section;
		} else {
			return $s == $this->section;
		}
	}
	
	/**
	 * returns the current language
	 * @return string
	 * @deprecated
	 */
	public function getLanguage() {
		return self::getLocale();
	}
	
	public function getLocale() {
		$ms = MultilingualSection::getCurrentSection();
		if (is_object($ms)) {
			$lang = $ms->getLocale();
		} else {
			$lang = Loader::helper('default_language','multilingual')->getSessionDefaultLocale();
		}
		$_SESSION['DEFAULT_LOCALE'] = $lang;
		return $lang;
	}
}