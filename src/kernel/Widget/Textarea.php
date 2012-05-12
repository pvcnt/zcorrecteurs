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
 * Widget représentant une grande zone de texte.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
*/
class Widget_Textarea extends Widget
{
	public function configure($options, $attrs)
	{
		$this->setAttribute('cols', 40);
		$this->setAttribute('rows', 10);
	}

	public function render()
	{
		if($this->hasAttribute('value'))
		{
			$value = $this->getAttribute('value');
			$this->setAttribute('value', null);
		}
		else
			$value = '';
		return sprintf('<textarea%s>'.htmlspecialchars($value).'</textarea>', $this->flatAttrs());
	}
}
