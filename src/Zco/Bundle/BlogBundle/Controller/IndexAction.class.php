<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Contrôleur gérant l'accueil du blog.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class IndexAction extends BlogActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, false, false, 1);

		//Si on veut une redirection
		if(isset($_POST['saut_rapide']) && isset($_POST['cat']) && is_numeric($_POST['cat']))
		{
			if($_POST['cat'] == 0)
			{
				return new Symfony\Component\HttpFoundation\RedirectResponse('index.html', 301);
			}
			else
			{
				return new Symfony\Component\HttpFoundation\RedirectResponse('categorie-'.$_POST['cat'].'.html', 301);
			}
		}

		$NombreDeBillet = CompterListerBilletsEnLigne();
		$nbBilletsParPage = 15;
		$NombreDePage = ceil($NombreDeBillet / $nbBilletsParPage);
		$page = is_numeric($_GET['p']) ? $_GET['p'] : 1;
		if ($page > 1) Page::$titre .= ' - Page '.$page;

		list($ListerBillets, $BilletsAuteurs) = ListerBillets(array(
			'lecteurs' => false,
			'etat' => BLOG_VALIDE,
			'futur' => false,
		), $page);
		$Categories = ListerEnfants(InfosCategorie(GetIDCategorieCourante()));
		$ListePage = liste_pages($page, $NombreDePage, $NombreDeBillet, $nbBilletsParPage, 'index-p%s.html');

		//Inclusion de la vue
		fil_ariane('Liste des derniers billets');
		
		return render_to_response(array(
			'Categories' => $Categories,
			'NombreDeBillet' => $NombreDeBillet,
			'ListerBillets' => $ListerBillets,
			'BilletsAuteurs' => $BilletsAuteurs,
			'ListePage' => $ListePage,
		));
	}
}
