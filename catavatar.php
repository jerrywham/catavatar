<?php
/**
 * Plugin catavatar
 *
 * @author	Cyril MAGUIRE
 **/
class catavatar extends plxPlugin {

	public function __construct($default_lang) {
		# appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);

		# limite l'accès à l'écran d'administration du plugin
		# PROFIL_ADMIN , PROFIL_MANAGER , PROFIL_MODERATOR , PROFIL_EDITOR , PROFIL_WRITER
		$this->setConfigProfil(PROFIL_ADMIN);
		
		# Déclaration d'un hook (existant ou nouveau)
		$this->addHook('plxMotorParseCommentaire', 'plxMotorParseCommentaire');	
	}
	
	# HOOKS
	public function plxMotorParseCommentaire()
	{	
		$string ="
		\$plxCatavatarPlugin = \$this->plxPlugins->aPlugins['catavatar'];
		if (\$com['type'] == 'admin' ) {
			\$loginToAvatar = \$plxCatavatarPlugin->getParam('admin'.\$com['author']);
		} else {
			\$loginToAvatar = \$com['author'];
		}
		\$imageurl = hexdec(substr(md5(mb_strtolower(\$loginToAvatar,PLX_CHARSET)),0,6));
		\$imageurl = preg_replace('![^A-Za-z0-9\._-]!', '', \$imageurl); 
		\$imageurl = substr(\$imageurl,0,35);

		\$cachefile = '".PLX_ROOT.$this->getParam('cachepath')."'.\$imageurl;
		\$cachetime = 604800; # 1 week (1 day = 86400)

		# Serve from the cache if it is younger than \$cachetime
		if (file_exists(\$cachefile) && time() - \$cachetime < filemtime(\$cachefile)) {
			\$chavatar = file_get_contents(\$cachefile);
		} else {

			# render the picture:
			\$chavatar = \$plxCatavatarPlugin->build_cat(\$imageurl);

			# Save /cache the output to a file
			file_put_contents(\$cachefile, \$chavatar);
			chmod(\$cachefile, 0755);
		}
			
 			\$com['catavatar'] = '<img height=\"70px\" width=\"70px\" src=\"data:image/jpeg;base64,'.\$chavatar.'\"/>';";

			echo '<?php'.$string.';?>';
	}

	/**
	* ====================
	* CAT-AVATAR-GENERATOR
	* ====================
	* 
	* @authors: Andreas Gohr, David Revoy
	* 
	* This PHP is licensed under the short and simple permissive:
	* [MIT License](https://en.wikipedia.org/wiki/MIT_License)
	* 
	**/

	public function build_cat($seed='') 
	{
		$imageurl = $seed;
		// init random seed
		if($seed) srand( $imageurl );

		// throw the dice for body parts
		$parts = array(
			'body' => rand(1,15),
			'fur' => rand(1,10),
			'eyes' => rand(1,15),
			'mouth' => rand(1,10),
			'accessorie' => rand(1,20)
			);

		// create backgound
		$cat = @imagecreatetruecolor(70, 70)
		or die("GD image create failed");
		$white = imagecolorallocate($cat, 255, 255, 255);
		imagefill($cat,0,0,$white);

		// add parts
		foreach($parts as $part => $num){
			$file = PLX_PLUGINS.'catavatar/avatars/'.$part.'_'.$num.'.png';

			$im = @imagecreatefrompng($file);
			if(!$im) die('Failed to load '.$file);
			imageSaveAlpha($im, true);
			imagecopy($cat,$im,0,0,0,0,70,70);
			imagedestroy($im);
		}

		// restore random seed
		if($seed) srand();
		ob_start(); 
		imagejpeg( $cat, NULL, 100 ); 
		imagedestroy( $cat ); 
		$i = ob_get_clean(); 

		$chat = base64_encode( $i );
		file_put_contents(PLX_ROOT.$this->getParam('cachepath').$imageurl, $chat);

		return $chat;
	}

}