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
 * Classe permettant d'abstraire un formulaire. Est relié à des widgets et à
 * des validateurs. Se charge de l'affichage, de la validation et du réaffichage
 * en cas d'erreur.
 * Peut être utilisée de deux façons : soit en instanciant une classe étendant
 * celle-ci, soit en utilisant les méthodes à la volée.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Form implements ArrayAccess
{
	protected
		$data = array(),
		$defaults = array(),
		$labels = array(),
		$help = array(),
		$fieldsets = array(),
		$errors = array(),
		$validators = array(),
		$cleanedData = array(),
		$widgets = array();

	/**
	 * Constructeur de la classe.
	 * @access public
	 * @param array $defaults		Des données initiales (facultatif).
	 */
	public function __construct($defaults = array())
	{
		$this->defaults = $defaults;
		$this->configure();
		$this->addWidget('token', new Widget_Input_Token);
		\Container::get('zco_core.resource_manager')->requireResource('@ZcoCoreBundle/Resources/public/js/form.js');
	}

	/**
	 * Configuration de la classe. Les classes étendant celle-ci doivent définir
	 * cette méthode.
	 * @access protected
	 */
	protected function configure()
	{
	}

	/**
	 * Ajoute un fieldset au formulaire. Les options de configuration possibles
	 * sont :
	 *   - widgets : les widgets à inclure, peut être redéfini par attachFieldset.
	 *   - class : une liste de classes CSS.
	 *   - description : un texte à afficher en haut du fieldset.
	 * @access public
	 * @param string $name		Le nom à afficher dans la balise legend.
	 * @param string $options	Des options de configuration.
	 */
	public function addFieldset($name, $options = array())
	{
		!isset($options['widgets']) && $options['widgets'] = array();
		$this->fieldsets[$name] = $options;
	}

	/**
	 * Marque un ou plusieurs widget(s) comme étant attaché(s) à un fieldset.
	 * @param array|string $params		Le nom d'un widget, ou un tableau
	 *									associant widget => fieldset.
	 * @param array|null $fieldset		Le nom du fieldset si le premier
	 *									paramètre était le nom du widget.
	 */
	public function attachFieldset($params, $fieldset = null)
	{
		if(is_string($params) && !is_null($fieldset))
			$this->fieldsets[$fieldset]['widgets'][] = $params;
		else
		{
			foreach($params as $widget => $fieldset)
				$this->fieldsets[$fieldset]['widgets'][] = $widget;
		}
	}

	/**
	 * Ajoute une liste de widgets au formulaire.
	 * @access public
	 * @param array $widgets
	 */
	public function setWidgets($widgets)
	{
		$this->widgets = array_merge($this->widgets, $widgets);
	}

	/**
	 * Ajoute un widget au formulaire.
	 * @access public
	 * @param string $name		Le nom du widget (sera utile pour le
	 *							reconfigurer par la suite et générer le label).
	 * @param Widget $widget	Le widget.
	 */
	public function addWidget($name, Widget $widget)
	{
		$this->widgets[$name] = $widget;
		$this->validators[$name] = new Validator_Pass;
	}

	/**
	 * Définit des labels pour plusieurs widgets.
	 * @access public
	 * @param array $labels		Un tableau associant le nom du widget au label.
	 */
	public function setLabels($labels)
	{
		$this->labels = array_merge($this->labels, $labels);
	}

	/**
	 * Définit un label pour un widget. Si rien n'est défini, le label sera
	 * composé du nom du widget, où les underscores auront été remplacés par
	 * des espaces, avec une majuscule au début et deux points à la fin.
	 * @access public
	 * @param string $name			Le nom du widget.
	 * @param string $label			Le label à afficher.
	 */
	public function setLabel($name, $label)
	{
		$this->labels[$name] = $label;
	}

	/**
	 * Définit des textes d'aide pour un ou plusieurs widget(s).
	 * @access public
	 * @param string|array $name
	 * @param string|null $help
	 */
	public function setHelpText($name, $help = null)
	{
		if(is_string($name) && !is_null($help))
			$this->help[$name] = $help;
		else
			$this->help = array_merge($this->help, $name);
	}

	public function setValidators($validators)
	{
		$this->validators = array_merge($this->validators, $validators);
	}

	public function setValidator($name, Validator $validator)
	{
		$this->validators[$name] = $validator;
	}

	public function setDefault($widget, $value)
	{
		$this->defaults[$widget] = $value;
	}

	public function setDefaults($params)
	{
		$this->defaults = array_merge($this->defaults, $params);
	}

	public function bind($data)
	{
		$this->data = $data;
	}

	public function unBind()
	{
		$this->data = null;
	}

	public function isBound()
	{
		return !is_null($this->data);
	}

	public function isValid()
	{
		if(!$this->isBound())
			return false;

		$this->errors = array();
		foreach($this->widgets as $name => $widget)
		{
			if($name != 'token')
			try
			{
				$this->cleanedData[$name]
					= $this->validators[$name]->clean(
					isset($this->data[$name])
						? $this->data[$name] : null
				);
			}
			catch(Validator_Error $e)
			{
				$this->errors[$name] = $e->getMessage();
			}
		}
		return empty($this->errors);
	}

	public function getCleanedData($key = null)
	{
		return is_null($key) ? $this->cleanedData : $this->cleanedData[$key];
	}

	public function __toString()
	{
		return $this->render();
	}

	public function render()
	{
		//On effectue le rendu des erreurs en premier.
		/*if(!empty($this->errors))
		{
			$errors = afficher_erreur(sprintf('%s à la validation du formulaire.',
				(count($this->errors) > 1 ? 'Plusieurs erreurs ont été détectées ' : 'Une erreur a été détectée ')));
		}*/
		$errors = '';

		//Si on n'a pas défini de fieldsets, on effectue juste le rendu des widgets.
		if(empty($this->fieldsets))
		{
			return '<fieldset id="fieldset_for_all">'.$errors.$this->renderWidgets(array_keys($this->widgets)).'</fieldset>';
		}

		//Sinon on effectue aussi le rendu des fieldsets.
		else
		{
			$return = array();
			$rendered = array();
			foreach($this->fieldsets as $name => $params)
			{
				$tmp = sprintf('<fieldset id="fieldset_for_%s">', rewrite($name));
				if($name != 'NONE')
					$tmp .= sprintf('<legend>%s</legend>', $name);
				if(!empty($params['description']))
					$tmp .= sprintf('<p>%s</p>', $params['description']);

				$tmp .= $this->renderWidgets($params['widgets']);
				$tmp .= '</fieldset>';
				$return[] = $tmp;

				$rendered = array_merge($rendered, $params['widgets']);
			}
			// Rendu des widgets hors-fieldset
			$remaining = array_diff(array_keys($this->widgets), $rendered);
			$return[] = $this->renderWidgets($remaining);
			return $errors.implode("\n\n", $return);
		}
	}

	protected function renderWidgets($widgets)
	{
		$return = array();
		foreach($widgets as $widget)
			$return[] = $this->renderWidget($widget);
		return implode("\n", $return);
	}

	protected function renderWidget($name)
	{
		$widget = $this->widgets[$name];
		if((!array_key_exists($name, $this->labels) || $this->labels[$name]) && $widget->getOption('auto_label'))
		{
			$label = !empty($this->labels[$name]) ? $this->labels[$name] : ucfirst(str_replace('_', ' ', $name));
			$label = trim($label);
			if(!in_array($label[mb_strlen($label)-1], array(':', '?', '!', ';')))
				$label .= ' :';
		}
		$help = !empty($this->help[$name]) ? sprintf('<div class="help_text">%s</div>', $this->help[$name]) : '';
		$widget->setAttribute('name', $name);
		$widget->setAttribute('id', sprintf('id_for_%s', $name));
		if(isset($this->defaults[$name]))
			$widget->setAttribute('value', $this->defaults[$name]);
		elseif(isset($this->data[$name]))
			$widget->setAttribute('value', $this->data[$name]);

		if(!empty($this->errors[$name]))
			$tmp = sprintf('<div class="form_row_error" id="row_for_%s"><ul class="form_errors"><li>%s</li></ul>', $name, $this->errors[$name]);
		else
			$tmp = sprintf('<div class="form_row" id="row_for_%s">', $name);

		if(isset($label))
			$tmp .= sprintf('<label for="id_for_%s">%s</label>', $name, htmlspecialchars($label));
		$tmp .= $widget->render().$help.'</div>';

		return $tmp;
	}

	public function offsetGet($offset)
	{
		return isset($this->widgets[$offset]) ? $this->widgets[$offset] : null;
	}
	public function offsetSet($offset, $value)
	{
		$this->widgets[$offset] = $value;
	}
	public function offsetUnset($offset)
	{
		unset($this->widgets[$offset]);
	}
	public function offsetExists($offset)
	{
		return isset($this->widgets[$offset]);
	}
}
