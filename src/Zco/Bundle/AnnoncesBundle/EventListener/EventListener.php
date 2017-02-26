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

namespace Zco\Bundle\AnnoncesBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\CoreEvents;
use Zco\Bundle\CoreBundle\Event\CronEvent;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Component\Templating\Event\FilterResourcesEvent;
use Zco\Component\Templating\TemplatingEvents;

/**
 * Observateur principal pour le module d'annonces.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EventListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
		    AdminEvents::MENU                  => 'onFilterAdmin',
			TemplatingEvents::FILTER_RESOURCES => 'onTemplatingFilterResources',
			CoreEvents::HOURLY_CRON            => 'onHourlyCron',
		);
	}
	
	/**
	 * Ajoute le code javascript nécessaire au chargement asynchrone des bannières.
	 *
	 * @param FilterResourcesEvent $event
	 */
	public function onTemplatingFilterResources(FilterResourcesEvent $event)
	{
	    $config = array(
	        'categorie' => GetIDCategorieCourante(),
	        'page'      => $_SERVER['REQUEST_URI'],
        );
	    
	    if (!empty($_GET['_annonce']))
	    {
	        $config['annonce'] = (int) $_GET['_annonce'];
	    }
	    if (!empty($_GET['_pays']) && verifier('annonces_publier'))
	    {
	        $config['pays'] = (string) $_GET['_pays'];
	    }
	    if (isset($_GET['_groupe']) && verifier('annonces_publier'))
	    {
	        $config['groupe'] = (int) $_GET['_groupe'];
	    }
	    
	    $event->initBehavior('annonces-inject-banner', $config);
	}
	
	/**
	 * Ajoute des liens sur le panneau d'administration.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterAdmin(FilterMenuEvent $event)
	{
	    $event
	        ->getRoot()
	        ->getChild('Contenu')
	        ->getChild('Communication')
	        ->addChild('Modifier les annonces globales au site', array(
	            'uri' => '/annonces/',
	        ))
	        ->secure(array('or', 'annonces_ajouter', 'annonces_modifier', 'annonces_supprimer', 'annonces_publier'));
	}

	/**
	 * Met à jour les statistiques horaires des annonces.
	 *
	 * @param CronEvent $event
	 */
	public function onHourlyCron(CronEvent $event)
	{
		$cache = $this->container->get('zco_core.cache');
	    $pks = \Doctrine_Query::create()
        	->select('id')
        	->from('Annonce')
        	->execute(array(), \Doctrine_Core::HYDRATE_ARRAY);

        foreach ($pks as $annonce)
        {
        	$nbv = $cache->get('annonce_nbv-'.$annonce['id'], 0);
        	$event->getOutput()->writeln('Annonce n°'.$annonce['id'].' : '.$nbv.' affichage(s)');

        	if ($nbv > 0)
        	{
        		\Doctrine_Query::create()
        			->update('Annonce')
        			->set('nb_affichages', 'nb_affichages + ?', $nbv)
        			->where('id = ?', $annonce['id'])
        			->execute();
        	}
        	
        	$cache->delete('annonce_nbv-'.$annonce['id']);
        }
	}
}