<?php defined('C5_EXECUTE') or die(_("Access Denied."));

class MultilingualPackage extends Package {

	protected $pkgHandle = 'multilingual';
	protected $appVersionRequired = '5.6';
	protected $pkgVersion = '1.2dev';
	
	public function getPackageDescription() {
		return t('Translate your site with this free multilingual solution.');
	}
	
	public function getPackageName() {
		return t('Internationalization');
	}
	
	public function on_start() {
		define('DIRNAME_IMAGES_LANGUAGES', 'flags');
		if(!defined('MULTILINGUAL_FLAGS_WIDTH')) {
			/** Width of multilingual flags.
			* @var int
			*/
			define('MULTILINGUAL_FLAGS_WIDTH', 16);
		}
		if(!defined('MULTILINGUAL_FLAGS_HEIGHT')) {
			/** Height of multilingual flags.
			* @var int
			*/
			define('MULTILINGUAL_FLAGS_HEIGHT', 16);
		}
		
		// checks to see if the user should be redirected to the default language home page instead of the / home page.
		Events::extend('on_start', 'DefaultLanguageHelper', 'checkDefaultLanguage', 'packages/' . $this->pkgHandle . '/helpers/default_language.php');
		
		// adds the site translation files to the translation library so strings wrapped in t('') will be translated
		Events::extend('on_start', 'DefaultLanguageHelper', 'setupSiteInterfaceLocalization', 'packages/' . $this->pkgHandle . '/helpers/default_language.php');

		// Ensure's the language tags are set in the header
		//Events::extend('on_start', 'TranslatedPagesHelper', 'addMetaTags', 'packages/' . $this->pkgHandle . '/helpers/translated_pages.php');
		
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

		if(defined('MULTILINGUAL_ADD_ALTERNATE_HREFLANG') && MULTILINGUAL_ADD_ALTERNATE_HREFLANG) {
			Events::extend('on_page_view', __CLASS__, 'addAlternateHrefLang', __FILE__);
		}

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

	public static function addAlternateHrefLang($page) {
		Loader::helper('interface/page', 'multilingual')->addAlternateHrefLang($page);
	}

	public function install() {
		$pkg = parent::install();
		
		Loader::model('single_page');
		Loader::model('job');

		// install job
		$jb = Job::installByPackage('generate_multilingual_sitemap', $pkg);

		
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
		
		$ak = CollectionAttributeKey::getByHandle('multilingual_exclude_from_copy');
		if(!is_object($ak)) {
			CollectionAttributeKey::add('BOOLEAN',array('akHandle' => 'multilingual_exclude_from_copy', 'akName' => t('Exclude from Internationalization Copy'), 'akIsSearchable' => true), $pkg);
		}
	}


	public function upgrade() {
		parent::upgrade();
		$pkg = Package::getByHandle($this->pkgHandle);
		
		//@todo write conversion from lang to locale
		//1.0 - 1.1 changed languaage to locale
		$db = Loader::db();
		// update the MultilingualSections table
		$rows = $db->getAll("SELECT * FROM MultilingualSections");
		if(is_array($rows) && count($rows)) {
			foreach($rows as $r) {
				if(strlen($r['msLanguage']) && !strlen($row['msLocale'])) {
					$locale = $r['msLanguage'].(strlen($r['msIcon'])?"_".$r['msIcon']:"");					
					$db->query("UPDATE MultilingualSections SET msLocale = ? WHERE cID = ?",array($locale, $r['cID']));
				}
			}
		}

		// install job
		Loader::model('job');
		$jb = Job::installByPackage('generate_multilingual_sitemap', $pkg);
		
		// update the MultilingualPageRelations table
		$hasLocales = $db->getOne("SELECT COUNT(msLocale) FROM MultilingualSections WHERE LENGTH(msLocale)");
		if(!$hasLocales) {
			$query = "UPDATE MultilingualPageRelations mpr, MultilingualSections 
				SET mpr.mpLocale = MultilingualSections.msLocale
				WHERE mpr.mpLanguage = MultilingualSections.msLanguage";
			$db->query($query);
		}
		
		// 1.1.2
		$ak = CollectionAttributeKey::getByHandle('multilingual_exclude_from_copy');
		if(!is_object($ak)) {
			CollectionAttributeKey::add('BOOLEAN',array('akHandle' => 'multilingual_exclude_from_copy', 'akName' => t('Exclude from Internationalization Copy'), 'akIsSearchable' => true), $pkg);
		}
		
	}

}
