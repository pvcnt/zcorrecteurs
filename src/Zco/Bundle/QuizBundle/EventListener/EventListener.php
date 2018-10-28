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

    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     * @param QuizManager $quizManager
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, QuizManager $quizManager)
    {
        $this->urlGenerator = $urlGenerator;
        $this->quizManager = $quizManager;
    }


    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            PagesEvents::SITEMAP => 'onFilterSitemap',
        );
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
}