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

namespace Zco\Component\HttpKernel;

use Zco\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * Étend les fonctionnalités du kernel de sorte à assurer la compatibilité 
 * descendante avec l'ancien code.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
abstract class Kernel extends BaseKernel
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct($environment, $debug = false)
	{
		parent::__construct($environment, $debug);
				
		date_default_timezone_set('Europe/Paris');
		setlocale(LC_ALL, 'fr_FR.UTF-8');
		mb_internal_encoding('UTF-8');
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function boot()
	{
		parent::boot();
		
		\Config::load('constants');
		
		foreach ($this->bundles as $name => $bundle)
		{
			if ($bundle instanceof AbstractBundle)
			{
				$bundle->preload();
			}
		}
	}
		
	/**
	 * {@inheritdoc}
	 */
	protected function initializeContainer()
	{
		parent::initializeContainer();
		
		//Injecte le DIC dans le singleton \Container pour assurer la 
		//compatibilité descendante.
		\Container::setInstance($this->container);
	}
}
