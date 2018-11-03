<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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

/**
 * Classe abstraite représentant un validateur. Un validateur a pour rôle de
 * vérifier si une valeur donnée correspond à une série de règles donnée.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
abstract class Validator
{
	protected $errorMessages = array();
	protected $options = array();
	protected $requiredOptions = array();
	protected $errors = array();
	protected $value;

	/**
	 * Constructeur de la classe.
	 * @access public
	 * @param array $options		Une liste d'options.
	 * @param array $messages		Une liste de messages d'erreur.
	 */
	public function __construct($options = array(), $messages = array())
	{
		$this->errorMessages = array_merge(array('invalid' => 'Valeur invalide.', 'required' => 'Valeur requise.'), $this->errorMessages);
		$this->options = array_merge(array('required' => true, 'trim' => false, 'empty_values' => array('', null)), $this->options);

		$this->configure($options, $messages);

		//On vérifie qu'on n'envoie pas de message non supporté.
		if($diff = array_diff(array_keys($messages), array_keys($this->errorMessages)))
		{
			throw new Exception(sprintf('%s ne supporte pas les messages d\'erreur suivants : %s.', get_class($this), implode(', ', $diff)));
		}
		//On vérifie qu'on n'envoie pas d'option non supportée.
		if($diff = array_diff(array_keys($options), array_keys($this->options)))
		{
			throw new Exception(sprintf('%s ne supporte pas les options suivantes : %s.', get_class($this), implode(', ', $diff)));
		}
		//On vérifie que les options requises aient bien été envoyées.
		if($diff = array_diff($this->requiredOptions, array_merge(array_keys($options), array_keys($this->options))))
		{
			throw new Exception(sprintf('%s nécessite les options suivantes : %s.', get_class($this), implode(', ', $diff)));
		}

		//Une fois que tout est bon, on merge les messages d'erreur et les options.
		$this->errorMessages = array_merge($this->errorMessages, $messages);
		$this->options = array_merge($this->options, $options);
	}

	/**
	 * Fonction se chargeant de configurer le validateur.
	 * @abstract
	 * @access protected
	 * @param array $options		Les options passées au constructeur.
	 * @param array $messages		Les messages passés au constructeur.
	 */
	 abstract protected function configure($options, $messages);

	/**
	 * Retourne la liste des erreurs retournées par la classe.
	 * @access public
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Cette fonction a deux rôles : valider la variable et la « nettoyer » pour
	 * qu'elle corresponde à un format donné (i.e. de date). Ne doit pas être
	 * réécrite.
	 * @access public
	 * @param mixed $value
	 * @return mixed
	 */
	public function clean($value)
	{
		$this->value = $value;
		if($this->getOption('trim') && is_string($value))
		{
			$value = trim($value);
		}
		if($this->getOption('required') && in_array($value, $this->getOption('empty_values')))
		{
			$this->addError('required');
		}
		return $this->doClean($value);
	}

	/**
	 * Se charge de la validation de la variable suivant les spécificités de
	 * chaque validateur étendant cette classe.
	 * @access protected
	 * @param mixed $value
	 * @return mixed
	 */
	abstract protected function doClean($value);

	/**
	 * Ajoute une erreur détectée.
	 * @access protected
	 * @param string $key		L'identifiant de l'erreur.
	 */
	protected function addError($key)
	{
		$message = $this->errorMessages[$key];
		$message = str_replace('%value%', $this->value, $message);
		foreach($this->options as $key => $value)
		{
			if(!is_array($value))
				$message = str_replace('%'.$key.'%', $value, $message);
		}
		$this->errors[] = $message;
		throw new Validator_Error($message);
	}

	/**
	 * Ajoute un message d'erreur.
	 * @access protected
	 * @param string $key		L'identifiant de l'erreur.
	 * @param string $value		Le message d'erreur explicite.
	 */
	protected function addMessage($key, $value)
	{
		$this->errorMessages[$key] = $value;
	}

	/**
	 * Redéfinit un message d'erreur.
	 * @access protected
	 * @param string $key		L'identifiant de l'erreur.
	 * @param string $value		Le message d'erreur explicite.
	 */
	protected function setMessage($key, $value)
	{
		$this->errorMessages[$key] = $value;
	}

	/**
	 * Ajoute une option disponible.
	 * @access protected
	 * @param string $key		L'identifiant de l'option.
	 * @param mixed $value		Sa valeur par défaut.
	 */
	protected function addOption($key, $value = null)
	{
		$this->options[$key] = $value;
	}

	/**
	 * Définit la valeur d'une option.
	 * @access protected
	 * @param string $key		L'identifiant de l'option.
	 * @param mixed $value		Sa valeur.
	 */
	protected function setOption($key, $value)
	{
		$this->options[$key] = $value;
	}

	/**
	 * Vérifie si une option existe et a été définie.
	 * @access protected
	 * @param string $key		L'identifiant de l'option.
	 * @return mixed
	 */
	protected function hasOption($key)
	{
		return isset($this->options[$key]) && !is_null($this->options[$key]);
	}

	/**
	 * Récupère la valeur d'une option (null si elle n'a pas été définie).
	 * @access protected
	 * @param string $key		L'identifiant de l'option.
	 * @return mixed
	 */
	protected function getOption($key)
	{
		return $this->hasOption($key) ? $this->options[$key] : null;
	}

	/**
	 * Marque une option comme étant requise.
	 * @access protected
	 * @param string $key		L'identifiant de l'option.
	 */
	protected function addRequiredOption($key)
	{
		$this->requiredOptions[] = $key;
	}
}