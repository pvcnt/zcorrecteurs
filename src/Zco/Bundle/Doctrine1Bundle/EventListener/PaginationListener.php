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

namespace Zco\Bundle\Doctrine1Bundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\Event\ItemsEvent;

/**
 * Ajoute le support des requêtes Doctrine1 sur le paginateur utilisé.
 * 
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class PaginationListener implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return array(
			'knp_pager.items' => array('items', 128 /* cas le plus fréquent, priorité élevée */)
		);
	}
	
	/**
	 * Récupère les éléments pour la pagination depuis une requête Doctrine.
	 *
	 * @param ItemsEvent $event
	 */
	public function items(ItemsEvent $event)
	{
		if ($event->target instanceof \Doctrine_Query)
		{
			$countQuery = clone $event->target;
			$event->count = (int) $countQuery->count();
			
			$sliceQuery = clone $event->target;
	        $sliceQuery->offset($event->getOffset());

	        if ($event->getLimit())
	        {
	            $sliceQuery->limit($event->getLimit());
	        }

			$event->items = $sliceQuery->execute()->getData();
			$event->stopPropagation();
		}
	}
}