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
 * Contrôleur gérant la modification d'un don.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ModifierAction
{
	public function execute()
	{
		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$don = \Doctrine_Core::getTable('Don')->find($_GET['id']);
			if (!$don)
			{
				redirect(15, 'gestion.html', MSG_ERROR);
			}

			if (isset($_POST['submit']))
			{
				$don['date']          = $_POST['date'];
				$don['nom']           = $_POST['nom'];
				$don->save();

				return redirect(16, 'gestion.html');
			}

			\Page::$titre = 'Modifier un don';
			
			return render_to_response(array('don' => $don));
		}
	}
}