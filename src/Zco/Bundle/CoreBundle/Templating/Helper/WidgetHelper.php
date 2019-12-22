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

namespace Zco\Bundle\CoreBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;

/**
 * Ensemble de fonctions aidant à afficher facilement des widgets 
 * complexes. Évite l'utilisation du framework de formulaires mais est 
 * déprécié.
 * 
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class WidgetHelper extends Helper
{
	/**
	 * Retourne le code pour afficher un champ de texte avec un petit calendrier
	 * Mootools s'affichant au clic pour sélectionner une date facilement.
	 *
	 * @param string $name		Nom du widget.
	 * @param string $value		Valeur du widget.
	 * @param array $options	Options pour personnaliser directement le widget.
	 * @param array $attrs		Attributs pour personnaliser directement le widget.
	 * @return string
	 */
	public function datePicker($name, $value = '', array $options = array(), array $attrs = array())
	{
		//On gère le décalage des arguments si nécessaire.
		if (is_array($value))
		{
			$options = $value;
			$value = '';
		}
		$attrs['value'] = !empty($value) ? $value : (!empty($attrs['value']) ? $attrs['value'] : '');

		if (!isset($attrs['name']))
		{
			$attrs['name'] = $name;
		}

		if (!isset($attrs['id']))
		{
			$attrs['id'] = $name;
		}

		$widget = new \Widget_DatePicker($options, $attrs);
		return $widget->render();
	}

	/**
	 * Idem que self::timePicker() mais avec une sélection d'heure en plus.
	 *
	 * @param string $name		Nom du widget.
	 * @param string $value		Valeur du widget.
	 * @param array $options	Options pour personnaliser directement le widget.
	 * @param array $attrs		Attributs pour personnaliser directement le widget.
	 * @return string
	 */
	public function dateTimePicker($name, $value = '', array $options = array(), array $attrs = array())
	{
		$options['timePicker'] = true;
		if (!isset($options['format']))
		{
			$options['format'] = 'd/m/Y à H:i';
		}
		if (!isset($options['inputOutputFormat']))
		{
			$options['inputOutputFormat'] = 'Y-m-d H:i';
		}

		return $this->datePicker($name, $value, $options, $attrs);
	}

	/**
	 * Renvoie le code HTML pour l'affichage d'une barre de progression.
	 *
	 * @param float $pourcent		Pourcentage à afficher (nombre de 0 à 1).
	 * @param integer $height		La taille de la barre en px (15 par défaut).
	 * @param integer $width		La longueur de la barre en px (400 par défaut).
	 */
	public function progressbar($pourcent, $height = 15, $width = 400)
	{
		return '<table class="UI_progress">' .
					'<tr>' .
						($pourcent != 0 ?
						'<td class="finished" ' .
						'style="width: '.($width * round($pourcent, 2)).'px; height: '.$height.'px;"> </td>' : '') .
						($pourcent != 100 ?
						'<td class="running" ' .
						'style="width: '.($width * (round(1-$pourcent, 2))).'px; height: '.$height.'px;"> </td>' : '') .
					'</tr>' .
				'</table>';
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'widget';
	}
}
