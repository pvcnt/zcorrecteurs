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

namespace Zco\Bundle\QuizBundle\EventListener;

use Zco\Bundle\InformationsBundle\Event\FilterSitemapEvent;
use Zco\Bundle\InformationsBundle\InformationsEvents;
use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Observateur principal pour le module de quiz.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EventListener implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'zco_core.filter_menu.speedbarre' => 'onFilterSpeedbarre',
			AdminEvents::MENU => 'onFilterAdmin',
			InformationsEvents::SITEMAP => 'onFilterSitemap',
		);
	}
	
	/**
	 * Ajoute le lien vers le module dans la barre de navigation raide.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterSpeedbarre(FilterMenuEvent $event)
	{
		$event
			->getRoot()
			->addChild('Quiz', array('uri'   => '/quiz/', 'weight' => 30))
		 	->setCurrent($event->getRequest()->attributes->get('_module') === 'quiz');
	}
	
	/**
	 * Ajoute les liens vers les pages d'administration.
	 *
	 * @param FilterMenuEvent $event
	 */
	public function onFilterAdmin(FilterMenuEvent $event)
	{
	    $tab = $event
		    ->getRoot()
		    ->getChild('Contenu')
		    ->getChild('Quiz');
		    
		$tab->addChild('Ajouter un quiz', array(
			'credentials' => 'quiz_ajouter', 
			'uri' => '/quiz/ajouter-quiz.html'
		));
	
		$tab->addChild('Gérer les quiz', array(
			'credentials' => 
			  array('or', 'quiz_ajouter', 'quiz_editer', 'quiz_editer_siens', 'quiz_supprimer',
					'quiz_supprimer_siens', 'quiz_ajouter_questions', 'quiz_ajouter_questions_siens',
					'quiz_editer_ses_questions', 'quiz_editer_questions', 'quiz_supprimer_questions', 'quiz_supprimer_ses_questions'),
			'uri' => '/quiz/gestion.html',
		));
		
	    $tab = $event
	        ->getRoot()
	        ->getChild('Informations')
	        ->getChild('Statistiques générales');
	    
		$tab->addChild('Statistiques d\'utilisation du quiz', array(
			'credentials' => 'quiz_stats_generales', 
			'uri' => '/quiz/statistiques.html',
			'weight' => 70,
		));
	
		$tab->addChild('Statistiques de popularité des quiz', array(
			'credentials' => 'quiz_stats_generales', 
			'uri' => '/quiz/statistiques-popularite.html',
			'weight' => 80,
		));
	}
	
	/**
     * Met à jour le sitemap.
     *
     * @param FilterSitemapEvent $event
     */
	public function onFilterSitemap(FilterSitemapEvent $event)
	{
		$event->addLink(URL_SITE.'/quiz/', array(
			'changefreq' => 'weekly',
			'priority'	 => '0.6',
		));
		foreach (\Doctrine_Core::getTable('Quiz')->getAllId() as $quiz)
		{
			$event->addLink(URL_SITE.'/quiz/quiz-'.$quiz['id'].'-'.rewrite($quiz['nom']).'.html', array(
				'changefreq' => 'monthly',
				'priority'	 => '0.5',
			));
		}
	}
}