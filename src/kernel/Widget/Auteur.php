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
 * Sélection / ajout d'auteurs.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
*/
class Widget_Auteur extends Widget
{
	public function configure($options, $attrs)
	{
		//$this->attrs['size'] = 60;
	}

	public function render()
	{
	    $value = isset($this->attrs['value']) ? $this->attrs['value'] : '';
		$html = '<select'.$this->flatAttrs().'>'."\n";
		$html .= '<option value="0" style="font-style: italic"> -- Aucun auteur</option>'
		    .'<optgroup label="Auteurs">';

		$auteurs = Doctrine_Core::getTable('Auteur')->liste();
		foreach ($auteurs as $Auteur)
			$html .= '<option value="'.$Auteur->id.'"'
				.($Auteur->id == $value ? ' selected="selected"' : '').'>'
				.htmlspecialchars($Auteur).'</option>'."\n";

		$html .= '</optgroup></select>';

		if (verifier('auteurs_ajouter'))
		{
			$html .= ' <a href="/auteurs/ajouter-1.html"
				onclick="window.open(\'/auteurs/ajouter-1.html#'.$this->attrs['id']
				.'\', \'ajouter-auteur\','
				.'\'width=800, height=500, status=no, location=no, menubar=no, '
				.'scrollbars=yes\'); return false;">
				<img src="/pix.gif" class="fff add" alt="+"
				title="L\'auteur n\'est pas dans la liste"/></a>';
		}

		return $html;
	}
}
