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

namespace Zco\Bundle\CoreBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\EventListener\SessionListener as BaseSessionListener;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Étend le listener de base pour personnaliser le démarrage de la session 
 * en y insérant nos variables indispensables.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class SessionListener extends BaseSessionListener
{
	protected $container;

    public function __construct(ContainerInterface $container, $autoStart = false)
	{
		$this->container = $container;
		parent::__construct($container, $autoStart);
	}

    /**
     * {@inheritdoc}
     */
	public function onKernelRequest(GetResponseEvent $event)
	{
		parent::onKernelRequest($event);
		
		
		
		//$this->container->get('zco_user.user')->attemptSessionLogin($event->getRequest());
	}
}