<?php if(!defined("PLX_ROOT")) exit; ?>
<?php 
	if(!empty($_POST)) {
		$cache = plxUtils::cdataCheck($_POST["cachepath"]);
		$plxPlugin->setParam("cachepath", $cache, "string");
		$plxPlugin->saveParams();
		if (!is_dir(PLX_ROOT.$cache)) {
			mkdir(PLX_ROOT.$cache);
		}
		header("Location: parametres_plugin.php?p=catavatar");
		exit;
	}
	if (empty($plxPlugin->getParam('cachepath'))) {
		$cachepath = 'data/medias/avatars/';
	} else {
		$cachepath = plxUtils::strCheck($plxPlugin->getParam('cachepath'));
	}
?>
<h2><?php $plxPlugin->lang("L_TITLE") ?></h2>
<p><?php $plxPlugin->lang("L_DESCRIPTION") ?></p>
<form action="parametres_plugin.php?p=catavatar" method="post" style="font-size:16px;">
	<li><label>Chemin du cache des images : 	
	<?php plxUtils::printInput('cachepath',$cachepath,'text','20-60') ?><a class="hint"><span><?php echo L_HELP_SLASH_END ?></span></a></label></li>
	<br />
	<input type="submit" name="submit" value="Enregistrer"/>
</form>
