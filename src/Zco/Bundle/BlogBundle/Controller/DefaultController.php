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

namespace Zco\Bundle\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Zco\Bundle\BlogBundle\Domain\BlogDAO;
use Zco\Bundle\ContentBundle\Domain\CategoryDAO;

/**
 * Contrôleur gérant l'accueil du blog.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $page = (int) $request->get('page', 1);
        //Si on veut une redirection
        if (isset($_POST['saut_rapide']) && isset($_POST['cat']) && is_numeric($_POST['cat'])) {
            if ($_POST['cat'] == 0) {
                return new RedirectResponse($this->generateUrl('zco_blog_index'), 301);
            } else {
                return new RedirectResponse('categorie-' . $_POST['cat'] . '.html', 301);
            }
        }

        $NombreDeBillet = BlogDAO::CompterListerBilletsEnLigne();
        $nbBilletsParPage = 15;
        $NombreDePage = ceil($NombreDeBillet / $nbBilletsParPage);
        if ($page > 1) {
            \Page::$titre .= ' - Page ' . $page;
        }

        list($ListerBillets, $BilletsAuteurs) = BlogDAO::ListerBillets(array(
            'lecteurs' => false,
            'etat' => BLOG_VALIDE,
            'futur' => false,
        ), $page);
        $Categories = CategoryDAO::ListerEnfants(CategoryDAO::InfosCategorie(CategoryDAO::GetIDCategorieCourante()));
        $ListePage = liste_pages($page, $NombreDePage, $this->generateUrl('zco_blog_index') . '?page=%s');

        fil_ariane('Liste des derniers billets');

        return $this->render('ZcoBlogBundle::index.html.php', array(
            'Categories' => $Categories,
            'NombreDeBillet' => $NombreDeBillet,
            'ListerBillets' => $ListerBillets,
            'BilletsAuteurs' => $BilletsAuteurs,
            'ListePage' => $ListePage,
        ));
    }
}
