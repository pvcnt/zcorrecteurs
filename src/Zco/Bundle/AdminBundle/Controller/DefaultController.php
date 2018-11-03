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

namespace Zco\Bundle\AdminBundle\Controller;

use Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zco\Bundle\AdminBundle\Menu\MenuFactory;
use Zco\Bundle\AdminBundle\Menu\MenuRenderer;

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
class DefaultController extends Controller
{
    public function indexAction()
    {
        if (!verifier('admin')) {
            throw new AccessDeniedHttpException();
        }

        Page::$titre = 'Accueil de l\'administration';

        $this->get('zco.admin')->refreshAll();

        $menuFactory = new MenuFactory($this->get('router'), $this->container->get('zco.admin'));
        $menu = $menuFactory->createMenu();
        $renderer = new MenuRenderer();

        return render_to_response('ZcoAdminBundle::index.html.php', [
            'admin' => $renderer->render($menu),
        ]);
    }
}
