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

namespace Zco\Component\HttpKernel\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;

/**
 * Étend les fonctionnalités des bundles de base. Les nouvelles méthodes sont 
 * déclenchées par l'extension du kernel.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
abstract class AbstractBundle extends BaseBundle
{
	/**
	 * Méthode appelée sur tous les bundles après qu'ils aient tous booté.
	 */
	public function preload()
	{
	}
	
	/**
	 * Méthode appelée sur le bundle élu par le système de routing.
	 */
	public function load()
	{
	}
}