<?php
/**
 * Liste de fonctions utilisées un peu partout sur le site.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 *         DJ Fox <djfox@zcorrecteurs.fr>
 *         Savageman <savageman@zcorrecteurs.fr>
 */

/**
 * Génère un fil d'Ariane.
 * 
 * @param integer $id			L'id de la catégorie de base.
 * @param array $enfants		Les éléments additionnels à ajouter.
 * @return string				L'arbre généré.
 */
function fil_ariane($id = null, $enfants = array())
{
	$appendTitle = !($id === null && empty($enfants));
	
	//Détection de l'id de catégorie de base si besoin
	if (!is_numeric($id) && $id !== null)
	{
		$enfants = $id;
	}
	if (is_null($id) || !is_numeric($id))
	{
		$id = GetIDCategorieCourante();
	}

	$ListerParents = ListerParents($id, true);
	if (empty($ListerParents))
	{
		$ListerParents = ListerParents(GetIDCategorie('informations'), false);
	}
    
	$items = array();
	$url = '';

	//Ajout automatique des parents
	foreach ($ListerParents as $i => $p)
	{
		if (!empty($p['cat_url']) && ($appendTitle || $i < count($ListerParents) - 1))
		{
			if(!preg_match('`\.`', $url))
				$url .= FormateURLCategorie($p['cat_id']);
			else
				$url = FormateURLCategorie($p['cat_id']);
			$items[] = '<a href="'.$url.'">'.htmlspecialchars($p['cat_nom']).'</a>';
		}
		else
		{
			$items[] = htmlspecialchars($p['cat_nom']);
		}
	}

	//Ajout des enfants à la main
	if (!is_array($enfants))
	{
		$enfants = array($enfants);
	}
	foreach ($enfants as $cle => $valeur)
	{
		if(!empty($cle))
		{
			$items[] = '<a href="'.$valeur.'">'.$cle.'</a>';
		}
		else
		{
			$items[] = $valeur;
		}
	}

	Page::$fil_ariane = $items;
}

/**
 * Applique automatiquement les règles typographiques françaises de base à un
 * texte, à savoir entre autres les guillemets français et les espaces
 * insécables.
 *
 * @param string $texte			Le texte à formater.
 * @return string				Le texte formaté.
 */
function typo($texte)
{
	static $trans;

	// Nettoyer 160 = nbsp ; 187 = raquo ; 171 = laquo ; 176 = deg ;
	// 147 = ldquo; 148 = rdquo; ' = zouli apostrophe
	if (!$trans)
	{
		$trans = array(
			"'" => "&#8217;",
			"&nbsp;" => "~",
			"&raquo;" => "&#187;",
			"&laquo;" => "&#171;",
			"&rdquo;" => "&#8221;",
			"&ldquo;" => "&#8220;",
			"&deg;" => "&#176;"
		);

		$chars = array(160 => '~', 187 => '&#187;', 171 => '&#171;', 148 => '&#8221;', 147 => '&#8220;', 176 => '&#176;');
		$chars_trans = array_keys($chars);
		$chars = array_values($chars);
		//$chars_trans = implode(' ',array_map('chr',$chars_trans));
		//$chars_trans = unicode2charset(charset2unicode($chars_trans, 'iso-8859-1', 'forcer'));
		//$chars_trans = explode(" ",$chars_trans);

		foreach ($chars as $k=>$r)
		{
			$trans[$chars_trans[$k]] = $r;
		}
	}

	$texte = strtr($texte, $trans);

	$cherche1 = array(
		/* 1 */ 	'/((?:^|[^\#0-9a-zA-Z\&])[\#0-9a-zA-Z]*)\;/S',
		/* 2 */		'/&#187;| --?,|(?::| %)(?:\W|$)/S',
		/* 3 */		'/([^[<(])([!?][!?\.]*)/iS',
		/* 4 */		'/&#171;|(?:M(?:M?\.|mes?|r\.?)|[MnN]&#176;) /S'
	);
	$remplace1 = array(
		/* 1 */		'\1~;',
		/* 2 */		'~\0',
		/* 3 */		'\1~\2',
		/* 4 */		'\0~'
	);
	$texte = preg_replace($cherche1, $remplace1, $texte);
	$texte = preg_replace("/ *~+ */S", "~", $texte);

	$cherche2 = array(
		'/([^-\n]|^)--([^-]|$)/S',
		',(http|https|ftp|mailto)~((://[^"\'\s\[\]\}\)<>]+)~([?]))?,S',
		'/~/'
	);
	$remplace2 = array(
		'\1&mdash;\2',
		'\1\3\4',
		'&nbsp;'
	);
	$texte = preg_replace($cherche2, $remplace2, $texte);

	return $texte;
}

function extrait($texte, $taille = 50)
{
	$extrait = wordwrap($texte, $taille);
	$extrait = explode("\n", $extrait);
	if($extrait[0] != $texte)
		$extrait[0] .= '…';
	return $extrait[0];
}

/**
 * Réalise un diff entre deux chaines de caractères.
 *
 * @param string	$old			L'ancienne chaine de caractères.
 * @param string	$new			La nouvelle chaine de caractères.
 * @param bool		$new			Renvoyer le diff brut ?
 */
function diff($old, $new, $raw = false)
{
	include_once(BASEPATH.'/vendor/diff/diff.php');
	include_once(BASEPATH.'/vendor/diff/htmlformatter.php');

	$old = explode("\n", $raw ? $old : strip_tags($old));
	$new = explode("\n", $raw ? $new : strip_tags($new));

	$diff = new Diff($old, $new);
	if($raw)
		$formatter = new UnifiedDiffFormatter();
	else	$formatter = new HTMLDiffFormatter();

	return $formatter->format($diff);
}

/**
 * Affiche un message de confirmation
 *
 * @param string $message		Le message à afficher
 * @return void
 */
function afficher_message($message)
{
	echo '<p class="UI_infobox">'.$message.'</p>';
}

/**
 * Affiche un message d'erreur
 *
 * @param string $message		Le message à afficher
 * @return void
 */
function afficher_erreur($message)
{
	echo '<p class="UI_errorbox">'.$message.'</p>';
}
