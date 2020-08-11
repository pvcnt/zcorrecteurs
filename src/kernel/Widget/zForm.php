<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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
 * Widget représentant une zForm.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
*/
class Widget_zForm extends Widget
{
	public function configure($options, $attrs)
	{
		$this->addOption('upload_id_formulaire');
	}

	public function render()
	{
		$vars = array();
		$this->hasAttribute('value') && $vars['texte'] = $this->getAttribute('value');
		$this->hasAttribute('name') && $vars['id'] = $this->getAttribute('name');
		$this->hasOption('upload_id_formulaire') && $vars['upload_id_formulaire'] = $this->getAttribute('upload_id_formulaire');

		return render_to_string('::zform.html.php', $vars);
	}
}
