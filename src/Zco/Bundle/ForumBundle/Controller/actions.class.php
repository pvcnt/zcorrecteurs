<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\CategoriesBundle\Domain\CategoryDAO;

class ForumActions extends Controller
{
	public function __construct()
	{
	    \Container::get('zco_core.resource_manager')->requireResource(
		    '@ZcoForumBundle/Resources/public/css/forum.css'
		);
	}

	public function executeAjaxAutocompleteTitre()
	{
		$dbh = \Doctrine_Manager::connection()->getDbh();

		$stmt = $dbh->prepare("SELECT sujet_titre, sujet_forum_id
			FROM zcov2_forum_sujets
			WHERE sujet_titre LIKE ".$dbh->quote($_POST['titre'].'%'));
		$stmt->execute();
		$donnees = $stmt->fetchAll();
		$retour = array();
		foreach($donnees as $row)
			if(verifier('voir_sujets', $row['sujet_forum_id']))
				$retour[] = $row['sujet_titre'];
		$response = new Response;
		$response->headers->set('Content-type',  'application/json');
		$response->setContent(json_encode($retour));
		return $response;
	}

	public function executeAjaxDeplacementMassif()
	{
		//Inclusion du modèle
		include(BASEPATH.'/src/Zco/Bundle/ForumBundle/modeles/categories.php');

		if(verifier('deplacer_sujets', $_GET['f']))
		{
			$CategoriesForums = ListerCategoriesForum();
			if($CategoriesForums)
			{
				$ret = '<select name="forum_cible">';
				$i=0;
				foreach($CategoriesForums as $clef => $valeur)
				{
					if($valeur['cat_niveau'] == 2 && $_GET['f'] != $valeur['cat_id'])
					{
						//Dans ce if on affiche que les catégories
						if($clef > 1)
						{
							$ret .= '</optgroup>';
						}
						$categorie_deplacement = $valeur['cat_id'];
						$ret .= '<optgroup label="'.htmlspecialchars($valeur['cat_nom']).'">';
					}
					//Ici on affiche que les forums
					else
					{
						$ret .= '<option value="'.$valeur['cat_id'].'">'.htmlspecialchars($valeur['cat_nom']).'</option>';
					}
					$i++;
				}
				$ret .= '</optgroup></select>';
			}
			else
			{
				$ret = 'Ce forum n\'existe pas.';
			}
		}
		else
		{
			$ret = 'Vous n\'avez pas les droits requis ou un paramètre a été omis.';
		}
        
		return new Response($ret);
	}

	public function executeAjaxDeplacerSujet()
	{
		//Inclusion du modèle
		include(BASEPATH.'/src/Zco/Bundle/ForumBundle/modeles/categories.php');

		if(!empty($_POST['fofo_actuel']) AND is_numeric($_POST['fofo_actuel']) AND verifier('deplacer_sujets', $_POST['fofo_actuel']) AND !empty($_POST['id']) AND is_numeric($_POST['id']))
		{
			$CategoriesForums = ListerCategoriesForum();
			if($CategoriesForums)
			{
				$ret = '
				<form action="deplacer-'.$_POST['id'].'.html" method="post">
				<select name="forum_cible">
				';
				$i=0;
				foreach($CategoriesForums as $clef => $valeur)
				{
					if($valeur['cat_niveau'] == 2 && $_POST['fofo_actuel'] != $valeur['cat_id'])
					{
						//Dans ce if on affiche que les catégories
						if($i > 1)
						{
							$ret .= '</optgroup>';
						}
						$categorie_deplacement = $valeur['cat_id'];
						$ret .= '<optgroup label="'.htmlspecialchars($valeur['cat_nom']).'">';
					}
					//Ici on affiche que les forums
					else
					{
						$ret .= '<option value="'.$valeur['cat_id'].'">'.htmlspecialchars($valeur['cat_nom']).'</option>';
						if (!empty($valeur['sous_forums']))
						{
							foreach ($valeur['sous_forums'] as $forum)
							{
								$ret .= '<option value="'.$forum['cat_id'].'">'.str_pad('', ($forum['cat_niveau']-3)*3, '...').htmlspecialchars($forum['cat_nom']).'</option>';
							}
						}
					}
					$i++;
				}
				$ret .= '
				</optgroup></select>
				<input type="submit" value="Déplacer" />
				</form>
				';
			}
			else
			{
				$ret = 'Ce forum n\'existe pas.';
			}

		}
		else
		{
			$ret = 'Vous n\'avez pas les droits requis ou un paramètre a été omis.';
		}
		return new Response($ret);
	}

	public function initSujet()
	{
		include(__DIR__.'/../modeles/sujets.php');

		//Compatibilité
		if(!isset($_GET['s'])) $_GET['s'] = $_GET['id'];
		if(empty($_GET['id'])) $_GET['id'] = $_GET['s'];

		//--- Récupération des infos sur le sujet ---
		if(empty($_GET['id']) || !is_numeric($_GET['id']))
			throw new NotFoundHttpException();
		else
		{
			$InfosSujet = InfosSujet($_GET['id']);
			$InfosForum = CategoryDAO::InfosCategorie($InfosSujet['sujet_forum_id']);
			if(empty($InfosSujet))
				throw new NotFoundHttpException();
		}

		//--- Modification des balises méta ---
		Page::$titre = htmlspecialchars($InfosSujet['sujet_titre']);

		return array($InfosSujet, $InfosForum);
	}
}
