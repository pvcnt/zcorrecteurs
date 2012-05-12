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
 * Widget représentant un champ de texte servant à sélectionner une date et
 * une heure. Se sert d'un script Mootools pour afficher un calendrier au clic
 * dans le champ.
 *
 * @link http://www.monkeyphysics.com/mootools/script/2/datepicker
 * @author vincent1870 <vincent@zcorrecteurs.fr>
*/
class Widget_DateTimePicker extends Widget_DatePicker
{
	public function configure($options, $attrs)
	{
		parent::configure($options, $attrs);
		$this->addOption('format', 'd/m/Y H:i:s');
		$this->addOption('inputOutputFormat', 'Y-m-d H:i:s');
	}
}
