<? defined('C5_EXECUTE') or die("Access Denied.");?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Multilingual Content Setup'), false, 'span14 offset1'); ?>

<h3><?=t('Content Sections')?></h3>
<?
$nav = Loader::helper('navigation');
if (count($pages) > 0) { ?>
	<table class="ccm-results-list" style="width: 100%">
	<tr>
		<th>&nbsp;</th>
		<th style="width: 45%"><?=t("Name")?></th>
		<th style="width: auto"><?=t('Language')?></th>
		<th style="width: 30%"><?=t('Path')?></th>
		<th>&nbsp;</th>
	</tr>
	<? foreach($pages as $pc) {
		$pcl = MultilingualSection::getByID($pc->getCollectionID()); ?>
		<tr>
			<td><?=$ch->getSectionFlagIcon($pc)?></td>
			<td><a href="<?=$nav->getLinkToCollection($pc)?>"><?=$pc->getCollectionName()?></a></td>
			<td><?=$pcl->getLanguageText()?> (<?php echo $pcl->getLocale();?>)</td>
			<td><?=$pc->getCollectionPath()?></td>
			<td><a href="<?=$this->action('remove_language_section', $pc->getCollectionID(), Loader::helper('validation/token')->generate())?>"><img src="<?=ASSETS_URL_IMAGES?>/icons/remove_minus.png" /></a></td>
		</tr>
	<? } ?>
	</table>

<? } else { ?>
	<p><?=t('You have not created any multilingual content sections yet.')?></p>
<? } ?>
<form method="post" action="<?=$this->action('add_content_section')?>">
	<h4><?=t('Add a Language')?></h4>
		<div class="clearfix">
			<?=$form->label('msLanguage', t('Choose Language'))?>
			<div class="input">
				<?=$form->select('msLanguage', $locales);?>
			</div>
		</div>
		<div class="clearfix">
			<label><?=t('Language Icon')?></label>
			<div class="input"><ul id="ccm-multilingual-language-icon" class="inputs-list"><li><span><strong><?=t('None')?></strong></span></li></ul></div>
		</div>
		<div class="clearfix">
			<label><?=t('Choose a Parent Page')?></label>
			<div class="input">
				<?=Loader::helper('form/page_selector')->selectPage('pageID', '')?>
			</div>
		</div>
		<div class="clearfix">
		<label></label>
		<div class="input">
			<?=Loader::helper('validation/token')->output('add_content_section')?>
			<?=Loader::helper('concrete/interface')->submit(t('Add Content Section'), 'add', 'left')?>
		</div>
		</div>
</form>

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
};

</script>


<br/>

<h3><?=t('Copy Language Tree')?></h3>
<form method="post" action="<?=$this->action('copy_tree')?>">
	<? if (count($pages) > 1) {
		$copyLanguages = array();
		foreach($pages as $pc) {
			$pcl = MultilingualSection::getByID($pc->getCollectionID());
			$copyLanguages[$pc->getCollectionID()] = $pc->getCollectionName() . ' - ' . $pcl->getLanguageText();
		}
		$copyLanguageSelect1 = $form->select('copyTreeFrom', $copyLanguages);
		$copyLanguageSelect2 = $form->select('copyTreeTo', $copyLanguages);

		?>
		<p><?=t('Copy all pages from a language to another section. This will only copy pages that have not been associated. It will not replace or remove any pages from the destination section.')?></p>
		<div class="clearfix">
		<label><?=t('Copy From')?></label>
		<div class="input"><?=$copyLanguageSelect1?></div>
		</div>

		<div class="clearfix">
		<label><?=t('To')?></label>
		<div class="input"><?=$copyLanguageSelect2?></div>
		</div>

		<div class="clearfix">
		<label></label>
		<div class="input">
			<?=Loader::helper('validation/token')->output('copy_tree')?>
			<?=Loader::helper('concrete/interface')->submit(t('Copy Tree'), 'copy', 'left')?>
		</div>
		</div>

	<? } else if (count($pages) == 1) { ?>
		<p><?=t("You must have more than one multilingual section to use this tool.")?></p>
	<? } else { ?>
		<p><?=t('You have not created any multilingual content sections yet.')?></p>
	<? } ?>
</form>


<? if (count($pages) > 0) {
	$defaultLanguages = array('' => t('** None Set'));
	foreach($pages as $pc) {
		$pcl = MultilingualSection::getByID($pc->getCollectionID());
		$defaultLanguages[$pcl->getLocale()] = $pcl->getLanguageText();
	}
	$defaultLanguagesSelect = $form->select('defaultLanguage', $defaultLanguages, $defaultLanguage);


	?>

<br/>

<h3><?=t('Multilingual Settings')?></h3>

	<form method="post" action="<?=$this->action('set_default')?>">
			<div class="clearfix">
				<label><?php echo t('Default Language');?></label>
				<div class="input">
					<? print $defaultLanguagesSelect; ?>
				</div>
			</div>

			<div class="clearfix">
				<div class="input">
					<ul class="inputs-list">
						<li>
							<label>
								<?=$form->checkbox('useBrowserDetectedLanguage', 1, $useBrowserDetectedLanguage)?>
								<span><?php echo t('Attempt to use visitor\'s language based on their browser information.') ?></span>
							</label>
						</li>
						<li>
							<label>
								<?=$form->checkbox('redirectHomeToDefaultLanguage', 1, $redirectHomeToDefaultLanguage)?>
								<span><?php echo t('Redirect home page to default language section.') ?></span>
							</label>
						</li>
					</ul>
				</div>
			</div>
			<div class="clearfix">
				<div class="input">
					<?=Loader::helper('validation/token')->output('set_default')?>
					<?=Loader::helper('concrete/interface')->submit(t('Save Settings'), 'set_default', 'left')?>
				</div>
			</div>

	</form>
	<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>