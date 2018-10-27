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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Bundle\CoreBundle\CoreEvents;
use Zco\Bundle\CoreBundle\Event\CronEvent;
use Zco\Bundle\StatsBundle\Service\AlexaStatsService;

/**
 * Observateur principal pour le module de statistiques.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EventListener implements EventSubscriberInterface
{
    private $alexaStats;

    /**
     * Constructor.
     *
     * @param AlexaStatsService $alexaStats
     */
    public function __construct(AlexaStatsService $alexaStats)
    {
        $this->alexaStats = $alexaStats;
    }

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            CoreEvents::DAILY_CRON => 'onDailyCron',
        );
    }

    public function onDailyCron(CronEvent $event)
    {
        $this->alexaStats->fetchAndSave();
    }
}