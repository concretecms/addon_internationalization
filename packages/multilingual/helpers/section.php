<?

Loader::Model('section', 'multilingual');

class SectionHelper {

	protected $lang = false;
	protected $section = false;
	
	public function section($s = false) {
		if (!$this->section) {
			$c = Page::getCurrentPage();
			$cparts = explode('/', $c->getCollectionPath());
			$this->section = $cparts[2];
		}
		if ($s == false) {
			return $this->section;
		} else {
			return $s == $this->section;
		}
	}
	
	public function getLanguage() {
		$ms = MultilingualSection::getCurrentSection();
		if (is_object($ms)) {
			return $ms->getLanguage();
		} else {
			return Loader::helper('default_language','multilingual')->getSessionDefaultLanguage();
		}
	}
	
}