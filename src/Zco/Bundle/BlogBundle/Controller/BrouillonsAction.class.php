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
 * Contrôleur gérant l'affichage des billets en cours de rédaction.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class BrouillonsAction extends BlogActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();
		Page::$titre .= ' - Voir les billets en cours de rédaction';

		list($ListerBillets, $Auteurs) = ListerBillets(array('etat' => BLOG_BROUILLON));

		//Inclusion de la vue
		fil_ariane('Liste des billets en cours de rédaction');
		
		return render_to_response(array(
			'ListerBillets' => $ListerBillets,
			'Auteurs' => $Auteurs,
		));
	}
}

