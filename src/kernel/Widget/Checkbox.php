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

/**
 * Widget représentant une case à cocher.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
*/
class Widget_Checkbox extends Widget
{
	protected function configure($options, $attrs)
	{
	}

	public function render()
	{
		$checked = $this->getAttribute('value') == true ? ' checked="checked"' : '';
		$this->setAttribute('value', null);
		return sprintf('<input type="checkbox"%s%s />', $this->flatAttrs(), $checked);
	}
}
