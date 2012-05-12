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

namespace Zco\Component\Templating\Event;

use Zco\Bundle\VitesseBundle\Resource\ResourceManagerInterface;
use Zco\Bundle\VitesseBundle\Javelin\Javelin;
use Symfony\Component\EventDispatcher\Event;

class FilterResourcesEvent extends Event
{
	private $manager;
	private $javelin;
	
	public function __construct(ResourceManagerInterface $manager, Javelin $javelin)
	{
		$this->manager = $manager;
		$this->javelin = $javelin;
	}
	
	public function addFeeds(array $feeds)
	{
		$this->manager->addFeeds($feeds);
	}
	
	public function addFeed($feed, array $options = array())
	{
		$this->manager->addFeed($feed, $options);
	}
	
	public function requireResources(array $symbols)
	{
		$this->manager->requireResources($symbols);
	}
	
	public function requireResource($symbol)
	{
		$this->manager->requireResource($symbol);
	}
	
	public function initBehavior($behavior, array $config = array())
	{
	    $this->javelin->initBehavior($behavior, $config);
	}
	
	public function onload($callback)
	{
		$this->javelin->onload($callback);
	}
}