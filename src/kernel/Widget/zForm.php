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
