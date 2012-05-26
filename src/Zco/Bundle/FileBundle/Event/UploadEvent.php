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

namespace Zco\Bundle\FileBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Événement transmettant les informations concernant un fichier envoyé à 
 * travers ce module.
 * 
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UploadEvent extends Event
{
	private $file;
	
	/**
	 * Constructeur.
	 *
	 * @param \File $file Le fichier envoyé
	 */
	public function __construct(\File $file)
	{
		$this->file = $file;
	}
	
	/**
	 * Renvoie le fichier envoyé.
	 *
	 * @return \File
	 */
	public function getFile()
	{
		return $this->file;
	}
}