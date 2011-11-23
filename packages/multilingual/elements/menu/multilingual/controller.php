<?
defined('C5_EXECUTE') or die("Access Denied.");
class MultilingualConcreteInterfaceMenuItemController extends ConcreteInterfaceMenuItemController {
	
	public function displayItem() {
		$sect = MultilingualSection::getCurrentSection();
		return (is_object($sect));
	}
	
	public function getMenuLinkHTML() {
		$sect = MultilingualSection::getCurrentSection();
		$ih = Loader::helper('interface/flag', 'multilingual');
		$icon = $ih->getFlagIcon($sect->getIcon(), true);
		$class = 'ccm-page-edit-nav-multilingual-flag';
		if (!$icon) {
			$icon = $this->menuItem->getMenuItemFileURL('generic_language.png');
			$class = 'ccm-page-edit-nav-multilingual-no-flag';
		}
		$page = Page::getCurrentPage();
		$this->menuItem->linkAttributes['href'] .= '?cID=' . $page->getCollectionID();
		$this->menuItem->linkAttributes['class'] = $class;
		$this->menuItem->linkAttributes['style'] = 'background-image: url(' . $icon . ')';
		$this->menuItem->setName($sect->getLanguageText());
		
		return parent::getMenuLinkHTML();
	}
}