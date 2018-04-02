<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Contrôleur gérant l'affichage des billets en cours de rédaction.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class BrouillonsAction extends BlogActions
{
	public function execute()
	{
        if (!verifier('blog_voir_billets_redaction')) {
            throw new AccessDeniedHttpException();
        }
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

