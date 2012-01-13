<? defined('C5_EXECUTE') or die("Access Denied.");?>
<style>
table.ccm-results-list {
margin-top: 15px;
}

</style>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Multilingual Content Setup'),false, false, false); ?>
<div class="ccm-pane-body">
<legend><?=t('Content Sections')?></legend>
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
<form method="post" action="<?=$this->action('add_content_section')?>" class="form-stacked">
	<fieldset>
		<legend><?=t('Add a Language')?></legend>
		<div class="clearfix">
			<label for="msLanguage"><?=$form->label('msLanguage', t('Choose Language'))?></label>
			<div class="input">
				<?=$form->select('msLanguage', $locales);?>
			</div>
		</div>
		<label><?=t('Language Icon')?></label>
		<div class="clearfix">
			<div id="ccm-multilingual-language-icon">
			</div>
		</div>
		<legend><?=t('Choose a Parent Page')?></legend>
		<?=Loader::helper('form/page_selector')->selectPage('pageID', '')?>
		<br/>
		<?=Loader::helper('validation/token')->output('add_content_section')?>
		<?=Loader::helper('concrete/interface')->submit(t('Add Content Section'), 'add', 'left')?>
	</fieldset>
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




<form method="post" action="<?=$this->action('copy_tree')?>" class="form-stacked">
	<? if (count($pages) > 1) {
	$copyLanguages = array();
	foreach($pages as $pc) {
		$pcl = MultilingualSection::getByID($pc->getCollectionID());
		$copyLanguages[$pc->getCollectionID()] = $pc->getCollectionName() . ' - ' . $pcl->getLanguageText();
	}
	$copyLanguageSelect1 = $form->select('copyTreeFrom', $copyLanguages);
	$copyLanguageSelect2 = $form->select('copyTreeTo', $copyLanguages);
	
	?>
		<fieldset>
		<legend><?=t('Copy Language Tree')?></legend>
	<p><?=t('Copy all pages from a language to another section. This will only copy pages that have not been associated. It will not replace or remove any pages from the destination section.')?></p>
			<p><?=t('Copy from %s to %s', $copyLanguageSelect1, $copyLanguageSelect2)?>
			</p>
			<?=Loader::helper('validation/token')->output('copy_tree')?>
			<?=Loader::helper('concrete/interface')->submit(t('Copy Tree'), 'copy', 'left')?>
		</fieldset>
	</form>
<? } else if (count($pages) == 1) { ?>
	<p><?=t("You must have more than one multilingual section to use this tool.")?></p>
<? } else { ?>
	<p><?=t('You have not created any multilingual content sections yet.')?></p>
<? } ?>



<? if (count($pages) > 0) {
	$defaultLanguages = array('' => t('** None Set'));
	foreach($pages as $pc) {
		$pcl = MultilingualSection::getByID($pc->getCollectionID());
		$defaultLanguages[$pcl->getLanguage()] = $pcl->getLanguageText();
	}
	$defaultLanguagesSelect = $form->select('defaultLanguage', $defaultLanguages, $defaultLanguage);
	
	?>
	<form method="post" action="<?=$this->action('set_default')?>" class="form-stacked">
		<fieldset>
		<legend><?=t('Default Language')?></legend>
<p><?=t('Set a default language section for your site.')?></p>
			<div class="clearfix">
				<? print $defaultLanguagesSelect; ?>
			</div>
			<div class="clearfix">
					<div class="input" id="redirectHomeToDefaultLanguage">
						<?=$form->checkbox('redirectHomeToDefaultLanguage', 1, $redirectHomeToDefaultLanguage)?>
					</div>
			</div>
			<div class="clearfix">
				<?=$form->label('redirectHomeToDefaultLanguage', t('Redirect home page to default language section.'))?>
				<div class="input" id="useBrowserDetectedLanguage">
					<?=$form->checkbox('useBrowserDetectedLanguage', 1, $useBrowserDetectedLanguage)?>
				</div>
			</div>
			<div class="clearfix">
				<?=$form->label('useBrowserDetectedLanguage', t('Automatically use visitor language based on their browser information.'))?>
				<div class="input" id="formSubmit">
					<?=Loader::helper('validation/token')->output('set_default')?>
					<?=Loader::helper('concrete/interface')->submit(t('Set Default'), 'set_default', 'left')?>
				</div>
			</div>
		</fieldset>
	</form>
<? } else { ?>
	<p><?=t('You have not created any multilingual content sections yet.')?></p>
<? } ?>
</div>
<div class="ccm-pane-footer"></div>
