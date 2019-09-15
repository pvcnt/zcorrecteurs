<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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
 * Classe de base servant à représenter un widget d'un formulaire.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 * @abstract
*/
abstract class Widget
{
	protected
		$attrs = array(),
		$options = array('auto_label' => true),
		$requiredOptions = array();

	/**
	 * Constructeur de la classe.
	 * @access public
	 * @param array $options		Une liste d'options pour le widget.
	 * @param array $attrs			Une liste de propriétés HTML pour le widget.
	 */
	public function __construct($options = array(), $attrs = array())
	{
		$this->configure($options, $attrs);

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

		//Une fois que tout est bon, on merge les attributs et les options.
		$this->attrs = array_merge($this->attrs, $attrs);
		$this->options = array_merge($this->options, $options);

		//On appelle une fonction de post-configuration si elle existe.
		if(method_exists($this, 'postConfigure'))
			$this->postConfigure();
	}

	/**
	 * Fonction effectuant le rendu que chaque widget doit implémenter.
	 * @abstract
	 * @return string		Le code HTML du widget.
	 */
	abstract public function render();

	/**
	 * Fonction se chargeant de configurer le widget, qui doit être implémentée
	 * par chaque widget.
	 * @see __construct
	 * @abstract
	 * @param array $options		La liste des options.
	 * @param array $attrs			La liste des attributs HTML.
	 */
	abstract protected function configure($options, $attrs);

	/**
	 * Accède à la valeur d'un attribut.
	 * @access public
	 * @param string $key		L'identifiant de l'attribut.
	 * @return mixed			Sa valeur.
	 */
	public function getAttribute($key)
	{
		return isset($this->attrs[$key]) ? $this->attrs[$key] : '';
	}

	/**
	 * Vérifie si un attribut existe.
	 * @access protected
	 * @param string $key		L'identifiant de l'attribut.
	 * @return boolean
	 */
	protected function hasAttribute($key)
	{
		return isset($this->attrs[$key]);
	}

	/**
	 * Récupère tous les attributs.
	 * @access protected
	 * @return array
	 */
	protected function getAttributes()
	{
		return $this->attrs;
	}

	/**
	 * Redéfinit la valeur d'un attribut.
	 * @access protected
	 * @param string $key		L'identifiant de l'attribut.
	 * @param string $value		Sa valeur.
	 */
	public function setAttribute($key, $value)
	{
		$this->attrs[$key] = $value;
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
	public function setOption($key, $value)
	{
		if(!in_array($key, array_keys($this->options)))
		{
			throw new Exception(sprintf('%s ne supporte pas l\'option suivante : %s.', get_class($this), $key));
		}
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
	 * @access public
	 * @param string $key		L'identifiant de l'option.
	 * @return mixed
	 */
	public function getOption($key)
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
		if(!isset($this->options[$key]))
			$this->options[$key] = null;
	}

	/**
	 * Transforme le tableau des attributs en chaine de caractères HTML.
	 * @access protected
	 * @param array $attrs		Des attributs additionnels.
	 * @return string			Une chaine de propriétés HTML.
	 */
	protected function flatAttrs($attrs = array())
	{
		$attrs = array_merge($this->attrs, $attrs);
		return implode('', array_map(array($this, 'flatAttrsCallback'), array_keys($attrs), array_values($attrs)));
	}

	/**
	 * Fonction de callback pour flatAttrs.
	 * @see flatAttrs
	 * @access protected
	 * @param string $key		Un nom d'attribut.
	 * @param string $value		Une valeur pour l'attribut.
	 * @return string			Une chaine traduisant la propriété en HTML.
	 */
	protected function flatAttrsCallback($key, $value)
	{
		return false === $value || is_null($value) || ('' === $value && 'value' != $key) ?
			'' : sprintf(' %s="%s"', $key, htmlspecialchars($value));
	}
}
