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

namespace Zco\Bundle\BlogBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;

/**
 * Observateur modifiant l'interface proposée à l'utilisateur pour y intégrer 
 * le module de blog.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UiListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			AdminEvents::MENU => 'onFilterAdmin',
		);
	}
	
	/**
	 * Ajoute les liens vers les pages d'administration du module de blog.
	 *
	 * @param FilterMenuEvent $event
	 */	
	public function onFilterAdmin(FilterMenuEvent $event)
	{
		$tab = $event
			->getRoot()
			->getChild('Contenu')
			->getChild('Blog');
		
		$tasks = $this->container->get('zco_admin.manager')->get('blog');
		$tab->addChild('Voir les billets proposés', array(
			'label' => 'Il y a '.$tasks.' billet'.pluriel($tasks).' proposé'.pluriel($tasks),
			'uri' => '/blog/propositions.html',
			'count' => $tasks,
		))->secure('blog_voir_billets_proposes');
		
		$tab->addChild('Voir les billets en cours de rédaction', array(
			'uri' => '/blog/brouillons.html',
		))->secure('blog_voir_billets_redaction');
		
		$tab->addChild('Voir les billets refusés', array(
			'uri' => '/blog/refus.html',
		))->secure('blog_voir_refus');
		
		$tab->addChild('Voir les billets en ligne', array(
			'uri' => '/blog/gestion.html'
		))->secure(array('or', 'blog_supprimer', 'blog_editer_valide'));
	}
}