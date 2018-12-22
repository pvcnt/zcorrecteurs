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

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zco\Bundle\BlogBundle\Domain\BlogDAO;
use Zco\Bundle\ContentBundle\Domain\CategoryDAO;

/**
 * Contrôleur gérant l'affichage des billets d'une catégorie.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class CategorieAction extends BlogActions
{
	public function execute()
	{
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosCategorie = CategoryDAO::InfosCategorie($_GET['id']);
			if(empty($InfosCategorie))
                throw new NotFoundHttpException();

			zCorrecteurs::VerifierFormatageUrl($InfosCategorie['cat_nom'], true, false, 1);
			$NombreDeBillet = BlogDAO::CompterListerBilletsEnLigne($_GET['id']);
			$nbBilletsParPage = 15;
			$NombreDePage = ceil($NombreDeBillet / $nbBilletsParPage);
			$page = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
			list($ListerBillets, $BilletsAuteurs) = BlogDAO::ListerBillets(array(
				'id_categorie' => $_GET['id'],
				'lecteurs' => false,
				'etat' => BLOG_VALIDE,
				'futur' => false,
			), $page);
			$ListePage = liste_pages($page, $NombreDePage, $NombreDeBillet, $nbBilletsParPage, '/blog/categorie-'.$_GET['id'].'-p%s-'.rewrite($InfosCategorie['cat_nom']).'.html');
			$ListerParents = CategoryDAO::ListerParents($InfosCategorie);
			$Categories = CategoryDAO::ListerEnfants($ListerParents[1]);

			//Inclusion de la vue
			fil_ariane($_GET['id'], 'Liste des billets de la catégorie');
			Page::$robots = 'noindex,follow';

			return render_to_response('ZcoBlogBundle::categorie.html.php', array(
				'ListerBillets' => $ListerBillets,
				'BilletsAuteurs' => $BilletsAuteurs,
				'ListePage' => $ListePage,
				'ListerParents' => $ListerParents,
				'Categories' => $Categories,
				'InfosCategorie' => $InfosCategorie,
				'NombreDeBillet' => $NombreDeBillet,
			));
		}
		else
		{
            throw new NotFoundHttpException();
		}
	}
}
