<?php
/**
 * Classe comportant des outils communs à tout le site.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
final class zCorrecteurs
{
	/**
	 * Vérifie l'unicité d'une URL et redirige en 301 si besoin.
	 *
	 * @author mwsaz
	 * @param string	$titre		Segment de l'URL représentant l'objet manipulé (titre d'un billet etc).
	 * @param bool		$id		L'action peut prendre un ID en argument.
	 * @param bool		$id2		L'action peut prendre un ID2 en argument.
	 * @param mixed		$pdefaut	Numéro de page par défaut. false = pas de numéro de page.
	 * @return void
	 */
	static public function VerifierFormatageUrl($titre = null, $id = false, $id2 = false, $pdefaut = false)
	{
		// Traiter les requêtes POST
		if($_POST != array())
			return;
		
		$request = \Container::request();

		$titre != null && $titre = rewrite($titre);

		// Si l'URL n'est pas dans sa forme canonique, on redirige
		if(	// Segments superflus
			($_GET['id']	!= ''	&& !$id) ||
			($_GET['p']	!= ''	&& $pdefaut === false) ||
			($_GET['id2']	!= ''	&& !$id2) ||
			($_GET['titre'] != ''	&& $titre == null) ||

			// Numéro de page
			($_GET['p'] != '' && ((int)$_GET['p'] == $pdefaut || $_GET['p'] < 0)) ||

			// 'titre'
			($titre != '' && $_GET['titre'] != $titre)
		)
		{
			// Récupération des paramètres de base (id, id2, page, titre)
			$segmentID	= ($id && $_GET['id'] != '')	? '-'.(int)$_GET['id']	: '';
			$segmentID2	= ($id2 && $_GET['id2'] != '')	? '-'.(int)$_GET['id2']	: '';
			$segmentTitre	= $titre != null		? '-'.rewrite($titre)	: '';
			if(	$_GET['p'] != '' &&
				$pdefaut !== false &&
				$_GET['p'] > 0 &&
				(int)$_GET['p'] != $pdefaut
			)
				$segmentPage = '-p'.(int)$_GET['p'];
			else
				$segmentPage = '';

			// Construction de l'URL canonique
			$redirection = str_replace('_', '-', $request->attributes->get('_action'))
				.$segmentID.$segmentID2
				.$segmentPage.$segmentTitre
				.'.html';

			// Ajout des paramètres additionels
			$params = $_GET;
			unset($params['id'], $params['id2'], $params['p'], $params['titre'], $params['s']);
			if($params)
			{
				$redirection .= '?';
				foreach($params as $k => &$v)
				{
					if(!is_array($v))
						$redirection .= urlencode($k).'='.urlencode($v).'&';
					else
					{
						foreach($v as $val)
							$redirection .= urlencode($k).'[]='.urlencode($val).'&';
					}
				}
				$redirection = substr($redirection, 0, -1);
			}
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: '.$redirection);
			exit();
		}
	}

	/**
	 * Vérifie le token (faille CSRF)
	 * Utilisation :
	 * <code>if($r = zCorrecteurs::verifierToken()) return $r;</code>
	 *
	 * @return mixed	Un Response si le token est absent / mauvais, false sinon.
	 */
	static public function verifierToken()
	{
		$token = (isset($_POST['token']) ? $_POST['token'] :
			(isset($_GET['token']) ? $_GET['token'] : false));
		if(!$token || $token != $_SESSION['token'])
			return new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
		return false;
	}
}
