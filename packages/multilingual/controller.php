<?php defined('C5_EXECUTE') or die(_("Access Denied."));

class MultilingualPackage extends Package {

	protected $pkgHandle = 'multilingual';
	protected $appVersionRequired = '5.4.2';
	protected $pkgVersion = '1.1dev';
	
	public function getPackageDescription() {
		return t('Translate your site with this free multilingual solution.');
	}
	
	public function getPackageName() {
		return t('Internationalization');
	}
	
	public function on_start() {
		define('DIRNAME_IMAGES_LANGUAGES', 'flags');
		
		// checks to see if the user should be redirected to the default language home page instead of the / home page.
		Events::extend('on_start', 'DefaultLanguageHelper', 'checkDefaultLanguage', 'packages/' . $this->pkgHandle . '/helpers/default_language.php');
		
		// adds the site translation files to the translation library so strings wrapped in t('') will be translated
		Events::extend('on_start', 'DefaultLanguageHelper', 'setupSiteInterfaceLocalization', 'packages/' . $this->pkgHandle . '/helpers/default_language.php');
		
		Events::extend('on_page_get_icon',
			'InterfaceFlagHelper',
			'getDashboardSitemapIconSRC',
			'packages/'.$this->pkgHandle.'/helpers/interface/flag.php'
			);

		Events::extend('on_page_delete',
			'MultilingualSection',
			'assignDelete',
			'packages/'.$this->pkgHandle.'/models/section.php'
			);

		Events::extend('on_page_duplicate',
			'MultilingualSection',
			'assignDuplicate',
			'packages/'.$this->pkgHandle.'/models/section.php'
			);

		Events::extend('on_page_add',
			'MultilingualSection',
			'assignAdd',
			'packages/'.$this->pkgHandle.'/models/section.php'
			);

		Events::extend('on_page_move',
			'MultilingualSection',
			'assignMove',
			'packages/'.$this->pkgHandle.'/models/section.php'
			);

		// add the header menu item
		$ihm = Loader::helper('concrete/interface/menu');
		Loader::model('section', 'multilingual');		
		$uh = Loader::helper('concrete/urls');
		$ihm->addPageHeaderMenuItem('multilingual', false, 'right', array(
				'dialog-title' => t('Multilingual Pages'),
			'href' => $uh->getToolsUrl('switch_language_for_page', 'multilingual'),
			'dialog-on-open' => "$(\'#ccm-page-edit-nav-multilingual\').removeClass(\'ccm-nav-loading\')",
			'dialog-width' => '400',
			'dialog-height' => "300",
			'dialog-modal' => "false",
			'class' => 'dialog-launch'
		), 'multilingual');
	}
	

	
	public function install() {
		$pkg = parent::install();
		
		Loader::model('single_page');

		
		$p = SinglePage::add('/dashboard/multilingual',$pkg);
		if (is_object($p)) {
			$p->update(array('cName'=>t('Multilingual'), 'cDescription'=>t('Translate your site.')));
		}
		
		$p1 = SinglePage::add('/dashboard/multilingual/setup', $pkg);
		if (is_object($p1)) {
			$p1->update(array('cName'=>t('Setup'), 'cDescription'=>''));
		}
		$p2 = SinglePage::add('/dashboard/multilingual/page_report', $pkg);
		if (is_object($p2)) {
			$p2->update(array('cName'=>t('Page Report'), 'cDescription'=>''));
		}
		BlockType::installBlockTypeFromPackage('switch_language', $pkg);

	}
}
