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

namespace Zco\Bundle\AdminBundle\Controller;

use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\AdminBundle\Menu\Renderer\AdminRenderer;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Bundle\CoreBundle\Menu\MenuFactory;
use Zco\Bundle\CoreBundle\Menu\MenuItem;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant l'accueil de l'administration pour les membres de l'équipe.
 * Gère la division de l'espace en onglets, avec dans chaque onglet des blocs, en
 * fonction des droits des utilisateurs.
 *
 * @author Zopieux, DJ Fox, vincent1870, mwsaz
 */
class IndexController extends Controller
{
	public function defaultAction()
	{
		if (!verifier('admin'))
		{
			throw new AccessDeniedHttpException();
		}
		
		\Page::$titre = 'Accueil de l\'administration';
		fil_ariane('Administration');
		
		$factory = new MenuFactory();
		$menu = $factory->createItem('Administration');
		$menu->addChild('Contenu');
		$menu->addChild('zCorrection');
		$menu->addChild('Communauté');
		$menu->addChild('Gestion technique');
		$menu->addChild('Informations');
		$menu->addChild('Gestion financière');
		
		$event = new FilterMenuEvent($this->get('request'), $menu);
		$this->get('event_dispatcher')->dispatch(AdminEvents::MENU, $event);
		$renderer = new AdminRenderer();
		
		return render_to_response(array('admin' => $renderer->render($menu)));
	}
}
