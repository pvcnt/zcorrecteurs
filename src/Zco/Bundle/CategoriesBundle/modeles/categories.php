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

use Doctrine\Common\Cache\Cache;

/**
 * Ajoute une catégorie.
 *
 * @param Cache $cache
 * @return integer				L'id de la catégorie insérée.
 */
function AjouterCategorie(Cache $cache)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//Infos sur le parent
	if(!empty($_POST['parent']) && is_numeric($_POST['parent']))
	{
		$InfosParent = InfosCategorie($_POST['parent']);
	}

	//MaJ des autres catégories si besoin
	if(!empty($InfosParent))
	{
		//Borne droite
		$stmt = $dbh->prepare("UPDATE zcov2_categories SET cat_droite = cat_droite +2 WHERE cat_droite >= :droite");
		$stmt->bindParam(':droite', $InfosParent['cat_droite']);
		$stmt->execute();

		$stmt->closeCursor();

		//Borne gauche
		$stmt = $dbh->prepare("UPDATE zcov2_categories SET cat_gauche = cat_gauche +2 WHERE cat_gauche >= :droite");
		$stmt->bindParam(':droite', $InfosParent['cat_droite']);
		$stmt->execute();

		$stmt->closeCursor();
	}

	//Insertion de la nouvelle catégorie
	$niveau = !empty($InfosParent) ? $InfosParent['cat_niveau'] + 1 : 0;
	$gauche = !empty($InfosParent) ? $InfosParent['cat_droite'] : 1;
	$droite = !empty($InfosParent) ? $InfosParent['cat_droite'] + 1 : 2;
	$type = isset($_POST['type']) && in_array($_POST['type'], array(MAP_FIRST, MAP_ALL)) ? $_POST['type'] : MAP_FIRST;

	$stmt = $dbh->prepare("INSERT INTO zcov2_categories(cat_nom, cat_description, " .
			"cat_url, cat_gauche, cat_droite, cat_niveau, cat_reglement, cat_map, " .
			"cat_map_type, cat_redirection, cat_keywords, cat_disponible_ciblage, cat_ciblage_actions) " .
			"VALUES(:nom, :desc, :url, :gauche, :droite, :niveau, :reglement, " .
			":map, :type_map, :url_redir, :keywords, :ciblage, :ciblage_actions)");
	$stmt->bindParam(':nom', $_POST['nom']);
	$stmt->bindParam(':url', $_POST['url']);
	$stmt->bindParam(':url_redir', $_POST['url_redir']);
	$stmt->bindParam(':desc', $_POST['description']);
	$stmt->bindParam(':gauche', $gauche);
	$stmt->bindParam(':droite', $droite);
	$stmt->bindParam(':niveau', $niveau);
	$stmt->bindParam(':reglement', $_POST['texte']);
	$stmt->bindParam(':map', $_POST['map']);
	$stmt->bindParam(':type_map', $type);
	$stmt->bindParam(':keywords', $_POST['keywords']);
	$stmt->bindValue(':ciblage', isset($_POST['disponible_ciblage']) ? 1 : 0);
	$stmt->bindValue(':ciblage_actions', isset($_POST['ciblage_actions']) ? 1 : 0);

	$stmt->execute();
	$stmt->closeCursor();
	$id_cat = $dbh->lastInsertId();

	//Insertion des droits si besoin
	if(!empty($_POST['cat']) && is_numeric($_POST['cat']))
	{
		//On récupère les droits sur cette catégorie
		$stmt = $dbh->prepare("SELECT gd_id_droit, gd_id_groupe, gd_valeur " .
				"FROM zcov2_groupes_droits " .
				"WHERE gd_id_categorie = :id");
		$stmt->bindParam(':id', $_POST['cat']);

		$stmt->execute();
		$droits = $stmt->fetchAll();
		$stmt->closeCursor();

		$groupes = array();

		//Et on insère les droits
		foreach($droits as &$d)
		{
			$stmt = $dbh->prepare("INSERT INTO zcov2_groupes_droits(gd_id_droit, gd_id_groupe, gd_id_categorie, gd_valeur) " .
					"VALUES(:droit, :groupe, :cat, :valeur)");
			$stmt->bindParam(':cat', $id_cat);
			$stmt->bindParam(':groupe', $d['gd_id_groupe']);
			$stmt->bindParam(':droit', $d['gd_id_droit']);
			$stmt->bindParam(':valeur', $d['gd_valeur']);

			$stmt->execute();
			$stmt->closeCursor();
			$groupes[$d['gd_id_groupe']] = true;
		}
	}
    $cache->delete('categories');
	return $id_cat;
}

/**
 * Edite une catégorie.
 * @param $id					L'id de la catégorie.
 * @return void
 */
function EditerCategorie($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//Infos sur la catégorie
	$InfosCategorie = InfosCategorie($id);

	//Infos sur le nouveau parent
	if(!empty($_POST['parent']) && is_numeric($_POST['parent']))
	{
		$InfosNouveauParent = InfosCategorie($_POST['parent']);
	}
	$ListerParents = ListerParents($InfosCategorie);
	if(empty($ListerParents))
		$ListerParents[0]['cat_id'] = 0;

	//Mise à jour du nom et de la description
	$type = isset($_POST['type']) && in_array($_POST['type'], array(MAP_FIRST, MAP_ALL)) ? $_POST['type'] : MAP_FIRST;

	$stmt = $dbh->prepare("UPDATE zcov2_categories " .
			"SET cat_nom = :nom, cat_description = :desc, cat_url = :url, " .
			"cat_reglement = :reglement, cat_map = :map, " .
			"cat_map_type = :type_map, " .
			"cat_redirection = :url_redir, cat_keywords = :keywords, ".
			"cat_disponible_ciblage = :ciblage, cat_ciblage_actions = :ciblage_actions " .
			"WHERE cat_id = :id");
	$stmt->bindParam(':nom', $_POST['nom']);
	$stmt->bindParam(':desc', $_POST['description']);
	$stmt->bindParam(':id', $InfosCategorie['cat_id']);
	$stmt->bindParam(':url', $_POST['url']);
	$stmt->bindParam(':url_redir', $_POST['url_redir']);
	$stmt->bindParam(':reglement', $_POST['texte']);
	$stmt->bindParam(':map', $_POST['map']);
	$stmt->bindParam(':type_map', $type);
	$stmt->bindParam(':keywords', $_POST['keywords']);
	$stmt->bindValue(':ciblage', isset($_POST['disponible_ciblage']) ? 1 : 0);
	$stmt->bindValue(':ciblage_actions', isset($_POST['ciblage_actions']) ? 1 : 0);
	$stmt->execute();
	$stmt->closeCursor();

	//Si le parent change, on déplace la catégorie
	if($ListerParents[count($ListerParents)-1]['cat_id'] != $_POST['parent'] && !empty($InfosNouveauParent))
	{
		// On va simuler une suppression/réinsertion de la catégorie à déplacer (au lieu de supprimer, on la déplace dans des bornes négatives)
		$BorneDroite = $InfosCategorie['cat_droite'];
		$NombreElements = $InfosCategorie['cat_droite'] - $InfosCategorie['cat_gauche'] + 1;

		// On déplace la catégorie à modifier dans les négatifs
		$stmt = $dbh->prepare("
		UPDATE zcov2_categories
		SET cat_droite = cat_droite - :droite,
		cat_gauche = cat_gauche - :droite
		WHERE cat_gauche >= :gauche AND cat_droite <= :droite");
		$stmt->bindParam(':droite', $InfosCategorie['cat_droite']);
		$stmt->bindParam(':gauche', $InfosCategorie['cat_gauche']);
		$stmt->execute();
		$stmt->closeCursor();

		// Maintenant que la catégorie est "supprimé" on update les bornes
		$stmt = $dbh->prepare("
		UPDATE zcov2_categories
		SET cat_gauche = cat_gauche - :nbelem
		WHERE cat_gauche >= :gauche");
		$stmt->bindParam(':nbelem', $NombreElements);
		$stmt->bindParam(':gauche', $InfosCategorie['cat_gauche']);
		$stmt->execute();
		$stmt->closeCursor();

		$stmt = $dbh->prepare("
		UPDATE zcov2_categories
		SET cat_droite = cat_droite - :nbelem
		WHERE cat_droite >= :gauche");
		$stmt->bindParam(':nbelem', $NombreElements);
		$stmt->bindParam(':gauche', $InfosCategorie['cat_gauche']);
		$stmt->execute();
		$stmt->closeCursor();

		// On récupère les nouvelles informations du parent
		// (au cas où il aurait changé de borne avec les modifs précédentes)
		Container::getService('zco_core.cache')->Delete('categories');
		$InfosNouveauParent = InfosCategorie($_POST['parent']);

		// Insertion
		$stmt = $dbh->prepare("
		UPDATE zcov2_categories
		SET cat_droite = cat_droite + :nbelem
		WHERE cat_droite >= :droite");
		$stmt->bindParam(':nbelem', $NombreElements);
		$stmt->bindParam(':droite', $InfosNouveauParent['cat_droite']);
		$stmt->execute();
		$stmt->closeCursor();

		$stmt = $dbh->prepare("
		UPDATE zcov2_categories
		SET cat_gauche = cat_gauche + :nbelem
		WHERE cat_gauche >= :droite");
		$stmt->bindParam(':nbelem', $NombreElements);
		$stmt->bindParam(':droite', $InfosNouveauParent['cat_droite']);
		$stmt->execute();
		$stmt->closeCursor();

		// Mise à jour des niveaux
		$NouveauNiveau = $InfosNouveauParent['cat_niveau'] + 1 - $InfosCategorie['cat_niveau'];
		$stmt = $dbh->prepare("
		UPDATE zcov2_categories
		SET cat_niveau = cat_niveau + :niveau
		WHERE cat_droite <=0 AND cat_gauche <= 0");
		$stmt->bindParam(':niveau', $NouveauNiveau);
		$stmt->execute();
		$stmt->closeCursor();

		// On remonte l'élément dans les négatifs à sa nouvelle place
		$NouvelleBorne = $InfosNouveauParent['cat_droite'] + $NombreElements - 1;
		$stmt = $dbh->prepare("
		UPDATE zcov2_categories
		SET cat_gauche = cat_gauche + :borne,
		cat_droite = cat_droite + :borne
		WHERE cat_droite <= 0 AND cat_gauche <=0");
		$stmt->bindParam(':borne', $NouvelleBorne);
		$stmt->execute();
		$stmt->closeCursor();
	}

	//Archivage ou non dans le cas d'un forum
	isset($_POST['archiver']) ? ArchiverForum($id) : DesarchiverForum($id);

	//On supprime les caches de catégorie
	Container::getService('zco_core.cache')->Delete('categories');
}

/**
 * Supprime une catégorie.
 * @param integer $id					L'id de la catégorie à supprimer.
 * @return void
 */
function SupprimerCategorie($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//Infos sur la catégorie
	$InfosCategorie = InfosCategorie($id);

	//Suppression de la catégorie
	$stmt = $dbh->prepare("DELETE FROM zcov2_categories WHERE cat_id = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$stmt->closeCursor();

	//MaJ des autres catégories
	//Borne gauche
	$stmt = $dbh->prepare("UPDATE zcov2_categories SET cat_gauche = cat_gauche -2 WHERE cat_gauche >= :gauche");
	$stmt->bindParam(':gauche', $InfosCategorie['cat_gauche']);
	$stmt->execute();
	$stmt->closeCursor();

	//Borne droite
	$stmt = $dbh->prepare("UPDATE zcov2_categories SET cat_droite = cat_droite -2 WHERE cat_droite >= :gauche");
	$stmt->bindParam(':gauche', $InfosCategorie['cat_gauche']);
	$stmt->execute();
	$stmt->closeCursor();

	//On supprime tous les droits associés à cette catégorie
	$stmt = $dbh->prepare("DELETE FROM zcov2_groupes_droits WHERE gd_id_categorie = :id");
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$stmt->closeCursor();

	Container::getService('zco_core.cache')->Delete('categories');
}

/**
 * Récupère les infos sur une catégorie.
 * @param integer $id						L'id de la catégorie.
 * @param boolean $verif_droits				Doit-on vérifier les droits en fonction du groupe (oui par défaut) ?
 * @return array
 */
function InfosCategorie($id, $verif_droits = false)
{
	$ListerCategories = ListerCategories($verif_droits);
	if(array_key_exists($id, $ListerCategories))
		return $ListerCategories[$id];
	else
		return array();
}

/**
 * Retourne l'arbre des catégories, récupéré du cache s'il existe, régénéré sinon.
 * @param boolean $verif_droits		Doit-on vérifier les droits en fonction du groupe (non par défaut) ?
 * @return array
 */
function ListerCategories($verif_droits = false)
{
	static $retour = null;
	static $retour_avec_verif = null;

	if (!$retour)
	{
		if(!($retour = Container::getService('zco_core.cache')->fetch('categories')))
		{
			$dbh = Doctrine_Manager::connection()->getDbh();
			$retour = array();

			$stmt = $dbh->prepare("SELECT cat_id, cat_nom, cat_description, " .
					"cat_gauche, cat_droite, cat_niveau, cat_url, cat_reglement, " .
					"cat_redirection, cat_map, cat_map_type, cat_nb_elements, " .
					"cat_last_element, cat_keywords, cat_disponible_ciblage, cat_ciblage_actions, " .
					" cat_archive FROM zcov2_categories " .
					"ORDER BY cat_gauche ASC");

			$stmt->execute();
			$ListerCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

			foreach($ListerCategories as $c)
				$retour[$c['cat_id']] = $c;

			Container::getService('zco_core.cache')->save('categories', $retour, 0);
		}
	}

	if($verif_droits == true && !$retour_avec_verif)
	{
		$retour_avec_verif = $retour;
		foreach($retour_avec_verif as $cle => $valeur)
		{
			if(!verifier('voir', $cle))
				unset($retour_avec_verif[$cle]);
		}
	}
	return $verif_droits ? $retour_avec_verif : $retour;
}

/**
 * Retourne les catégories parentes d'une catégorie.
 * @param array|integer $InfosCategorie		Les infos sur la catégorie, ou son ID.
 * @param boolean $include					Doit-on inclure la catégorie demandée (non par défaut) ?
 * @param boolean $verif_droits				Doit-on vérifier les droits en fonction du groupe (oui par défaut) ?
 * @return array
 */
function ListerParents($InfosCategorie, $include = false, $verif_droits = false)
{
	if(!is_array($InfosCategorie))
		$InfosCategorie = InfosCategorie($InfosCategorie, $verif_droits);
	if(empty($InfosCategorie)) return array();

	$ListerCategories = ListerCategories($verif_droits);
	$retour = array();

	//On ajoute les catégories désirées
	foreach($ListerCategories as $cle => $valeur)
	{
		if($valeur['cat_gauche'] < $InfosCategorie['cat_gauche'] &&
		$valeur['cat_droite'] > $InfosCategorie['cat_droite'] &&
		$valeur['cat_niveau'] < $InfosCategorie['cat_niveau'])
			$retour[] = $valeur;
	}

	//On ajoute la catégorie si demandé
	if($include == true)
		$retour[] = $InfosCategorie;

	return $retour;
}

/**
 * Retourne les catégories enfants d'une catégorie.
 * @param array|integer $InfosCategorie		Les infos sur la catégorie, ou son ID.
 * @param boolean $include			Doit-on inclure la catégorie demandée (non par défaut) ?
 * @param boolean $verif_droits			Doit-on vérifier les droits en fonction du groupe (oui par défaut) ?
 * @return array
 */
function ListerEnfants($InfosCategorie, $include = false, $verif_droits = false)
{
	if(!is_array($InfosCategorie))
		$InfosCategorie = InfosCategorie($InfosCategorie);
	if(empty($InfosCategorie)) return array();
	$ListerCategories = ListerCategories($verif_droits);
	$retour = array();

	//On ajoute les catégories désirées
	foreach($ListerCategories as $cle => $valeur)
	{
		if($valeur['cat_gauche'] > $InfosCategorie['cat_gauche'] &&
		$valeur['cat_droite'] < $InfosCategorie['cat_droite'] &&
		$valeur['cat_niveau'] > $InfosCategorie['cat_niveau'])
			$retour[] = $valeur;
	}

	//On ajoute la catégorie si demandé
	if($include == true)
		$retour = array_merge(array($InfosCategorie), $retour);

	return $retour;
}

/**
 * Descend une catégorie.
 * @param array $InfosCategorie						Les infos sur la catégorie.
 * @return void
 */
function DescendreCategorie($InfosCategorie)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//Infos sur la catégorie à intervertir, si elle existe
	$stmt = $dbh->prepare("
	SELECT cat_id, cat_gauche, cat_droite, cat_niveau
	FROM zcov2_categories
	WHERE cat_gauche > :gauche AND cat_droite > :droite AND cat_niveau = :niveau
	ORDER BY cat_gauche ASC
	LIMIT 0, 1");
	$stmt->bindParam(':gauche', $InfosCategorie['cat_gauche']);
	$stmt->bindParam(':droite', $InfosCategorie['cat_droite']);
	$stmt->bindParam(':niveau', $InfosCategorie['cat_niveau']);
	$stmt->execute();
	$InfosRemplacant = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->closeCursor();

	//S'il y a une catégorie du même niveau, on descend
	if(!empty($InfosRemplacant))
	{
		// On va vérifier qu'elles ont un parent commun, sinon c'est pas bon non plus
		$ParentCategorieActu = ListerParents($InfosCategorie);
		$ParentCategorieCible = ListerParents($InfosRemplacant);
		if($ParentCategorieActu[count($ParentCategorieActu)-1]['cat_id']!=$ParentCategorieCible[count($ParentCategorieCible)-1]['cat_id'])
			return false;

		// On met la catégorie dans les négatifs
		$stmt = $dbh->prepare("
		UPDATE zcov2_categories
		SET cat_gauche = cat_gauche - :droite,
		cat_droite = cat_droite - :droite
		WHERE cat_gauche >= :gauche AND cat_droite <= :droite");
		$stmt->bindParam(':droite', $InfosCategorie['cat_droite']);
		$stmt->bindParam(':gauche', $InfosCategorie['cat_gauche']);
		$stmt->execute();
		$stmt->closeCursor();

		// On met le remplacant à monter à la place de la catégorie
		$DifferenceBorne = $InfosRemplacant['cat_gauche'] - $InfosCategorie['cat_gauche'];
		$stmt = $dbh->prepare("
		UPDATE zcov2_categories
		SET cat_gauche = cat_gauche - :diff,
		cat_droite = cat_droite - :diff
		WHERE cat_gauche >= :gauche AND cat_droite <= :droite");
		$stmt->bindParam(':diff', $DifferenceBorne);
		$stmt->bindParam(':gauche', $InfosRemplacant['cat_gauche']);
		$stmt->bindParam(':droite', $InfosRemplacant['cat_droite']);
		$stmt->execute();
		$stmt->closeCursor();

		// On met la catégorie à l'ancienne place du remplacant monté
		$stmt = $dbh->prepare("
		UPDATE zcov2_categories
		SET cat_gauche = cat_gauche + :diff,
		cat_droite = cat_droite + :diff
		WHERE cat_gauche <= 0 AND cat_droite <=0");
		$stmt->bindParam(':diff', $InfosRemplacant['cat_droite']);
		$stmt->execute();
		$stmt->closeCursor();

		//On supprime les caches de catégorie
		Container::getService('zco_core.cache')->Delete('categories');

		return true;
	}
	return false;
}

/**
 * Monte une catégorie.
 * @param array $InfosCategorie						Les infos sur la catégorie.
 * @return void
 */
function MonterCategorie($InfosCategorie)
{
	$dbh = Doctrine_Manager::connection()->getDbh();

	//Infos sur la catégorie à intervertir, si elle existe
	$stmt = $dbh->prepare("
	SELECT cat_id, cat_gauche, cat_droite, cat_niveau
	FROM zcov2_categories
	WHERE cat_gauche < :gauche AND cat_droite < :droite AND cat_niveau = :niveau
	ORDER BY cat_gauche DESC
	LIMIT 1");
	$stmt->bindParam(':gauche', $InfosCategorie['cat_gauche']);
	$stmt->bindParam(':droite', $InfosCategorie['cat_droite']);
	$stmt->bindParam(':niveau', $InfosCategorie['cat_niveau']);
	$stmt->execute();
	$InfosRemplacant = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->closeCursor();

	//S'il y a une catégorie du même niveau, on passe à la suite
	if(!empty($InfosRemplacant))
	{
		// On va vérifier qu'elles ont un parent commun, sinon c'est pas bon non plus
		$ParentCategorieActu = ListerParents($InfosCategorie);
		$ParentCategorieCible = ListerParents($InfosRemplacant);
		if($ParentCategorieActu[count($ParentCategorieActu)-1]['cat_id']!=$ParentCategorieCible[count($ParentCategorieCible)-1]['cat_id'])
			return false;

		// On met le remplacant dans les négatifs
		$stmt = $dbh->prepare("
		UPDATE zcov2_categories
		SET cat_gauche = cat_gauche - :droite,
		cat_droite = cat_droite - :droite
		WHERE cat_gauche >= :gauche AND cat_droite <= :droite");
		$stmt->bindParam(':droite', $InfosRemplacant['cat_droite']);
		$stmt->bindParam(':gauche', $InfosRemplacant['cat_gauche']);
		$stmt->execute();
		$stmt->closeCursor();

		// On met la catégorie à monter à la place du remplacant
		$DifferenceBorne = $InfosCategorie['cat_gauche'] - $InfosRemplacant['cat_gauche'];
		$stmt = $dbh->prepare("
		UPDATE zcov2_categories
		SET cat_gauche = cat_gauche - :diff,
		cat_droite = cat_droite - :diff
		WHERE cat_gauche >= :gauche AND cat_droite <= :droite");
		$stmt->bindParam(':diff', $DifferenceBorne);
		$stmt->bindParam(':gauche', $InfosCategorie['cat_gauche']);
		$stmt->bindParam(':droite', $InfosCategorie['cat_droite']);
		$stmt->execute();
		$stmt->closeCursor();

		// On met le remplacant à l'ancienne place de la catégorie montée
		$stmt = $dbh->prepare("
		UPDATE zcov2_categories
		SET cat_gauche = cat_gauche + :diff,
		cat_droite = cat_droite + :diff
		WHERE cat_gauche <= 0 AND cat_droite <=0");
		$stmt->bindParam(':diff', $InfosCategorie['cat_droite']);
		$stmt->execute();
		$stmt->closeCursor();

		//On supprime les caches de catégorie
		Container::getService('zco_core.cache')->Delete('categories');

		return true;
	}
	return false;
}

/**
 * Retourne une liste d'<option> HTML contenant toutes les catégories.
 * @param integer $id					La catégorie sélectionnée par défaut.
 * @param integer $parent				L'id de la catégorie dont on va lister les enfants.
 * @return string
 */
function GetListeCategories($id = null, $parent = null)
{
	$ListerCategories = is_null($parent) ? ListerCategories() : ListerEnfants(InfosCategorie($parent), false);
	$retour = '';

	foreach($ListerCategories as $c)
	{
		$marqueur = '';
		for($i = 0 ; $i < $c['cat_niveau'] ; $i++)
			$marqueur .= '.....';

		$retour .= '<option value="'.$c['cat_id'].'"';
		if(!is_null($id) && $id == $c['cat_id']) $retour .= ' selected="selected"';
		$retour .= '>'.$marqueur.' '.htmlspecialchars($c['cat_nom']).'</option>';
	}

	return $retour;
}

/**
 * Retourne l'id de la catégorie correspondant au module actuellement exécuté
 * (alias pour GetIDCategorie(null));
 * @return integer|null
 */
function GetIDCategorieCourante($plus_precis = false)
{
	return GetIDCategorie(null, $plus_precis);
}

/**
 * Retourne l'id de la catégorie correspondant à un module, par son nom.
 * @param string|null $module		Un nom de module dont on veut obtenir l'id correspondant.
 * @return integer|null
 */
function GetIDCategorie($module = null, $plus_precis = false)
{
	static $id_cats = array();

	if (!$module)
	{
        $module = Container::getService('request')->attributes->get('_module');
		if (empty($module))
		{
		    return 1;
		}
	}
	
	//Recherche basique.
	if($plus_precis == false)
	{
		if (array_key_exists($module, $id_cats) && !$plus_precis)
		{
			return $id_cats[$module];
		}

		//Sinon on parcourt l'arbre des catégories à la recherche de la nôtre.
		$ListerCategories = ListerCategories();
		foreach($ListerCategories as $c)
		{
			if(trim($c['cat_url'], '/') == $module)
			{
				$id_cats[$module] = $c['cat_id'];
				
				return $c['cat_id'];
			}
		}
		
		return null;
	}

	//Recherche pour retourner un ID de niveau plus bas si possible. Gros code
	//en dur pour tenir compte des spécificités de chaque catégorie.
	else
	{
	    $action = \Container::getService('request')->attributes->get('_action');
		if($module === 'blog' && $action === 'categorie')
			return !empty($_GET['id']) ? $_GET['id'] : null;
		elseif($module === 'forum' && in_array($action, array('categorie', 'forum')))
			return !empty($_GET['id']) ? $_GET['id'] : null;
		else
			return GetIDCategorie($module, false);
	}
}

/**
 * Formate l'url d'une catégorie enregistrée en base de données en url correcte.
 * @param integer $id					L'id de la catégorie.
 * @return string
 */
function FormateURLCategorie($id)
{
	$infos = InfosCategorie($id);
	if(empty($infos['cat_redirection']))
		return str_replace(array('%id%', '%id2%', '%nom%'), array($infos['cat_id'], !empty($_GET['id2']) ? $_GET['id2'] : 0, rewrite($infos['cat_nom'])), $infos['cat_url']);
	else
		return $infos['cat_redirection'];
}

/**
 * Met en archive un forum
 * @param	integer 	$id		L'id de la catégorie.
 * @return 	void
 */
function ArchiverForum($id)
{
	$dbh = Doctrine_Manager::connection()->getDbh();
	
	$req = $dbh->prepare("UPDATE zcov2_categories SET cat_archive = :archive WHERE cat_id = :id ");
	$req->bindParam(':id', $id);
	$req->bindValue(':archive', 1);
	$req->execute();
	$req->closeCursor();
		
	Container::getService('zco_core.cache')->Delete('categories');
}

/**
 * Enlève un forum des archives
 * @param 	integer 	$id		L'id de la catégorie.
 * @return 	void
 */
 function DesarchiverForum($id)
 {
 	$dbh = Doctrine_Manager::connection()->getDbh();
	
	$req = $dbh->prepare("UPDATE zcov2_categories SET cat_archive = :archive WHERE cat_id = :id");
	$req->bindParam(':id', $id);
	$req->bindValue(':archive', 0);
	$req->execute();
	$req->closeCursor();

	Container::getService('zco_core.cache')->Delete('categories');
 }

