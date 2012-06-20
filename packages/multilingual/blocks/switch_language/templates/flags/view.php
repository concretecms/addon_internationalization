<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<?
$ih = Loader::helper("interface/flag", 'multilingual');

?>

<div class="ccm-multilingual-switch-language-flags ">
	<div class="ccm-multilingual-switch-language-flags-label"><?=$label?></div>


<? foreach($languageSections as $ml) {
	?>

	<a href="<?=$action?>?ccmMultilingualChooseLanguage=<?=$ml->getCollectionID()?>&ccmMultilingualCurrentPageID=<?=$cID?>" class="<? if ($activeLanguage == $ml->getCollectionID()) { ?>ccm-multilingual-active-flag<? } ?>"><?
		print $ih->getSectionFlagIcon($ml);
	?></a>




<? } ?>

</div>