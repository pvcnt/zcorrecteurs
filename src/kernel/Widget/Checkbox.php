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
