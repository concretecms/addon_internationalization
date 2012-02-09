<? defined('C5_EXECUTE') or die(_("Access Denied."));

$ih = Loader::helper("interface/flag", 'multilingual');
?>

<div class="ccm-multilingual-language-list-wrapper">
	<strong><?=$label?></strong>
	
	<form method="post" action="<?=$action?>" id="ccm-multilingual-language-list">
	<? if (Loader::helper('validation/numbers')->integer($_REQUEST['rcID'])) { ?>
		<input type="hidden" name="ccmMultilingualCurrentPageID" value="<?=Loader::helper('text')->entities($_REQUEST['rcID'])?>" />	
	<? } ?>

<? foreach($languageSections as $ml) {  ?>	
	<div class="ccm-multilingual-language-list-item">
	
	<input type="radio" name="ccmMultilingualSiteDefaultLanguage" value="<?=$ml->getLocale()?>"  <? if ($defaultLanguage == $ml->getLocale()) { ?> checked="checked" <? } ?> /><?
		print $ih->getSectionFlagIcon($ml);	
		print $ml->getLanguageText($ml->getLanguage());
		print ' ' . (strlen($ml->msIcon)?'('.$ml->msIcon.')':'');
	?></div>
	
<? } ?>

	<div class="ccm-multilingual-site-default-remember">
		<?=$form->checkbox('ccmMultilingualSiteRememberDefault', 1, 1)?> <?=$form->label('ccmMultilingualSiteRememberDefault', t('Remember my choice on this computer.'))?>
	</div>
	
	<?=$form->submit('submit', t('Save'))?>
	</form>
	
</div>