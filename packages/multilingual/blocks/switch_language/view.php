<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<form method="post" action="<?=$action?>">
	<?=$label?>
	<?=$form->select('ccmMultilingualChooseLanguage', $languages, $activeLanguage)?>
	<input type="hidden" name="ccmMultilingualCurrentPageID" value="<?=$cID?>" />
</form>