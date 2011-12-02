<? defined('C5_EXECUTE') or die("Access Denied.");?>

<h1><span><?=t("Multilingual Content Setup")?></span></h1>
<div class="ccm-dashboard-inner">
<h2><?=t('Content Sections')?></h2>
<? 
$nav = Loader::helper('navigation');
if (count($pages) > 0) { ?>
	<table class="ccm-results-list" style="width: auto">
	<thead>
	<tr>
		<th>&nbsp;</th>
		<th style="width: 200px"><?=t("Name")?></td>
		<th style="width: 150px"><?=t('Language')?></th>
		<th style="width: 150px"><?=t('Path')?></th>
		<th>&nbsp;</th>
	</tr>
	<? foreach($pages as $pc) { 
		$pcl = MultilingualSection::getByID($pc->getCollectionID());
		?>
		<tr>
			<td><?=$ch->getSectionFlagIcon($pc)?></td>
			<td><a href="<?=$nav->getLinkToCollection($pc)?>"><?=$pc->getCollectionName()?></a></td>
			<td><?=$pcl->getLanguageText()?></td>
			<td><?=$pc->getCollectionPath()?></td>
			<td><a href="<?=$this->action('remove_language_section', $pc->getCollectionID(), Loader::helper('validation/token')->generate())?>"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove_minus.png" /></td>
		</tr>
	<? } ?>
	</table>
	<br/><br/>
<? } else { ?>
	<p><?=t('You have not created any multilingual content sections yet.')?></p>
<? } ?>
<form method="post" action="<?=$this->action('add_content_section')?>">
	<h2><?=t('Add a Language')?></h2>
	
	<h3><?=$form->label('msLanguage', t('Choose Language'))?></h3>
	<div><?=$form->select('msLanguage', $locales);?></div>
	
	<br/>
	
	<h3><?=t('Language Icon')?></h3>
	<div id="ccm-multilingual-language-icon">
	<?=t('Choose a Language')?>
	</div>
	
	<br/>
	<h3><?=t('Choose a Parent Page')?></h3>
	<?=Loader::helper('form/page_selector')->selectPage('pageID', '')?>
	<br/>
	<?=Loader::helper('validation/token')->output('add_content_section')?>
	<?=Loader::helper('concrete/interface')->submit(t('Add Content Section'), 'add', 'left')?>
</form>

<style type="text/css">
ul.ccm-multilingual-choose-flag {list-style-type: none;}
ul.ccm-multilingual-choose-flag li img {vertical-align: middle;}

</style>

<script type="text/javascript">
$(function() {
	$("select[name=msLanguage]").change(function() {
		ccm_multilingualPopulateIcons($(this).val(), '');
	});
	ccm_multilingualPopulateIcons($("select[name=msLanguage]").val(), '<?=$_POST["msIcon"]?>');
});

ccm_multilingualPopulateIcons = function(lang, icon) {
	if (lang && lang != '') {
		$("#ccm-multilingual-language-icon").load('<?=$this->action("load_icons")?>', {'msLanguage': lang, 'selectedLanguageIcon': icon});
	}
}

</script>

</div>

<h1><span><?=t('Copy Language Tree')?></span></h1>
<div class="ccm-dashboard-inner">
<p><?=t('Copy all pages from a language to another section. This will only copy pages that have not been associated. It will not replace or remove any pages from the destination section.')?></p>
<? if (count($pages) > 1) {
	$copyLanguages = array();
	foreach($pages as $pc) {
		$pcl = MultilingualSection::getByID($pc->getCollectionID());
		$copyLanguages[$pc->getCollectionID()] = $pc->getCollectionName() . ' - ' . $pcl->getLanguageText();
	}
	$copyLanguageSelect1 = $form->select('copyTreeFrom', $copyLanguages);
	$copyLanguageSelect2 = $form->select('copyTreeTo', $copyLanguages);
	
	?>
	<form method="post" action="<?=$this->action('copy_tree')?>">
		<p><?=t('Copy from %s to %s', $copyLanguageSelect1, $copyLanguageSelect2)?>
		</p>
		<?=Loader::helper('validation/token')->output('copy_tree')?>
		<?=Loader::helper('concrete/interface')->submit(t('Copy Tree'), 'copy', 'left')?>
		
	</form>
<? } else if (count($pages) == 1) { ?>
	<p><?=t("You must have more than one multilingual section to use this tool.")?></p>
<? } else { ?>
	<p><?=t('You have not created any multilingual content sections yet.')?></p>
<? } ?>

</div>

<h1><span><?=t('Default Language')?></span></h1>
<div class="ccm-dashboard-inner">
<p><?=t('Set a default language section for your site.')?></p>
	<? 
	if (count($pages) > 0) {
	$defaultLanguages = array('' => t('** None Set'));
	foreach($pages as $pc) {
		$pcl = MultilingualSection::getByID($pc->getCollectionID());
		$defaultLanguages[$pcl->getLanguage()] = $pcl->getLanguageText();
	}
	$defaultLanguagesSelect = $form->select('defaultLanguage', $defaultLanguages, $defaultLanguage);
	
	?>
	<form method="post" action="<?=$this->action('set_default')?>">
		<?=$form->checkbox('useBrowserDetectedLanguage', 1, $useBrowserDetectedLanguage)?>
		<?=$form->label('useBrowserDetectedLanguage', t('Attempt to use visitor\'s language based on their browser information.'))?>
		
		<p><? print $defaultLanguagesSelect; ?>
		
		<?=$form->checkbox('redirectHomeToDefaultLanguage', 1, $redirectHomeToDefaultLanguage)?>
		<?=$form->label('redirectHomeToDefaultLanguage', t('Redirect home page to default language section.'))?><br /><br />
		</p>
		<?=Loader::helper('validation/token')->output('set_default')?>
		<?=Loader::helper('concrete/interface')->submit(t('Set Default'), 'set_default', 'left')?>
		
	</form>
<? } else { ?>
	<p><?=t('You have not created any multilingual content sections yet.')?></p>
<? } ?>

</div>
