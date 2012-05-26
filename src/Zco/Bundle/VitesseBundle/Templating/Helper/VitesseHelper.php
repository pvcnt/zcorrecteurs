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

namespace Zco\Bundle\VitesseBundle\Templating\Helper;

use Zco\Bundle\VitesseBundle\Resource\ResourceManagerInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * Ensemble de fonctions permettant de demander l'inclusion de ressources
 * à l'intérieur de la page et ensuite de les inclure effectivement.
 * 
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class VitesseHelper extends Helper
{
    private $manager;
    
	public function __construct(ResourceManagerInterface $manager)
	{
	    $this->manager = $manager;
	}
	
	public function requireResource($symbol)
	{
	    $this->manager->requireResource($symbol);
	}
	
	public function requireResources(array $symbols)
	{
	    $this->manager->requireResources($symbols);
	}
	
	public function addFeeds(array $feeds)
	{
	    return $this->manager->addFeeds($feeds);
	}
		
	public function addFeed($feed, array $options = array())
	{
	    return $this->manager->addFeed($feed, $options);
	}
	
	public function renderFeeds()
	{
	    return $this->manager->renderFeeds();
	}
		
	public function stylesheets(array $stylesheets = array())
	{
	    return $this->manager->stylesheets($stylesheets);
	}
	
	public function javascripts(array $javascripts = array())
	{
	    return $this->manager->javascripts($javascripts);
	}
		
	public function getName()
	{
		return 'vitesse';
	}
}
