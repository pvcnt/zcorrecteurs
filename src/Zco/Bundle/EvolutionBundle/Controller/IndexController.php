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
			'InfosUtilisateur' => InfosUtilisateur(ID_MBR_CHEF_SECURITE),
			'DerniersTickets' => Lister5DerniersTickets($prive, 'bug'),
			'DerniersTicketsModifies' => Lister5DerniersTicketsModifies($prive, 'bug'),
			'DernieresTaches' => Lister5DerniersTickets($prive, 'tache'),
			'DernieresTachesModifiees' => Lister5DerniersTicketsModifies($prive, 'tache'),
		));
	}
}
