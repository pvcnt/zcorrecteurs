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

namespace Zco\Bundle\EvolutionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur se chargeant de la création d'une nouvelle demande.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class NouveauController extends Controller
{
	public function defaultAction()
	{
		//Si on veut envoyer une demande
		if(isset($_POST['send']))
		{
			if(empty($_POST['texte']) || empty($_POST['resume']))
				return redirect(17, '', MSG_ERROR, -1);

			$assigner = isset($_POST['assigner']) && is_numeric($_POST['assigner']) && (($_POST['assigner'] == $_SESSION['id'] && verifier('tracker_assigner_soi')) || verifier('tracker_assigner')) ? $_POST['assigner'] : null;
			$prive = isset($_POST['prive']) && verifier('tracker_voir_prives') ? 1 : 0;
			$critique = isset($_POST['critique']) ? 1 : 0;
			$type = in_array($_POST['type'], array('bug', 'tache')) ? $_POST['type'] : 'bug';

			$id = AjouterTicket($_POST['resume'], $_POST['priorite'],
				$_POST['categorie'], $assigner, $_POST['texte'], $prive,
				$_POST['url'], $critique, $type);


			//Ajout du suivi pour le créateur
			ChangerSuiviTicket($_SESSION['id'], $id, 1);

			return redirect(360, 'demande-'.$id.'.html');
		}

		//Inclusion de la vue
		fil_ariane(array('Créer une nouvelle demande'));
		
		return render_to_response(array(
			'ListerEquipe' => ListerUtilisateursDroit('tracker_etre_assigne'),
			'ListerCategories' => ListerCategories(!verifier('code')),
		));
	}
}
