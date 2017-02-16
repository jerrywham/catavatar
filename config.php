<?php if(!defined("PLX_ROOT")) exit; ?>
<?php 
	if(!empty($_POST)) {
		//On supprime l'ancienne image si elle existe
		$old = $plxPlugin->getParam('admin'.$plxAdmin->aUsers[$_SESSION['user']]['name']);
		$oldimageurl = hexdec(substr(md5(mb_strtolower($old,PLX_CHARSET)),0,6));
		$oldimageurl = preg_replace('![^A-Za-z0-9\._-]!', '', $oldimageurl); 
		$oldimageurl = substr($oldimageurl,0,35);
		if (is_file(PLX_ROOT.$plxPlugin->getParam('cachepath').$oldimageurl)) {
			unlink(PLX_ROOT.$plxPlugin->getParam('cachepath').$oldimageurl);
		}

		$admin = plxUtils::cdataCheck($_POST['admin']);
		$plxPlugin->setParam('admin'.$plxAdmin->aUsers[$_SESSION['user']]['name'], $admin, 'string');

		$cache = plxUtils::cdataCheck($_POST['cachepath']);
		$plxPlugin->setParam('cachepath', $cache, 'string');

		$plxPlugin->saveParams();

		if (!is_dir(PLX_ROOT.$cache)) {
			mkdir(PLX_ROOT.$cache);
		}
		header('Location: parametres_plugin.php?p=catavatar');
		exit;
	}
	if (empty($plxPlugin->getParam('cachepath'))) {
		$cachepath = 'data/medias/avatars/';
	} else {
		$cachepath = plxUtils::strCheck($plxPlugin->getParam('cachepath'));
	}
	if ($plxPlugin->getParam('admin'.$plxAdmin->aUsers[$_SESSION['user']]['name']) == '') {
		$admin = plxUtils::charAleatoire();
	} else {
		$admin = plxUtils::strCheck($plxPlugin->getParam('admin'.$plxAdmin->aUsers[$_SESSION['user']]['name']));
	}
?>
<h2><?php $plxPlugin->lang('L_TITLE') ?></h2>
<p><?php $plxPlugin->lang('L_DESCRIPTION') ?></p>
<form action="parametres_plugin.php?p=catavatar" method="post" style="font-size:16px;">
	<li><label><?php $plxPlugin->lang('L_CACHE_PATH'); ?> : 	
	<?php plxUtils::printInput('cachepath',$cachepath,'text','20-60') ?><a class="hint"><span><?php echo L_HELP_SLASH_END ?></span></a></label></li>
	<?php if ($_SESSION['profil'] == PROFIL_ADMIN ) :?>

	<li><label><?php $plxPlugin->lang('L_STRING_FOR_ADMIN'); ?> : 
		<?php plxUtils::printInput('admin',$admin,'text','40-35') ?><a class="hint"><span><?php echo $plxPlugin->getLang('L_HELP_ADMIN_STRING') ?></span></a></label>
		<?php 
		if (null !== $plxPlugin->getParam('admin'.$plxAdmin->aUsers[$_SESSION['user']]['name']) && !empty($plxPlugin->getParam('admin'.$plxAdmin->aUsers[$_SESSION['user']]['name']))) {
			if ($_SESSION['profil'] == PROFIL_ADMIN ) {
				$loginToAvatar = $plxPlugin->getParam('admin'.$plxAdmin->aUsers[$_SESSION['user']]['name']);
			} else {
				$loginToAvatar = $plxAdmin->aUsers[$_SESSION['user']]['name'];
			}
			$imageurl = hexdec(substr(md5(mb_strtolower($loginToAvatar,PLX_CHARSET)),0,6));
			$imageurl = preg_replace('![^A-Za-z0-9\._-]!', '', $imageurl); 
			$imageurl = substr($imageurl,0,35);
			$chavatar = $plxPlugin->build_cat($imageurl);
			echo $plxPlugin->getLang('L_YOUR_CATAVATAR').'<img height="70px" width="70px" src="data:image/jpeg;base64,'.$chavatar.'"/>';
		}
		 ?>
	</li>
	<?php endif; ?>

	<br />
	<input type="submit" name="submit" value="Enregistrer"/>
</form>
