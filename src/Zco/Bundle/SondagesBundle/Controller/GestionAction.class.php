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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Contrôleur gérant l'affichage de la page de gestion des sondages.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class GestionAction
{
	public function execute()
	{
        if (!verifier_array([['sondages_ajouter', 'sondages_supprimer', 'sondages_editer', 'sondages_editer_siens', 'sondages_supprimer_siens']])) {
            throw new AccessDeniedHttpException();
        }

		Page::$titre = 'Gestion des sondages';
		
		return render_to_response(array(
		    'sondages' => Doctrine_Core::getTable('Sondage')->Lister(true),
		));
	}
}