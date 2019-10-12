<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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

namespace Zco\Bundle\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Zco\Bundle\ContentBundle\Menu\AdminMenuFactory;
use Zco\Bundle\ContentBundle\Menu\MenuRenderer;

/**
 * Contrôleur gérant l'accueil de l'administration pour les membres de l'équipe.
 * Gère la division de l'espace en onglets, avec dans chaque onglet des blocs, en
 * fonction des droits des utilisateurs.
 *
 * @author Zopieux
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 * @author mwsaz <mwsaz.fr>
 */
class AdminController extends Controller
{
    /**
     * @Route(name="zco_admin", path="/admin")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        if (!verifier('admin')) {
            throw new AccessDeniedHttpException();
        }

        \Page::$titre = 'Accueil de l\'administration';

        $admin = $this->get(\Zco\Bundle\ContentBundle\Admin\Admin::class);
        $admin->refreshAll();

        $menuFactory = new AdminMenuFactory($this->get('router'), $admin);
        $menu = $menuFactory->createMenu();
        $renderer = new MenuRenderer();

        return $this->render('ZcoContentBundle:Admin:index.html.php', [
            'admin' => $renderer->render($menu),
        ]);
    }
}
