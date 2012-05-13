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

namespace Zco\Bundle\PubliciteBundle\EventListener;

use Zco\Component\Templating\TemplatingEvents;
use Zco\Component\Templating\Event\FilterResourcesEvent;
use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventListener extends ContainerAware implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'zco_core.filter_menu.left_menu' => 'onFilterLeftMenu',
			'zco_core.filter_menu.footer3' => 'onFilterFooter3',
			TemplatingEvents::FILTER_RESOURCES => 'onTemplatingFilterResources',
			AdminEvents::MENU => 'onFilterAdmin',
		);
	}
	
	/**
	 * Initialise le comportements de base commun à toutes les pages du site
	 * destiné à compter les clics sur les liens publicitaires.
	 *
	 * @param FilterResourcesEvent $event
	 */
	public function onTemplatingFilterResources(FilterResourcesEvent $event)
	{
	    $event->initBehavior('ads-track-clicks', array('category_id' => GetIDCategorieCourante()));
	}
	
	/**
	 * Ajoute les partenaires dans le menu de gauche.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterLeftMenu(FilterMenuEvent $event)
	{
		if ($this->container->getParameter('kernel.environment') === 'prod' && is_file(BASEPATH.'/vendor/linklift/liens.php'))
		{
			ob_start();
			include(BASEPATH.'/vendor/linklift/liens.php');
			$event->getRoot()->setHtml(ob_get_clean(), 'prefix');
		}
		
		$block = $event->getRoot()->getChild('Partenaires');
		$this->generateHtml('menu', $block);
		
		//Ajoute un lien vers le formulaire de contact si le bundle est activé.
		if (array_key_exists('ZcoAboutBundle', $this->container->getParameter('kernel.bundles')))
		{
			$class = $block->getAttribute('class', '');
			$block->setAttribute('class', trim($class.' bloc_partenaires'));
			$block->addChild('Votre site ici ?', array(
				'uri' => $this->container->get('router')->generate('zco_about_contact', array('objet' => 'Partenariat')),
				'linkAttributes' => array(
					'title' => 'Devenez partenaire',
					'rel'   => 'Pour devenir partenaire du site zCorrecteurs.fr, envoyez-nous un courriel via le formulaire de contact.',
				),
			));
		}
	}
	
	/**
	 * Ajoute les partenaires dans le pied de page.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterFooter3(FilterMenuEvent $event)
	{
		$this->generateHtml('pied', $event->getRoot());
	}
	
	/**
	 * Ajoute les liens d'administration.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterAdmin(FilterMenuEvent $event)
	{
		$tab = $event
		    ->getRoot()
		    ->getChild('Gestion financière')
		    ->getChild('Publicité');
		
		$tab->addChild('Ajouter une publicité', array(
			'uri' => '/publicite/ajouter.html',
		))->secure('publicite_changer_etat');
		
		$tab->addChild('Voir les campagnes actives', array(
			'uri' => '/publicite/index.html?all=1&etat[]=en_cours&etat[]=pause',
		))->secure('publicite_voir');
		
		$tab->addChild('Voir les campagnes inactives', array(
			'uri' => '/publicite/index.html?all=1&etat[]=termine&etat[]=supprime',
		))->secure('publicite_voir');
	}
	
	private function generateHtml($section, ItemInterface $menu)
	{
		$partenaires = \Doctrine_Core::getTable('Publicite')->getFor($section);
		if ($partenaires !== false)
		{
			foreach ($partenaires as $p)
			{
				if ($p['contenu_js'] == true)
				{
					$options = array('html' => $p['contenu']);
				}
				else
				{
					$options = array(
						'uri'   => $p['url_cible'],
						'label' => $p['titre'],
						'linkAttributes' => array(
							'id'    => 'pub-'.$p['id'],
							'title' => $p['titre'],
							'rel'   => $p['contenu'],
						),
					);
				}
				
				$menu->addChild('Partenaire '.$p['id'], $options);
			}
		}
	}
}