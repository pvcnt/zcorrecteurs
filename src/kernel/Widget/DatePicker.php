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

/**
 * Widget représentant un champ de texte servant à sélectionner une date et / ou
 * une heure. Se sert d'un script Mootools pour afficher un calendrier au clic
 * dans le champ.
 *
 * @link http://www.monkeyphysics.com/mootools/script/2/datepicker
 * @author vincent1870 <vincent@zcorrecteurs.fr>
*/
class Widget_DatePicker extends Widget
{
	public function configure($options, $attrs)
	{
		$this->addOption('skin', 'vista');
		$this->addOption('toggleElements', false);
		$this->addOption('days', array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'));
		$this->addOption('dayShort', 2);
		$this->addOption('months', array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'));
		$this->addOption('monthShort', 3);
		$this->addOption('startDay', 1);
		$this->addOption('timePicker', false);
		$this->addOption('timePickerOnly', false);
		$this->addOption('yearPicker', true);
		$this->addOption('yearsPerPage', 20);
		$this->addOption('format', 'd/m/Y');
		$this->addOption('inputOutputFormat', 'Y-m-d');
		$this->addOption('animationDuration', 400);
		$this->addOption('startView', 'month');
		$this->addOption('allowEmpty', false);
		$this->addOption('minDate');
		$this->addOption('maxDate');
		$this->addOption('positionOffset', array(0, 0));
		$this->addOption('debug', false);
	}

	public function render()
	{
		//Traduit les attributs skin en pickerClass et positionOffset, dont
		//l'utilisation avait été simplifiée.
		$options = $this->options;
		$options['pickerClass'] = 'datepicker_'.$options['skin'];
		$options['positionOffset'] = array(
			'x' => $options['positionOffset'][0],
			'y' => $options['positionOffset'][1]
		);
		unset($options['skin']);

		$javelin = Container::get('zco_core.javelin');
		$javelin->initBehavior('datepicker', array('id' => $this->attrs['id'], 'options' => $options));
        
		return sprintf('<input type="text"%s /><noscript>Format : %s</noscript>', $this->flatAttrs(), $this->getOption('inputOutputFormat'));
	}
}
