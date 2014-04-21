<?php   defined('C5_EXECUTE') or die("Access Denied.");?>
<?php  
$class1 = 'icon-select-list';
$class2 = 'icon-select-page';
if (version_compare(APP_VERSION, '5.5.2.2', '>')) {
	$class1 = 'item-select-list';
	$class2 = 'item-select-page';
}
$c = Page::getByID($_REQUEST['cID'], 'RECENT');
$cp = new Permissions($c);
$pc = Page::getByPath('/dashboard/multilingual');
$pcp = new Permissions($pc);
$nav = Loader::helper('navigation');
$ih = Loader::helper('interface/flag', 'multilingual');
if ($cp->canRead() && $pcp->canRead()) {

	// grab all languages
	Loader::model('section', 'multilingual');
	$mlist = MultilingualSection::getList();
	$currentSection = MultilingualSection::getCurrentSection();
	$ml[] = $currentSection;
	foreach($mlist as $m) {
		if ($m->getCollectionID() != $currentSection->getCollectionID()) {
			$ml[] = $m;
		}
	}
	
	$currentPageCID = Page::getCurrentPage()->cID;
	$jsActionUrlBase = DIR_REL . '/' . DISPATCHER_FILENAME . '/' . DIRNAME_DASHBOARD . '/multilingual/page_report';
	
	// then loop through them and show if a page exists
	?>

	<ul class="<?=$class1?>">
	<?php   foreach($ml as $m) { 
		$relatedID = $m->getTranslatedPageID($c); 
		$icon = $ih->getSectionFlagIcon($m, true);
		$locale = $m->getLocale();
		?>
			<li id="node-<?= $currentPageCID; ?>-<?= $locale; ?>" style="position: relative;" class="<?= ($icon) ? '' : $class2; ?>">
		<?php  
		if ($relatedID && $currentSection->getCollectionID() != $m->getCollectionID()) { 
				$relatedPage = Page::getByID($relatedID, 'RECENT');
				
		?><a <?php   if ($icon) { ?>style="background-image: url(<?php  echo $icon?>)" <?php   } ?> href="<?php  echo $nav->getLinkToCollection($relatedPage)?>"><?php  echo t('%s: %s', $m->getLanguageText(), $relatedPage->getCollectionName())?></a>
		<?php   } else { ?><span <?php   if ($icon) { ?>style="background-image: url(<?php  echo $icon?>)" <?php   } ?>><?php  
			if ($currentSection->getCollectionID() == $m->getCollectionID()) {
				print t('Currently Viewing: %s', $c->getCollectionName());
			} else { ?><?php  echo t('%s: None Created', $m->getLanguageText())?></span>
				<div class="ccm-ui" style="position: absolute; top: 5px; right: 5px;">
					<input type="button" ccm-source-page-id="<?= $currentPageCID; ?>" ccm-destination-language="<?= $locale; ?>" name="ccm-multilingual-create-page" value="<?= t('Create'); ?>" class='btn success' style="font-size: 10px" />
					<input type="button" ccm-source-page-id="<?= $currentPageCID; ?>" ccm-destination-language="<?= $locale; ?>" name="ccm-multilingual-assign-page" value="<?= t('Map'); ?>" class='btn info' style="font-size: 10px"  />
				</div>
			<?php }
			
			}?></li>
	<?php   } ?>
	</ul>

	<script type="text/javascript">
		var activeAssignNode = false;
		$(function() {

			$("input[name=ccm-multilingual-assign-page]").click(function() {
				activeAssignNode = this;
				$.fn.dialog.open({
					title: '<?= t("Choose A Page"); ?>',
					href: CCM_TOOLS_PATH + '/sitemap_overlay.php?sitemap_mode=select_page&callback=ccm_multilingualAssignPage',
					width: '550',
					modal: false,
					height: '400'
				});
			});
			$('input[name=ccm-multilingual-create-page]').click(function() {
				ccm_multilingualCreatePage($(this).attr('ccm-source-page-id'), $(this).attr('ccm-destination-language'));
			});

			ccm_multilingualAssignPage = function(cID, cName) {
				var srcID    = $(activeAssignNode).attr('ccm-source-page-id');
				var destLang = $(activeAssignNode).attr('ccm-destination-language');
				$("#node-" + srcID + "-" + destLang).load('<?= $jsActionUrlBase; ?>/assign_page/', {'token': '<?= Loader::helper("validation/token")->generate("assign_page"); ?>', 'sourceID': srcID, 'destID': cID});
			}
			ccm_multilingualCreatePage = function(srcID, destLang) {
				$("#node-" + srcID + "-" + destLang).load('<?= $jsActionUrlBase; ?>/create_page/', {'token': '<?= Loader::helper("validation/token")->generate("create_page"); ?>', 'sourceID': srcID, 'locale': destLang});
			}

		});
	</script>

<?php   } ?>