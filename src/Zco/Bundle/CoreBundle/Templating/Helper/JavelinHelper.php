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

namespace Zco\Bundle\CoreBundle\Templating\Helper;

use Zco\Bundle\CoreBundle\Javelin\Javelin;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class JavelinHelper extends Helper
{
    private $javelin;
    
    public function __construct(Javelin $javelin)
    {
        $this->javelin = $javelin;
    }
	public function initBehavior($behavior, array $config = array())
	{
	    $this->javelin->initBehavior($behavior, $config);
	}
	
	public function onload($call)
	{
	    $this->javelin->onload($call);
	}
	
	public function generateUniqueNodeId()
	{
	    return $this->javelin->generateUniqueNodeId();
	}
	
	public function addMetadata($metadata)
	{
	    return $this->javelin->addMetadata($metadata);
	}
	
	public function renderHTMLFooter()
	{
	    return $this->javelin->renderHTMLFooter();
	}
	
	public function getName()
	{
		return 'javelin';
	}
}
