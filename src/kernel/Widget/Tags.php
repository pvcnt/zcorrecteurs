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
 * Widget permettant de sélectionner des tags, éventuellement d'en créer.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class Widget_Tags extends Widget
{
	public function configure($options, $attrs)
	{
		$this->attrs['size'] = 60;
	}

	public function render()
	{
		$options = $this->options;

		/*Container::getService('zco_core.ajax')->addAutocomplete($this->attrs['id'], '/tags/ajax-liste.html',
			array('multiple'   => true,
			      'minLength'  => 0));*/

		return sprintf(
			'<input type="text"%s /><noscript>Tag1, Tag2, …</noscript>',
			$this->flatAttrs()
		);
	}
}
