<? defined('C5_EXECUTE') or die("Access Denied.");?>

<h1><span><?=t('Page Report')?></span></h1>
<div class="ccm-dashboard-inner">
<?
$fh = Loader::helper('interface/flag', 'multilingual');
$nav = Loader::helper('navigation');
if (count($sections) > 0) { ?>
	<form method="get" action="<?=$this->action('view')?>" id="ccm-multilingual-page-report-form">

	
	<span class="ccm-multilingual-page-report-section">
	<strong><?=t('Choose Source')?></strong> <?=$form->select('sectionIDSelect', $sections, $sectionID)?>
	</span>
	
	<span class="ccm-multilingual-page-report-section">
	
	<strong><?=t('Choose Targets')?></strong>
	<? foreach($sectionList as $sc) { ?>
		<? $args = array('style' => 'vertical-align: middle');
		if ($sectionID == $sc->getCollectionID()) {
			$args['disabled'] = 'disabled';
		}
		?>
		<span class="ccm-multilingual-page-report-target">
			<?=$form->checkbox('targets[' . $sc->getCollectionID() . ']', $sc->getCollectionID(), in_array($sc->getCollectionID(), $targets), $args)?><?=$fh->getSectionFlagIcon($sc)?>
			<?=$form->label('targets[' . $sc->getCollectionID() . ']', $sc->getLanguageText(). " (".$sc->getLocale().")")?>
		</span>
	<? } ?>
	
	</span>

	<span class="ccm-multilingual-page-report-section">
	
	<strong><?=t('Show: ')?></strong>
	<?=$form->radio('showAllPages', 0, 0)?>
	<?=$form->label('showAllPages', t('Missing Pages'))?>
	<?=$form->radio('showAllPages', 1, false)?>
	<?=$form->label('showAllPages', t('All Pages'))?>

	
	<?=$form->submit('submitForm', t('Go'))?>
	<?=$form->hidden('sectionID', $sectionID); ?>
	
	</span>

	
	<div style="clear: both">&nbsp;</div>
	</form>
	<? if (count($pages) > 0) { ?>
		<?=$pl->displaySummary()?>
	<? } ?>
	
	
		<table class="ccm-results-list" cellspacing="0" cellpadding="0" border="0">
		<thead>
		<tr>
			<th><?
				$sourceMS = MultilingualSection::getByID($sectionID);
				print $sourceMS->getLanguageText(); echo " (".$sourceMS->getLocale().")";
			
			?></th>
			<? foreach($targetList as $sc) { ?>
				<? if ($section->getCollectionID() != $sc->getCollectionID()) { ?>
					<th><?
						print $sc->getLanguageText();
						echo " (".$sc->getLocale().")";
					?></th>
				<? } ?>
			<? } ?>
		</tr>
		</thead>
		<tbody>
		<? if (count($pages) > 0) { ?>
		<? $class = 'ccm-list-record-no-hover ccm-list-record-alt'; ?>
		<? foreach($pages as $pc) { 
			if ($class == 'ccm-list-record-no-hover ccm-list-record-alt') {
				$class = 'ccm-list-record-no-hover';
			} else {
				$class = 'ccm-list-record-no-hover ccm-list-record-alt';
			}
			
			?>
		<tr class="<?=$class?>">
			<td><a href="<?=$nav->getLinkToCollection($pc)?>"><?=$pc->getCollectionName()?></a></td>
			<? foreach($targetList as $sc) { ?>
				<? if ($section->getCollectionID() != $sc->getCollectionID()) { ?>
					<td id="node-<?=$pc->getCollectionID()?>-<?=$sc->getLocale()?>"><?
						$cID = $sc->getTranslatedPageID($pc);
						if ($cID) { 
							$p = Page::getByID($cID);
							print '<div style="margin-bottom: 8px"><a href="' . $nav->getLinkToCollection($p) . '">' . $p->getCollectionName() . '</a></div>';
						} else if ($cID === '0') { 
							print '<div style="margin-bottom: 8px">' . t('Ignored') . '</div>';
						
						} 
						
							$cParentID = $pc->getCollectionParentID();
							$cParent = Page::getByID($cParentID);
							$cParentRelatedID = $sc->getTranslatedPageID($cParent);
							if ($cParentRelatedID) { 
							
								$assignLang = t('Re-Map');
								if (!$cID) {
									$assignLang = t('Map');
								}
						?>
						<form>
							<? if (!$cID) { ?>
								<input style="font-size: 10px" type="button" value="<?=t('Create')?>" ccm-source-page-id="<?=$pc->getCollectionID()?>" ccm-destination-language="<?=$sc->getLocale()?>" name="ccm-multilingual-create-page" />
							<? } ?>
							<input style="font-size: 10px" type="button" value="<?=$assignLang?>" ccm-source-page-id="<?=$pc->getCollectionID()?>" ccm-destination-language="<?=$sc->getLocale()?>" name="ccm-multilingual-assign-page" />
							<? if ($cID !== '0' && !$cID) { ?>
								<input style="font-size: 10px" type="button" value="<?=t('Ignore')?>" ccm-source-page-id="<?=$pc->getCollectionID()?>" ccm-destination-language="<?=$sc->getLocale()?>" name="ccm-multilingual-ignore-page" />
							<? } ?>
						</form>
						
						<? } else { ?>
							<div class="ccm-note"><?=t("Create the parent page first.")?></div>
						<? } ?>
					</td>
				<? } ?>
			<? } ?>
		</tr>
		<? } ?>
		
		<? } else { ?>
		<tr>
			<td colspan="4"><?=t('No pages found.')?></td>
		</tr>
		<? } ?>
		</tbody>
		</table>
		<?=$pl->displayPaging()?>
	</form>
	
<style type="text/css">
.ccm-multilingual-page-report-target {margin-right: 16px; }
.ccm-multilingual-page-report-target img, ccm-multilingual-page-report-target input {margin-right: 4px; vertical-align: middle;}
.ccm-multilingual-page-report-section {float: left; height: 20px;  margin-right: 30px; white-space: nowrap; margin-bottom: 16px; line-height: 24px}
table.ccm-results-list td {padding-top: 12px; padding-bottom: 12px}
</style>

<script type="text/javascript">

var activeAssignNode = false;

$(function() {
	$('input[name=ccm-multilingual-create-page]').click(function() {
		ccm_multilingualCreatePage($(this).attr('ccm-source-page-id'), $(this).attr('ccm-destination-language'));
	});

	$("select[name=sectionIDSelect]").change(function() {
		$(".ccm-multilingual-page-report-target input").attr('disabled', false);
		$(".ccm-multilingual-page-report-target input[value=" + $(this).val() + "]").attr('disabled', true).attr('checked', false);
		$("input[name=sectionID]").val($(this).val());
		$("#ccm-multilingual-page-report-form").submit();
	});
	$('input[name=ccm-multilingual-ignore-page]').click(function() {
		ccm_multilingualIgnorePage($(this).attr('ccm-source-page-id'), $(this).attr('ccm-destination-language'));
	});
	
	$("input[name=ccm-multilingual-assign-page]").click(function() {
		activeAssignNode = this;
		$.fn.dialog.open({
			title: '<?php echo t("Choose A Page") ?>',
			href: CCM_TOOLS_PATH + '/sitemap_overlay.php?sitemap_mode=select_page&callback=ccm_multilingualAssignPage',
			width: '550',
			modal: false,
			height: '400'
		});
	});
});

ccm_multilingualAssignPage = function(cID, cName) {
	var srcID = $(activeAssignNode).attr('ccm-source-page-id'); 
	var destLang = $(activeAssignNode).attr('ccm-destination-language');
	$("#node-" + srcID + "-" + destLang).load('<?=$this->action("assign_page")?>', {'token': '<?=Loader::helper("validation/token")->generate("assign_page")?>', 'sourceID': srcID, 'destID': cID});
}
ccm_multilingualCreatePage = function(srcID, destLang) {
	$("#node-" + srcID + "-" + destLang).load('<?=$this->action("create_page")?>', {'token': '<?=Loader::helper("validation/token")->generate("create_page")?>', 'sourceID': srcID, 'locale': destLang});
}
ccm_multilingualIgnorePage = function(srcID, destLang) {
	$("#node-" + srcID + "-" + destLang).load('<?=$this->action("ignore_page")?>', {'token': '<?=Loader::helper("validation/token")->generate("ignore_page")?>', 'sourceID': srcID, 'locale': destLang});
}

</script>
<? } else { ?>
	<p><?=t('You have not defined any multilingual sections for your site yet.')?></p>
<? } ?>
</div>