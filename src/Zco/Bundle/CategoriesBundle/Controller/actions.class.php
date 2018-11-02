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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\CategoriesBundle\Domain\CategoryDAO;

/**
 * Actions pour tout ce qui concerne la gestion des catégories du site.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class CategoriesActions extends Controller
{
	/**
	 * Affichage de la liste des catégories.
	 */
	public function executeIndex()
	{
	    if (!verifier('cats_editer')) {
	        throw new AccessDeniedHttpException();
        }
		//Si on veut descendre une catégorie
		if(!empty($_GET['descendre']) && is_numeric($_GET['descendre']))
		{
			$InfosCategorie = CategoryDAO::InfosCategorie($_GET['descendre']);
			if(!empty($InfosCategorie))
			{
				if(CategoryDAO::DescendreCategorie($InfosCategorie))
					return redirect('La catégorie a bien été descendue.', '/categories/');
				else
					return redirect('Impossible de descendre cette catégorie car elle est déjà en bas.', '/categories/', MSG_ERROR);
			}
			else
				throw new NotFoundHttpException();
		}

		//Si on veut monter une catégorie
		if(!empty($_GET['monter']) && is_numeric($_GET['monter']))
		{
			$InfosCategorie = CategoryDAO::InfosCategorie($_GET['monter']);
			if(!empty($InfosCategorie))
			{
				if(CategoryDAO::MonterCategorie($InfosCategorie))
					return redirect('La catégorie a bien été montée.', '/categories/');
				else
					return redirect(
					    'Impossible de monter cette catégorie car elle est déjà en haut.',
                        '/categories/',
                        MSG_ERROR
                    );
			}
			else
				throw new NotFoundHttpException();
		}

		fil_ariane('Liste des catégories');
		$this->get('zco_core.resource_manager')->requireResource('@ZcoCoreBundle/Resources/public/js/messages.js');

		return render_to_response(array('categories' => CategoryDAO::ListerCategories()));
	}

	/**
	 * Ajout d'une nouvelle catégorie.
	 */
	public function executeAjouter()
	{
        if (!verifier('cats_editer')) {
            throw new AccessDeniedHttpException();
        }
		Page::$titre = 'Ajouter une catégorie';

		//Si on veut ajouter une catégorie
		if(!empty($_POST['nom']))
		{
            CategoryDAO::AjouterCategorie();
			return redirect('La catégorie a bien été ajoutée.', 'index.html');
		}

		fil_ariane('Ajouter une catégorie');

		return render_to_response(array('categories' => CategoryDAO::ListerCategories()));
	}

	/**
	 * Modification d'une catégorie.
	 */
	public function executeEditer()
	{
        if (!verifier('cats_editer')) {
            throw new AccessDeniedHttpException();
        }
		Page::$titre = 'Modifier une catégorie';

		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosCategorie = CategoryDAO::InfosCategorie($_GET['id']);
			if(empty($InfosCategorie))
				throw new NotFoundHttpException();

			//Si on veut éditer la catégorie
			if(!empty($_POST['nom']))
			{
                CategoryDAO::EditerCategorie($_GET['id']);
				return redirect('La catégorie a bien été modifiée.', 'index.html');
			}

			fil_ariane('Modifier une catégorie');
			$ListerParents = CategoryDAO::ListerParents($InfosCategorie);
			if(empty($ListerParents))
				$ListerParents[0]['cat_id'] = 0;
				
			// Récupération des informations du forum et vérification si c'est bien un forum
			$InfoForum = CategoryDAO::InfosCategorie(CategoryDAO::GetIDCategorie('forum'));
			$forum = false;
			
			if ($InfosCategorie['cat_droite'] <= $InfoForum['cat_droite'] && $InfosCategorie['cat_gauche'] >= $InfoForum['cat_gauche'] ) {
				$forum = true;
			}
		}
		else
            throw new NotFoundHttpException();

		return render_to_response(array(
			'InfosCategorie' => $InfosCategorie,
			'ListerParents' => $ListerParents,
			'isForum' => $forum
		));
	}

	/**
	 * Suppression d'une catégorie.
	 */
	public function executeSupprimer()
	{
        if (!verifier('cats_editer')) {
            throw new AccessDeniedHttpException();
        }
		Page::$titre = 'Supprimer une catégorie';

		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosCategorie = CategoryDAO::InfosCategorie($_GET['id']);
			if(empty($InfosCategorie))
                throw new NotFoundHttpException();

			if($InfosCategorie['cat_droite'] - $InfosCategorie['cat_gauche'] > 1)
				return redirect(
				    'Vous ne pouvez pas supprimer cette catégorie car elle a des sous-catégories.',
                    'index.html',
                    MSG_ERROR
                );

			//Si on veut supprimer la catégorie
			if(isset($_POST['confirmer']))
			{
                CategoryDAO::SupprimerCategorie($_GET['id']);
				return redirect('La catégorie a bien été supprimée.', 'index.html');
			}
			//Si on annule
			elseif(isset($_POST['annuler']))
			{
				return new RedirectResponse('index.html');
			}

			fil_ariane('Supprimer une catégorie');
			return render_to_response(array('InfosCategorie' => $InfosCategorie));
		}
		else
		{
            throw new NotFoundHttpException();
		}
	}
}
