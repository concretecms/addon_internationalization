<? defined('C5_EXECUTE') or die("Access Denied.");?>
<?
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

	// then loop through them and show if a page exists
	?>

	<ul class="icon-select-list">
	<? foreach($ml as $m) {
		$relatedID = $m->getTranslatedPageID($c);
		$icon = $ih->getSectionFlagIcon($m, true);
		if ($icon) { ?>
			<li><?
		} else { ?>
			<li class="icon-select-page">
			<?
		}
		if ($relatedID && $currentSection->getCollectionID() != $m->getCollectionID()) {
				$relatedPage = Page::getByID($relatedID, 'RECENT');

		?><a <? if ($icon) { ?>style="background-image: url(<?=$icon?>)" <? } ?> href="<?=$nav->getLinkToCollection($relatedPage)?>"><?=t('%s: %s', $m->getLanguageText(), $relatedPage->getCollectionName())?></a>
		<? } else { ?><span <? if ($icon) { ?>style="background-image: url(<?=$icon?>)" <? } ?>><?
			if ($currentSection->getCollectionID() == $m->getCollectionID()) {
				print t('Currently Viewing: %s', $c->getCollectionName());
			} else { ?><?=t('%s: None Created', $m->getLanguageText())?></span><? }

			}?></li>
	<? } ?>
	</ul>


<? } ?>