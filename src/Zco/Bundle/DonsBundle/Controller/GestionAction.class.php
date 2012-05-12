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
 * Contrôleur gérant l'affichage de tous les dons affichés sur la page des
 * donateurs.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class GestionAction
{
	public function execute()
	{
		//Supprimer un don.
		if (verifier('dons_supprimer') && !empty($_GET['supprimer']) && is_numeric($_GET['supprimer']) && isset($_GET['token']) && $_GET['token'] == $_SESSION['token'])
		{
			$don = Doctrine_Core::getTable('Don')->find($_GET['supprimer']);
			if ($don != false)
			{
				$don->delete();
				return redirect(19);
			}
		}
		
		$dons = Doctrine_Core::getTable('Don')->findAll();
		Page::$titre = 'Liste des dons';
		return render_to_response(array('dons' => $dons));
	}
}