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
	
	// then loop through them and show if a page exists
	?>

	<ul class="<?=$class1?>">
	<?php   foreach($ml as $m) { 
		$relatedID = $m->getTranslatedPageID($c); 
		$icon = $ih->getSectionFlagIcon($m, true);
		if ($icon) { ?>
			<li><?php  
		} else { ?>
			<li class="<?=$class2?>">
			<?php  
		}
		if ($relatedID && $currentSection->getCollectionID() != $m->getCollectionID()) { 
				$relatedPage = Page::getByID($relatedID, 'RECENT');
				
		?><a <?php   if ($icon) { ?>style="background-image: url(<?php  echo $icon?>)" <?php   } ?> href="<?php  echo $nav->getLinkToCollection($relatedPage)?>"><?php  echo t('%s: %s', $m->getLanguageText(), $relatedPage->getCollectionName())?></a>
		<?php   } else { ?><span <?php   if ($icon) { ?>style="background-image: url(<?php  echo $icon?>)" <?php   } ?>><?php  
			if ($currentSection->getCollectionID() == $m->getCollectionID()) {
				print t('Currently Viewing: %s', $c->getCollectionName());
			} else { ?><?php  echo t('%s: None Created', $m->getLanguageText())?></span><?php   }
			
			}?></li>
	<?php   } ?>
	</ul>


<?php   } ?>