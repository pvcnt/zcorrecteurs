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

namespace Zco\Bundle\EvolutionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur se chargeant de l'affichage de la todo-list d'un développeur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class TodoController extends Controller
{
	public function defaultAction()
	{
		$id_admin = !empty($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : $_SESSION['id'];
		if ($id_admin != $_SESSION['id']) $admin = InfosUtilisateur($id_admin);
		
		\Page::$titre = $_SESSION['id'] == $id_admin ? 'Demandes qui me sont assignées' : 'Demandes assignées à '.htmlspecialchars($admin['utilisateur_pseudo']);
		
		$Tickets = ListerTickets(null, array(
			'prive' => verifier('tracker_voir_prives'),
			'admin' => $id_admin,
			'etat' => array('not', 4, 5, 7, 8, 9),
			'type' => 'bug',
		));
		$Taches = ListerTickets(null, array(
			'prive' => verifier('tracker_voir_prives'),
			'admin' => $id_admin,
			'etat' => array('not', 4, 5, 7, 8, 9),
			'type' => 'tache',
		));

		//Inclusion de la vue
		fil_ariane('Demandes assignées');
		
		return render_to_response(array(
			'Tickets' => $Tickets,
			'Taches' => $Taches,
			'ListerDeveloppeurs' => ListerUtilisateursDroit('tracker_etre_assigne'),
			'id_admin' => $id_admin,
		));
	}
}
