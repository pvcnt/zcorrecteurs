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

namespace Zco\Bundle\StatsBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Zco\Bundle\AdminBundle\AdminEvents;
use Zco\Bundle\CoreBundle\CoreEvents;
use Zco\Bundle\CoreBundle\Event\CronEvent;
use Zco\Bundle\CoreBundle\Menu\Event\FilterMenuEvent;
use Zco\Bundle\StatsBundle\Service\AlexaStatsService;

/**
 * Observateur principal pour le module de statistiques.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EventListener implements EventSubscriberInterface
{
    private $urlGenerator;
    private $alexaStats;

    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     * @param AlexaStatsService $alexaStats
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, AlexaStatsService $alexaStats)
    {
        $this->urlGenerator = $urlGenerator;
        $this->alexaStats = $alexaStats;
    }

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            AdminEvents::MENU => 'onFilterAdmin',
            CoreEvents::DAILY_CRON => 'onDailyCron',
        );
    }

    /**
     * Ajoute des liens sur le panneau d'administration.
     *
     * @param FilterMenuEvent $event
     */
    public function onFilterAdmin(FilterMenuEvent $event)
    {
        $tab = $event
            ->getRoot()
            ->getChild('Informations')
            ->getChild('Statistiques générales');

        $tab->addChild('Statistiques générales (GA)', array(
            'credentials' => 'voir_stats_generales',
            'uri' => 'https://www.google.com/analytics/reporting/dashboard?id=6978501&scid=1725896',
        ));

        $tab->addChild('Statistiques Alexa (classement du site)', array(
            'credentials' => 'voir_stats_generales',
            'uri' => $this->urlGenerator->generate('zco_stats_alexa'),
        ));

        $tab->addChild('Statistiques d\'inscription', array(
            'credentials' => 'voir_stats_generales',
            'uri' => $this->urlGenerator->generate('zco_stats_registration'),
        ));

        $tab->addChild('Statistiques de géolocalisation', array(
            'credentials' => 'voir_stats_generales',
            'uri' => $this->urlGenerator->generate('zco_stats_location'),
        ));

        $tab->addChild('Âge des membres', array(
            'credentials' => 'voir_stats_generales',
            'uri' => $this->urlGenerator->generate('zco_stats_ages'),
        ));
    }

    public function onDailyCron(CronEvent $event)
    {
        $this->alexaStats->fetchAndSave();
    }
}