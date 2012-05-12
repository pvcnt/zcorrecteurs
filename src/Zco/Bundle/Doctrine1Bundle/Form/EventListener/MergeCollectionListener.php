<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Zco\Bundle\Doctrine1Bundle\Form\EventListener;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\FilterDataEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Merge changes from the request to a \Doctrine_Collection instance.
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class MergeCollectionListener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(FormEvents::BIND_NORM_DATA => 'onBindNormData');
	}

	public function onBindNormData(FilterDataEvent $event)
	{
		$collection = $event->getForm()->getData();
		$data = $event->getData();

		if (!$collection)
		{
			$collection = $data;
		}
		elseif (count($data) === 0)
		{
			$collection->clear();
		}
		else
		{
			// merge $data into $collection
			$collectionData = $collection->getData();
			$eventData = $data->getData();
			foreach ($collectionData as $i => $entity)
			{
				if (($key = array_search($entity, $eventData)) === false)
				{
					unset($collectionData[$i]);
				}
				else
				{
					unset($eventData[$key];
				}
			}

			foreach ($eventData as $entity)
			{
				$collectionData[] = $entity;
			}
			
			$collection->setData($collectionData);
		}

		$event->setData($collection);
	}
}
