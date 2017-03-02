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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\Cache\CacheInterface;
use Zco\Bundle\CoreBundle\CoreEvents;
use Zco\Bundle\CoreBundle\Event\CronEvent;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Bundle\PagesBundle\Event\FilterSitemapEvent;
use Zco\Bundle\PagesBundle\PagesEvents;
use Zco\Bundle\QuizBundle\Entity\QuizManager;

/**
 * Observateur principal pour le module de quiz.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EventListener implements EventSubscriberInterface
{
    private $urlGenerator;
    private $quizManager;
    private $cache;

    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     * @param QuizManager $quizManager
     * @param CacheInterface $cache
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, QuizManager $quizManager, CacheInterface $cache)
    {
        $this->urlGenerator = $urlGenerator;
        $this->quizManager = $quizManager;
        $this->cache = $cache;
    }


    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            'zco_core.filter_menu.speedbarre' => 'onFilterSpeedbarre',
            AdminEvents::MENU => 'onFilterAdmin',
            PagesEvents::SITEMAP => 'onFilterSitemap',
            CoreEvents::DAILY_CRON => 'onDailyCron',
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
            ->addChild('Quiz', array('uri' => $this->urlGenerator->generate('zco_quiz_index'), 'weight' => 30))
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
            'uri' => $this->urlGenerator->generate('zco_quiz_newQuiz'),
        ));

        $tab->addChild('Gérer les quiz', array(
            'credentials' =>
                array('or', 'quiz_ajouter', 'quiz_editer', 'quiz_editer_siens', 'quiz_supprimer',
                    'quiz_supprimer_siens', 'quiz_ajouter_questions', 'quiz_ajouter_questions_siens',
                    'quiz_editer_ses_questions', 'quiz_editer_questions', 'quiz_supprimer_questions', 'quiz_supprimer_ses_questions'),
            'uri' => $this->urlGenerator->generate('zco_quiz_admin'),
        ));

        $tab = $event
            ->getRoot()
            ->getChild('Informations')
            ->getChild('Statistiques générales');

        $tab->addChild('Statistiques d\'utilisation du quiz', array(
            'credentials' => 'quiz_stats_generales',
            'uri' => $this->urlGenerator->generate('zco_quiz_stats'),
            'weight' => 70,
        ));

        $tab->addChild('Statistiques de popularité des quiz', array(
            'credentials' => 'quiz_stats_generales',
            'uri' => $this->urlGenerator->generate('zco_quiz_popularity'),
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
        $event->addLink($this->urlGenerator->generate('zco_quiz_index', [], UrlGeneratorInterface::ABSOLUTE_URL), array(
            'changefreq' => 'weekly',
            'priority' => '0.6',
        ));
        $list = $this->quizManager->lister();
        foreach ($list as $quiz) {
            $event->addLink($this->urlGenerator->generate('zco_quiz_show', ['id' => $quiz['id'], 'slug' => rewrite($quiz['nom'])], UrlGeneratorInterface::ABSOLUTE_URL), array(
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ));
        }
    }

    /**
     * Actions à exécuter chaque jour.
     *
     * @param CronEvent $event
     */
    public function onDailyCron(CronEvent $event)
    {
        //Mise en cache des quiz les plus fréquentés
        $this->cache->delete('quiz_liste_frequentes');
    }
}