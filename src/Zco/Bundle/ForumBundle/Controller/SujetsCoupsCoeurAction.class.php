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
 * Controleur pour l'affichage des sujets en coups de coeur.
 *
 * @author Barbatos <barbatos.tsyke@gmail.com>
 */
class SujetsCoupsCoeurAction extends ForumActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();
		Page::$titre = 'Gérer les sujets en coups de cœur';

		//Inclusion du modèle
		include(dirname(__FILE__).'/../modeles/moderation.php');

		if ((isset($_GET['action']) == 'changer_coup_coeur') && intval($_GET['id_sujet']) != null)
		{
			ChangerCoupCoeur($_GET['id_sujet'], true);
			return redirect(299, '/forum/sujets-coups-coeur.html');
		}

		//Inclusion de la vue
		fil_ariane('Gérer les sujets en coups de cœur');
		
		return render_to_response(array(
		    'ListerSujets' => ListerSujetsCoupsCoeur(),
		));
	}
}
