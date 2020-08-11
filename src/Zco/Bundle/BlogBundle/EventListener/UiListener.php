<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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

use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Observateur modifiant l'interface proposée à l'utilisateur pour y intégrer 
 * le module de blog.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UiListener extends ContainerAware implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'zco_core.filter_menu.speedbarre' => 'onFilterSpeedbarre',
			'zco_core.filter_menu.speedbarre_right' => 'onFilterSpeedbarreRight',
			'zco_core.filter_menu.left_menu' => 'onFilterLeftMenu',
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
		
		$tasks = $this->container->get('zco_admin.manager')->get('commentairesBlog');
		$tab->addChild('Voir les nouveaux commentaires', array(
			'label' => 'Il y a '.$tasks.' nouveau'.pluriel($tasks, 'x').' commentaire'.pluriel($tasks),
			'uri' => '/blog/nouveaux-commentaires.html',
			'count' => $tasks,
			'separator' => true,
		))->secure('blog_supprimer_commentaires');
		
		$tab->addChild('Voir tous les commentaires', array(
			'uri' => '/blog/tous-les-commentaires.html',
		))->secure('blog_voir_tous_les_commentaires');
	}
	
	/**
	 * Ajoute un lien vers la page des billets d'un utilisateur dans le menu de 
	 * gauche (ancien design uniquement).
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterLeftMenu(FilterMenuEvent $event)
	{
		if (!verifier('blog_ajouter') || $event->getTemplate() !== 'legacy')
		{
			return;
		}
		
		$event->getRoot()->getChild('Mon compte')->addChild('Mes billets', array(
			'uri'	=> '/blog/mes-billets.html',
			'weight' => 20,
			'linkAttributes' => array(
				'rel'   => 'Proposez votre billet pour qu\'il apparaisse sur la page d\'accueil du site.', 
				'title' => 'Mes billets',
			),
		));
	}

	/**
	 * Ajoute un lien vers l'accueil du module de blog dans la barre de 
	 * navigation rapide.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterSpeedbarre(FilterMenuEvent $event)
	{
		$event
			->getRoot()
			->addChild('Blog', array('uri'   => '/blog/', 'weight' => 10))
			->setCurrent($event->getRequest()->attributes->get('_module') === 'blog');
		
	}
	
	/**
	 * Ajoute un lien vers la page des billets d'un utilisateur dans le menu 
	 * déroulant « Mon compte » de la barre de navigation rapide (nouveau 
	 * design uniquement).
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterSpeedbarreRight(FilterMenuEvent $event)
	{
		if ($event->getTemplate() === 'bootstrap' && verifier('connecte'))
		{
			$event->getRoot()->getChild('Mon compte')->addChild('Mes billets', array(
				'uri'    => '/blog/mes-billets.html',
				'weight' => 30,
			));
		}
	}
}