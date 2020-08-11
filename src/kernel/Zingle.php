<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use Symfony\Component\HttpFoundation\Response;
use Zco\Component\Templating\TemplatingEvents;
use Zco\Component\Templating\Event\FilterResourcesEvent;
use Zco\Component\Templating\Event\FilterVariablesEvent;

/**
 * Replace accented characters with non accented
 *
 * @param string $str
 * @return string
 */
function stripAccents($str) {
    $a = ['À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή'];
    $b = ['A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η'];

    return str_replace($a, $b, $str);
}

/**
 * Réduit le charset pour une URL.
 *
 * @param   string  $t  Texte.
 * @return  string
 */
function rewrite($t)
{
	//Remplacement des caractères non acceptés
	$t = stripAccents($t);
	$t = mb_strtolower($t);
	$t = preg_replace('`[^a-z0-9]+`', '-', $t);
	$t = trim($t, '-');

	//Éviter la confusion avec id2 si 'nom' commence par un chiffre
	isset($t[0]) && is_numeric($t[0]) && $t = '-'.$t;
	return $t  === '' ? 'n-a' : $t;
}

/**
 * Vérification des autorisations
 *
 * @param   string $droit  Le droit à vérifier.
 * @param   integer $cat   La catégorie où l'ont veut vérifier le droit (null par défaut).
 * @return  bool|integer   Retourne true / false en cas de droit binaire, la valeur numérique sinon.
*/
function verifier($droit, $cat = 0, $groupe = null)
{
	static $liste_droits = array();
	static $cache_droits = array();

	//Si on teste le fait que le visiteur soit connecté
	if($droit == 'connecte')
	{
		$result = isset($_SESSION['id']) && $_SESSION['id'] > 0;
		return $result;
	}
	
	//Si on teste le fait que le visiteur soit anonyme
	if ($droit === 'anonyme')
	{
		return !verifier('connecte');
	}
	
	if ($groupe == null && isset($cache_droits[$cat][$droit]))
	{
		return $cache_droits[$cat][$droit];
	}

	//Si on n'a pas spécifié de groupe, c'est celui en session
	if(is_null($groupe)) {
		$groupe = isset($_SESSION['groupe']) ? $_SESSION['groupe'] : GROUPE_VISITEURS;
        $groupes = isset($_SESSION['groupes_secondaires']) ? $_SESSION['groupes_secondaires'] : array();
        array_unshift($groupes, $groupe);
        $ret = false;
        foreach($groupes as $groupe_id) {
            if (false == $ret) {
                $ret = verifier($droit, $cat, $groupe_id);
            }
        }
        return $ret;
    }

	//On récupère les droits du groupe et les stocke dans une variable statique pour ne pas les perdre
	if(!isset($liste_droits[$groupe]))
	{
		include_once(BASEPATH.'/src/Zco/Bundle/GroupesBundle/modeles/droits.php');
		$liste_droits[$groupe] = RecupererDroitsGroupe($groupe);
	}
	$droits = $liste_droits[$groupe];

	//On vérifie que le droit existe, sinon refus
	if(!array_key_exists($droit, $droits))
	{
		$cache_droits[$cat][$droit] = false;
		return false;
	}

	//Si aucune catégorie n'a été spécifiée
	if($cat == 0)
	{
		//Droit numérique ou binaire
		if(is_numeric($droits[$droit]))
		{
			$cache_droits[$cat][$droit] = $droits[$droit];
			return $droits[$droit];
		}
		//Si on a un array (c'était en fait un droit choisissable par catégories)
		//on retourne true s'il y a au moins un droit à true, false si tout est à false (ou droit non binaire)
		elseif(is_array($droits[$droit]))
		{
			foreach($droits[$droit] as $cle => $valeur)
			{
				if($valeur === 1)
				{
					$cache_droits[$cat][$droit] = true;
					return true;
				}
			}
			$cache_droits[$cat][$droit] = false;
			return false;
		}
	}
	//Si on avait spécifié une catégorie
	else
	{
		//Si on a bien un array
		if(is_array($droits[$droit]))
		{
			//Si cette catégorie n'est pas dans l'array
			if(!array_key_exists($cat, $droits[$droit]))
			{
				$cache_droits[$cat][$droit] = false;
				return false;
			}
			else
			{
				$cache_droits[$cat][$droit] = $droits[$droit][$cat];
				return $droits[$droit][$cat];
			}
		}
		//Sinon c'est un droit qui ne se gère pas par catégorie, on retourne sa valeur
		elseif(is_numeric($droits[$droit]))
		{
			$cache_droits[$cat][$droit] = $droits[$droit];
			return $droits[$droit];
		}
	}
}

function verifier_array($credentials)
{
	if (empty($credentials))
	{
		return true;
	}

	//Doubles crochets => condition de type OR entre les droits cités.
	if(is_array($credentials[0]))
	{
		foreach($credentials[0] as $auth)
		{
			if(verifier($auth))
				return true;
		}
		return false;
	}
	//Sinon tableau simple => condition de type AND entre les droits cités.
	else
	{
		foreach($credentials as $auth)
		{
			if(!verifier($auth))
				return false;
		}
		return true;
	}
}

/**
 * Fonction permettant le listage des pages à la SdZ
 * (Page : Précédent 1 2 3 ... 7 8 9 Suivante).
 *
 * @author winzou, DJ Fox, vincent1870
 * @link http://www.siteduzero.com/forum-83-33940-254991.html#r254991
 * @param int    $page              Page courante.
 * @param int    $nb_page           Nombre de pages en tout.
 * @param int    $nb_mess           Nombre de messages.
 * @param int    $nb_mess_par_page  Nombre de messages par page.
 * @param string $url               L'url, avec un %s pour le numéro de la page.
 * @param int    $nb                   Nombre de pages de chaque côté de la page courante.
 * @param bool   $reverse             Doit-on inverser les pages ?
 * @return array
 */
function liste_pages($page, $nb_page, $nb_mess, $nb_mess_par_page, $url, $reverse = false, $nb = 3)
{
	// Initialisations
	$list_page = array();
	$_page = $page;
	$page <= 0 && $page = 1;

	// Page précédente
	if($page > 1 && $_page != -1)
		$list_page[] = '<a href="'.str_replace(array('%s', '%d'), $page-1, $url).'">'
			.($reverse ? 'Suivante' : 'Précédente').'</a>&nbsp;';

	// Création de l'array
	for ($i=1; $i <= $nb_page; $i++)
	{
		if (($i < $nb) || ($i > $nb_page - $nb) || (($i < $page + $nb) && ($i > $page -$nb)))
		{
			if($i == $page && $_page != -1)
				$list_page[] = '<span class="UI_pageon">'.$i.'</span>&nbsp;';
			else
				$list_page[] = '<a href="'.str_replace(array('%s', '%d'), $i, $url).'">'.$i.'</a>&nbsp;';
		}
		else
		{
			if ($i >= $nb && $i <= $page - $nb)
				$i = $page - $nb;
			elseif ($i >= $page + $nb && $i <= $nb_page - $nb)
				$i = $nb_page - $nb;
			$parts_url = explode('%s', $url);
			$list_page[] = '<a href="#" onclick="page=prompt(\'Sur quelle page voulez-vous vous rendre ('
				.$nb_page.' pages) ?\'); if(page) document.location=\''.$parts_url[0]
				.'\' + page + \''.(isset($parts_url[1]) ? $parts_url[1] : '')
				.'\'; return false;">…</a>&nbsp';
		}
	}

	// Page suivante
	if($page < $nb_page && $_page != -1)
		$list_page[] = '<a href="'.str_replace(array('%s', '%d'), $page+1, $url).'">'
		.($reverse ? 'Précédente' : 'Suivante').'</a>&nbsp;';

	// Si ce qu'on retourne est vide, on ajoute une page
	if(empty($list_page))
	{
		if($_page == -1)
			$list_page[] = '1&nbsp;';
		else
			$list_page[] = '<span class="UI_pageon">1</span>&nbsp;';
	}

	return $reverse ? array_reverse($list_page) : $list_page;
}

//Types de messages
define('MSG_ERROR',	0);
define('MSG_NEUTRAL',	1);
define('MSG_OK',	2);
/**
 * Fonction permettant de rediriger le visiteur avec un message.
 *
 * @param integer $idMsg  L'id du message à afficher.
 * @param string $url     L'url cible.
 * @param integer $type   Le type de message (confirmation par défaut).
 * @param integer $time   Le temps d'affichage de la page (1 par défaut, si 0 aucune page de redirection, -1 pour pas de redirection).
 * @return Response
 */
function redirect($idMsg = null, $url = '', $type = MSG_OK, $time = null)
{
	if ($idMsg == null)
		return new Symfony\Component\HttpFoundation\RedirectResponse($url);

	if(is_string($idMsg) && !empty($idMsg))
	{
		$message = $idMsg;
		$idMsg = null;
	}
	else
	{
		Config::load('messages');
		$message = Config::get('messages');
		$message = $message['msg'][$idMsg];
	}

	//--- Si on est dans une requête Ajax ---
	if (Container::getService('request')->isXmlHttpRequest())
	{
		$type = ($type == MSG_OK) ? 'info' : 'error';
		return new Response(json_encode(array(
		    'msg' => $message, 
		    'id' => $idMsg, 
		    'type' => $type, 
		    'url' => $url,
		)));
	}
	//--- Sinon on redirige de la façon ordinaire ---
	else
	{
		if (empty($url))
		{
		    $action = Container::getService('request')->attributes->get('_action');
			$url = str_replace('_', '-', $action).'.html';
		}

		if ($type == MSG_OK || $type == MSG_NEUTRAL)
		{
			$_SESSION['message'][] = $message;
		}
		else
		{
			$_SESSION['erreur'][] = $message;
		}
		
		return new Symfony\Component\HttpFoundation\RedirectResponse($url);
	}
}

/**
 * Récupère la valeur d'une préférence.
 *
 * @param string $nom  Le nom de la préférence.
 * @return mixed
 */
function preference($nom)
{
	$id = verifier('connecte') ? $_SESSION['id'] : 0;
	
	//Si la préférence est déjà en session
	if (isset($_SESSION['prefs'][$nom]))
	{
		return $_SESSION['prefs'][$nom];
	}
	
	//Sinon on les récupère toutes et on les met en session.
	if ($preferences = \Doctrine_Core::getTable('UserPreference')->getById($id))
	{
		$preferences->apply();
		if (isset($_SESSION['prefs'][$nom]))
		{
			return $_SESSION['prefs'][$nom];
		}
		
		$container = \Container::getInstance();
		if ($container->has('logger'))
		{
			$container->get('logger')->warn(sprintf(
				'La préférence "%s" n\'existe pas.', $nom
			));
		}
		return false;
	}
}

/**
 * Envoie un mail.
 *
 * @param string $destinataire_adresse  L'adresse du destinataire.
 * @param string $destinataire_nom      Le nom du destinataire.
 * @param string $objet                 L'objet du message.
 * @param string $message_html          Le message formaté en HTML.
 * @return bool
 */
function send_mail($destinataire_adresse, $destinataire_nom, $objet, $message_html)
{
    $apiKey = getenv('SENDGRID_API_KEY');
    if (!$apiKey) {
        return false;
    }

    $email = new \SendGrid\Mail\Mail();
    $email->setFrom('contact@zcorrecteurs.fr', 'Contact des zCorrecteurs');
    $email->setSubject($objet);
    $email->addTo($destinataire_adresse, $destinataire_nom);
    $email->addContent("text/html", $message_html);
    $sendgrid = new \SendGrid($apiKey);
    try {
        $response = $sendgrid->send($email);

        return $response->statusCode() >= 200 && $response->statusCode() < 300;
    } catch (Exception $e) {
        \Sentry\captureException($e);
        return false;
    }
}

/**
 * Remplace les longs isset($_POST[x], $_POST[y]...
 *
 * @author mwsaz
 * @param  array	$arr  Array des clés à vérifier
 * @return bool
 */
function check_post_vars($a)
{
	foreach((is_array($a) ? $a : func_get_args()) as $arg)
		if(!isset($_POST[$arg]))
			return false;
	return true;
}

function array_trim($vars, $index = null)
{
	if ($index != null)
	{
		if (!is_array($index))
			$index = array($index);

		$v2 = array();
		foreach ($index as $ind)
			$v2[$ind] = $vars[$ind];
		$vars = $v2;
	}

	foreach ($vars as &$var)
		$var = trim($var);
	return $vars;
}

/**
 * Génération d'un objet réponse à partir d'un nom de template et de variables
 * à y insérer.
 *
 * @param string $template  Le nom du template.
 * @param array $vars       Variables à remplacer.
 * @param array $headers    Options pour personnaliser la réponse.
 * @return Response
 */
function render_to_response($template = array(), array $vars = array(), array $headers = array())
{
	//DÉPRÉCIÉ : le premier paramètre peut-être omis.
	if (is_array($template) && $vars == array())
	{
		$vars = $template;
		$bundle = Container::getService('request')->attributes->get('_bundle');
		$action = Container::getService('request')->attributes->get('_action');
		$template = $bundle.'::'.lcfirst(\Util_Inflector::camelize($action)).'.html.php';
	}

	$dispatcher = \Container::getService('event_dispatcher');
	$event = new FilterVariablesEvent($vars);
	$dispatcher->dispatch(TemplatingEvents::FILTER_VARIABLES, $event);	
	$vars = $event->getAll();	
		
	//Register resources.
	$event = new FilterResourcesEvent(\Container::getService('zco_vitesse.resource_manager'), \Container::getService('zco_vitesse.javelin'));
	$dispatcher->dispatch(TemplatingEvents::FILTER_RESOURCES, $event);

	//Template rendering.
	$engine = \Container::getService('templating');
		
	return new Response($engine->render($template, $vars), 200, $headers);
}

function render_to_string($template = array(), array $vars = array())
{
	//First parameter can be omitted.
	if (is_array($template) && $vars == array())
	{
		$vars = $template;
		$bundle = Container::getService('request')->attributes->get('_bundle');
		$action = Container::getService('request')->attributes->get('_action');
		$template = $bundle.'::'.lcfirst(\Util_Inflector::camelize($action)).'.html.php';
	}
	
	$engine = \Container::getService('templating');
	return $engine->render($template, $vars);
}

/**
 * Retourne l'équivalent en octets de la sortie de sizeformat
 * @param string $size  Le nombre à formater.
 * @return string Le nombre formaté.
 */
function sizeint($size)
{
	$sint = (int)$size;
	if((string)$sint != (string)$size) // Unite à la fin ?
	{
		$unite = substr($size, strlen($sint));
		if($unite[0] == 'K')
			$size = $sint * 1024;
		elseif($unite[0] == 'M')
			$size = $sint * 1024 * 1024;
		elseif($unite[0] == 'G')
			$size = $sint * 1024 * 1024 * 1024;
	}
	return $size;
}

/**
 * Fonction permettant la correction des « s » (singulier / pluriel)
 * Exemple : 'chev' . pluriel(3, 'aux', 'al') affiche 'chevaux'
 *
 * @author vincent1870, Zopieux
 * @param  integer $nb     Le nombre à tester
 * @param  string $alt     Le pluriel à afficher
 * @param  string $normal  La forme singulière
 * @return array
 */
function pluriel($nb, $alt = 's', $normal = '')
{
	return $nb > 1 ? $alt : $normal;
}

/**
 * Constantes utiles par la suite.
 */
define('DATETIME', 0);
define('DATE', 1);
define('MAJUSCULE', 2);
define('MINUSCULE', 3);

/**
 * Transforme une date en une une date relative (Hier, dans 20min…) ou la
 * formate en tenant compte du décalage horaire de l'utilisateur actuel.
 *
 * @author mwsaz
 * @param string|int $dateheure  Timestamp ou date compréhensible par strtotime
 * @param integer    $casse      MAJUSCULE ou MINUSCULE, selon la casse de la première lettre
 * @param integer    $format     DATE ou DATETIME, pour afficher ou non l'heure avec la date
 * @return string
 */
function dateformat($dateHeure, $casse = MAJUSCULE, $format = DATETIME)
{
	//Omission ou inversion du second paramètre.
	if ($casse === DATE || $casse === DATETIME)
	{
		$_format = $format;
		$format = $casse;
		$casse = in_array($_format, array(MINUSCULE, MAJUSCULE)) ? $_format : MAJUSCULE;
	}

	if (!is_numeric($dateHeure))
	{
	    if (strpos($dateHeure, '0000-00-00') === 0)
	    {
	        $dateHeure = 0;
	    }
	    else
	    {
		    $dateHeure = strtotime($dateHeure);
	    }
	}

	$casse = $casse === MAJUSCULE ? 'ucfirst' : 'sprintf';
	$out = '';
	
	if (!$dateHeure)
	{
	    return $casse('jamais');
	}

	// Gestion du décalage
	static $decalage = false;
	if ($decalage === false)
	{
		$decalage = preference('time_difference');
		// Les timestamps sont enregistrés en GMT+1 dans la base de données
		$decalage -= 3600;
	}

	// Dates relatives
	$difference = time() - $dateHeure;
	$aujourdhui = mktime(0, 0, 0) - $decalage;

	//La différence en nombre de jours est fort imprécise (pas de prise en 
	//compte des années bissextiles et des mois à 31 jours) mais suffit pour 
	//ce dont on a besoin : savoir si on est à 0, 1, 2 ou 3 jours de décalage.
	$jours = abs(
		((int) date('d', $dateHeure) - (int) date('d', $aujourdhui)) 
		+ ((int) date('m', $dateHeure) - (int) date('m', $aujourdhui)) * 30
		+ ((int) date('Y', $dateHeure) - (int) date('Y', $aujourdhui)) * 365
	);

	if (0 === $jours) // Même jour
	{
		// ±4h autour de maintenant
		if ($format === DATETIME && abs($difference) < 3600 * 4)
		{
			$s = abs($difference) % 60;
			$m = (int)(abs($difference) / 60);
			$h = (int)($m / 60);
			$m %= 60;

			$out = '';

			if($h > 0)     $out = $h.' h '.($m < 10 ? '0' : '').$m;
			elseif($m > 0) $out = $m.' min';
			elseif($s > 0) $out = $s.' s';

			return $out ? $casse($difference < 0 ? 'dans' : 'il y a').' '.$out
			            : $casse('maintenant');
		}

		$out = 'aujourd\'hui';
	}
	elseif ($jours >= -1 && $jours < 2)
		$out = $difference < 0 ? 'demain' : 'hier';
	elseif($jours >= -2 && $jours < 3)
		$out = $difference < 0 ? 'après-demain' : 'avant-hier';
	else
	{
		$out = 'le '.date('d/m/Y', $dateHeure + $decalage);
	}

	if ($format === DATETIME)
		$out .= (is_numeric(substr($out, -1)) ? '' : ',')
			.' à '.date('H \\h i', $dateHeure + $decalage);

	return $casse($out);
}

/**
 * Formate une taille en octets nombre de façon agréable pour l'affichage.
 *
 * @param float $size  Le nombre à formater.
 * @return string Le nombre formaté.
 */
function sizeformat($size)
{
	$size = sizeint($size);
	if($size < 1024)		return $size.' o';
	if(($size /= 1024) < 1024)	return round($size, 2).' Ko';
	if(($size /= 1024) < 1024)	return round($size, 2).' Mo';
	if(($size /= 1024) < 1024)	return round($size, 2).' Go';
	if(($size /= 1024) < 1024)	return round($size, 2).' To';
	if(($size /= 1024) < 1024)	return round($size, 2).' Po';
	if(($size /= 1024) < 1024)	return round($size, 2).' Eo';
	if(($size /= 1024) < 1024)	return round($size, 2).' Zo';
	return round($size, 2).' Yo';
}
