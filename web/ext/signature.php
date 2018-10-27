<?php
/* Frontend minimal */
define('BASEPATH', dirname(__FILE__).'/../..');

include(BASEPATH.'/vendor/autoload.php');
include(BASEPATH.'/app/AppKernel.php');
$app = new AppKernel('prod', false);
$app->boot();
$cache = \Container::getService('zco_core.templating.helper.cache');

header('Content-Type: image/png');

/**
 * Crée une signature avec le titre du dernier billet
 * @author Skydreamer
*/
if (!$cache->output('signature', 7200))
{
	include(BASEPATH.'/src/Zco/Bundle/BlogBundle/modeles/blog.php');
	$billets = ListerBillets(array('etat' => BLOG_VALIDE), 1, 1);

	//Config
	$milieu = 302;
	$taille = 9;
	$police = BASEPATH.'/data/fonts/oceania.ttf';
	$texte = 'Dernier billet : %s';
	$texte = sprintf($texte, $billets[0][0]['version_titre']);
	$angle = 0;
	$longueur = imagettfbbox($taille, $angle, $police, $texte);
	$longueur = $longueur[4] - $longueur[6];
	$x = $milieu - $longueur / 2;
	$y = 40;
	$image = imagecreatefrompng(BASEPATH.'/web/ext/signature.png');
	$noir = imagecolorallocate($image, 0, 0, 0);
	imagettftext($image, $taille, $angle, $x, $y, $noir, $police, $texte);
	imagepng($image);
	
	$cache->end();
}
