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
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Événement permettant d'interagir sur un fichier désirant être envoyé vers le 
 * module gestionnaire de fichiers. Il est possible d'accepter/rejeter le fichier 
 * ou de modifier les options d'envoi.
 * 
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class FilterUploadEvent extends Event
{
	private $errorMessage;
	private $acceptable;
	private $file;
	private $options;
	
	/**
	 * Constructeur.
	 *
	 * @param UploadedFile $file Le fichier désirant être envoyé
	 * @param array $options Les options d'envoi
	 */
	public function __construct(UploadedFile $file, array $options = array())
	{
		$this->file    = $file;
		$this->options = $options;
	}
	
	/**
	 * Retourne le fichier désirant être envoyé.
	 *
	 * @return UploadedFile
	 */
	public function getFile()
	{
		return $this->file;
	}
	
	/**
	 * Retourne les options d'envoi.
	 *
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}
	
	/**
	 * Retourne une option d'envoi si elle existe.
	 *
	 * @param  string $name Le nom de l'option
	 * @return mixed La valeur de l'option, null si elle n'existe pas
	 */
	public function getOption($name)
	{
		return isset($this->options[$name]) ? $this->options[$name] : null;
	}
	
	/**
	 * Définit une option d'envoi.
	 *
	 * @param string $name Le nom de l'option
	 * @param mixed $value La valeur de l'option
	 */
	public function setOption($name, $value)
	{
		$this->options[$name] = $value;
	}
	
	/**
	 * Marque le fichier comme acceptable. La propagation de l'événement n'est 
	 * pas stoppée à ce stade ce qui permet à un autre observateur d'éventuellement 
	 * le rejeter après. Si vous souhaitez éviter cela appelez explicitement 
	 * stopPropagation() après cette méthode.
	 */
	public function validate()
	{
		$this->acceptable = true;
	}
	
	/**
	 * Indique que le fichier n'est pas acceptable.
	 *
	 * @param string $message Un message optionnel explicitant les raisons du rejet
	 */
	public function reject($message = '')
	{
		$this->acceptable = false;
		$this->errorMessage = $message;
	}
	
	/**
	 * Vérifie si le fichier est marqué comme acceptable.
	 *
	 * @return boolean
	 */
	public function isAcceptable()
	{
		return $this->acceptable;
	}
	
	/**
	 * Retourne le message d'erreur associé à un fichier non acceptable.
	 *
	 * @throws \LogicException Si le fichier est acceptable
	 * @return string Le message d'erreur, éventuellement vide
	 */
	public function getErrorMessage()
	{
		if ($this->isAcceptable())
		{
			throw new \LogicException('Cannot get the error message when upload is acceptable.');
		}
		
		return $this->errorMessage;
	}
}