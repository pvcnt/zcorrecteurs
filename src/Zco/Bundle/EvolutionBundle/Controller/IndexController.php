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

namespace Zco\Bundle\EvolutionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant la vue globale du module (accueil).
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class IndexController extends Controller
{
	public function defaultAction()
	{
		$prive = verifier('tracker_voir_prives');

		//Inclusion de la vue
		fil_ariane('Vue globale');
		
		return render_to_response(array(
			'DerniersTickets' => Lister5DerniersTickets($prive, 'bug'),
			'DerniersTicketsModifies' => Lister5DerniersTicketsModifies($prive, 'bug'),
			'DernieresTaches' => Lister5DerniersTickets($prive, 'tache'),
			'DernieresTachesModifiees' => Lister5DerniersTicketsModifies($prive, 'tache'),
		));
	}
}
