<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
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

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Contrôleur gérant l'accueil des forums (listage des catégories + forums).
 *
 * @author Original DJ Fox <marthe59@yahoo.fr>
 */
class IndexAction extends ForumActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();

		//Redirection si demandé
		if(!empty($_POST['saut_forum']))
		{
			return new RedirectResponse('/forum/'.htmlspecialchars($_POST['saut_forum']));
		}
		
		//Mise à jour de la position sur le site.
		\Doctrine_Core::getTable('Online')->updateUserPosition($_SESSION['id'], 'ZcoForumBundle:index');

		//Inclusion du modèle
		include(dirname(__FILE__).'/../modeles/categories.php');
		include(dirname(__FILE__).'/../modeles/membres.php');

		$ListerCategories = ListerCategoriesForum(array());
		$derniere_lecture = DerniereLecture($_SESSION['id']);

		//Appel de la fonction lu / non-lu
		$Lu = array();
		if ($ListerCategories)
		{
			$nbIndex = 0;
			foreach ($ListerCategories as $cat)
			{
				//Si le forum est vide, l'image lu/non-lu sera une ampoule blanche.
				if ($cat['cat_last_element'] == 0)
				{
					$Lu[$cat['cat_id']] = array(
						'image' => 'lightbulb_off',
						'title' => 'Pas de nouvelles réponses, jamais participé'
					);
				}
				else
				{
					$Lu[$cat['cat_id']] = LuNonluCategorie(array(
						'lunonlu_utilisateur_id'   => $cat['lunonlu_utilisateur_id'],
						'lunonlu_sujet_id'         => $cat['lunonlu_sujet_id'],
						'lunonlu_message_id'       => $cat['lunonlu_message_id'],
						'lunonlu_participe'        => $cat['lunonlu_participe'],
						'sujet_dernier_message'    => $cat['message_id'],
						'date_dernier_message'     => $cat['message_timestamp'],
						'derniere_lecture_globale' => $derniere_lecture,
					));
				}
				
				if (!empty($_GET['archives']))
				{
					// Forum parent
					$parent = ListerParents($cat);
					if (count($parent) > 2)
					{
						$parent = array_pop($parent);
						$ListerCategories[$nbIndex]['parent'] = $parent;
					}
				}
				
				$nbIndex++;
			}
		}

		//Inclusion de la vue
		if (!empty($_GET['trash']))
		{
			fil_ariane('Accueil de la corbeille');
		}
		elseif (!empty($_GET['favori']))
		{
			fil_ariane('Liste des sujets en favoris');
		}
		elseif(!empty($_GET['archives']))
		{
			fil_ariane('Accueil des archives');
		}
		else
		{
			fil_ariane('Accueil des forums');
		}
		
		$this->get('zco_core.resource_manager')->requireResources(array(
			'@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
		));
		
		$response = render_to_response(array(
			'ListerCategories' => $ListerCategories,
			'Lu' => $Lu,
			'ListerVisiteurs' => ListerVisiteursForumEntier(),
		));
		$response->headers->set('Pragma', 'no-cache');
		$response->headers->set('cache-Control', 'no-cache');
		
		return $response;
	}
}
