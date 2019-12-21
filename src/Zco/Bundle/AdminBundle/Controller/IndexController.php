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

namespace Zco\Bundle\AdminBundle\Controller;

use Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\AdminBundle\Menu\Renderer\AdminRenderer;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Bundle\CoreBundle\Menu\MenuFactory;

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
class IndexController extends Controller
{
    public function defaultAction()
    {
        if (!verifier('admin')) {
            throw new AccessDeniedHttpException();
        }

        $this->get('zco_admin.manager')->refresh();

        Page::$titre = 'Accueil de l\'administration';
        fil_ariane('Administration');

        $factory = new MenuFactory();
        $menu    = $factory->createItem('Administration');
        $menu->addChild('Contenu');
        $menu->addChild('Communauté');
        $menu->addChild('Gestion technique');
        $menu->addChild('Informations');
        $menu->addChild('Gestion financière');

        $event    = new FilterMenuEvent($this->get('request'), $menu);
        $this->get('event_dispatcher')->dispatch(AdminEvents::MENU, $event);
        $renderer = new AdminRenderer();

        return render_to_response(array('admin' => $renderer->render($menu)));
    }
}
