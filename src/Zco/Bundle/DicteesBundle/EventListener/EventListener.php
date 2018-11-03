<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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

namespace Zco\Bundle\DicteesBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zco\Bundle\DicteesBundle\Domain\Dictation;
use Zco\Component\Templating\Event\FilterVariablesEvent;
use Zco\Component\Templating\TemplatingEvents;

class EventListener implements EventSubscriberInterface
{
    use ContainerAwareTrait;

    static public function getSubscribedEvents()
    {
        return array(
            TemplatingEvents::FILTER_VARIABLES => 'onTemplatingFilterVariables',
        );
    }

    public function onTemplatingFilterVariables(FilterVariablesEvent $event)
    {
        $request = \Container::request();
        if (!$request->attributes->has('_module') || $request->attributes->get('_module') !== 'dictees') {
            return;
        }

        $event->set('DicteeDifficultes', Dictation::LEVELS);
        $event->set('DicteeEtats', Dictation::STATUSES);
        $event->set('DicteeCouleurs', Dictation::COLORS);
    }
}