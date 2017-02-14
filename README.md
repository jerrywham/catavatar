Cat-Avatar-Generator : Plugin for PluXml 5.5 and 5.6
====================================================

![cover picture](http://www.peppercarrot.com/data/images/lab/2016-11-30_cdn/2016-11-29_the-quest-to-free-peppercarrot-website_02a-avatar.jpg)

A generator of cats pictures optimised to generate or random avatars, or defined avatar from a "seed". A derivation by [David Revoy](http://www.peppercarrot.com) from the original [MonsterID by Andreas Gohr's](https://www.splitbrain.org/blog/2007-01/20_monsterid_as_gravatar_fallback).

## License:

**Artworks:**
PNG and ORA files licensed under: [CC-By 4.0](https://creativecommons.org/licenses/by/4.0/) attribution: David Revoy with the following exception: Generated cats used as Avatar (for blog,forum,social-network) don't need direct attribution and so, can be used as regular avatars without pasting David Revoy's name all over the place.

**Code**
This PHP is licensed under the short and simple permissive:
[MIT License](https://en.wikipedia.org/wiki/MIT_License)
 
## Usage:

Call the original script this way: 
```
echo '<img height="70px" width="70px" src="your/path/to/cat-avatar-generator?seed='.$var.'"/>';
```
_(Note: for the seed, I advice to use author's name to not expose email or sensitive datas, even hashed on a public code.)_

For PluXml, modify, in your theme, the 'commentaires.php' file as follow :
```php
<?php if(!defined('PLX_ROOT')) exit; ?>

	<?php if($plxShow->plxMotor->plxRecord_coms): ?>

		<h3 id="comments">
			<?php echo $plxShow->artNbCom(); ?>
		</h3>

		<?php while($plxShow->plxMotor->plxRecord_coms->loop()): # On boucle sur les commentaires ?>

		<div id="<?php $plxShow->comId(); ?>" class="<?php if (!isset($plxMotor->plxPlugins->aPlugins['catavatar'])) {echo 'comment ';}?><?php $plxShow->comLevel(); ?>">

			<div id="com-<?php $plxShow->comIndex(); ?>">

				<?php if (isset($plxMotor->plxPlugins->aPlugins['catavatar'])) {echo $plxShow->plxMotor->plxRecord_coms->f('catavatar'); }; ?>

// (...) the rest of the page is the same as original commentaires.php file 
```

## How to edit artworks

1. Open img/00_SRC.ora with Krita ( or Gimp,Mypaint,Pinta) Do your edit/draw/paint, respect layer naming, save.
2. Open it again in Gimp 2.8, with the [export layer plugin](https://github.com/khalim19/gimp-plugin-export-layers/releases/download/2.4/export-layers-2.4.zip)
3. Scale the image down to the result you want (eg. 256px x 256px as on the demo ) LancZos filter
3. File > Export layer (Allow invisible layer to be exported, check 'image size', PNG file format )
4. Done. 

All PNG files of 'parts' are extracted this way and keep their layer name.


## ORIGINAL SCRIPT

Copy and paste the following code :
```php
<?php
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

// /!\ change the path to your system's cache or a folder(write permission) 
// Note: this path end with / and is relative to the cat-avatar-generator.php file.
$cachepath = 'cache/';

function build_cat($seed=''){
    // init random seed
    if($seed) srand( hexdec(substr(md5($seed),0,6)) );

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
        $file = dirname(__FILE__).'/avatars/'.$part.'_'.$num.'.png';

        $im = @imagecreatefrompng($file);
        if(!$im) die('Failed to load '.$file);
        imageSaveAlpha($im, true);
        imagecopy($cat,$im,0,0,0,0,70,70);
        imagedestroy($im);
    }

    // restore random seed
    if($seed) srand();

    header('Pragma: public');
    header('Cache-Control: max-age=86400');
    header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
    header('Content-Type: image/jpg');
    imagejpeg($cat, NULL, 90);
    imagedestroy($cat);
}

$imageurl = $_GET["seed"];
$imageurl = preg_replace('/[^A-Za-z0-9\._-]/', '', $imageurl); 
$imageurl = substr($imageurl,0,35).'';
$cachefile = ''.$cachepath.''.$imageurl.'.jpg';
$cachetime = 604800; # 1 week (1 day = 86400)

// Serve from the cache if it is younger than $cachetime
if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile)) {
  header('Pragma: public');
  header('Cache-Control: max-age=86400');
  header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
  header('Content-Type: image/jpg');
  readfile($cachefile);
  exit;
}

// ...Or start generation
ob_start(); 

// render the picture:
build_cat($_REQUEST['seed']);

// Save/cache the output to a file
$savedfile = fopen($cachefile, 'w+'); # w+ to be at start of the file, write mode, and attempt to create if not existing.
fwrite($savedfile, ob_get_contents());
fclose($savedfile);
chmod($savedfile, 0755);
ob_end_flush();
?>
```
