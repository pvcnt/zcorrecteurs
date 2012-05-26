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
 * Widget représentant une liste de choix.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
*/
class Widget_Select extends Widget
{
	protected function configure($options, $attrs)
	{
		$this->addOption('choices');
		$this->addRequiredOption('choices');
	}

	public function render()
	{
		if($this->hasAttribute('value'))
		{
			$selected = $this->getAttribute('value');
			$this->setAttribute('value', null);
		}

		$return =  sprintf('<select%s>', $this->flatAttrs());
		foreach($this->getOption('choices') as $key => $value)
		{
			$attrs = '';
			if(is_array($value) && isset($value[1]))
			{
				$attrs = $value[1];
				$value = $value[0];
			}
			$return .= sprintf(
				'<option value="%s"%s%s>%s</option>',
				htmlspecialchars($key),
				((isset($selected) && $key == $selected) ? ' selected="selected"' : ''),
				$attrs,
				htmlspecialchars($value)
			);
		}
		$return .= '</select>';

		return $return;
	}
}
