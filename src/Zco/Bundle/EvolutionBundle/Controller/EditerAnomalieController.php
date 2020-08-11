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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Contrôleur se chargeant de l'édition d'une anomalie.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EditerAnomalieController extends Controller
{
	public function defaultAction()
	{
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosTicket = InfosTicket($_GET['id']);
			if(empty($InfosTicket))
				return redirect(357, 'index.html', MSG_ERROR);
		}
		else
			return redirect(364, 'index.html', MSG_ERROR);

		\zCorrecteurs::VerifierFormatageUrl($InfosTicket['ticket_titre'], true, true);

		/**
		 * Edition de l'anomalie.
		 */
		if(!empty($_GET['id2']) && is_numeric($_GET['id2']) && $_GET['id2'] == 1 && (verifier('tracker_editer') || ($InfosTicket['id_demandeur'] == $_SESSION['id'] && verifier('tracker_editer_siens'))))
		{
			//Si on veut éditer
			if(!empty($_POST['texte']) && !empty($_POST['titre']))
			{
				$prive = isset($_POST['prive']) && verifier('tracker_voir_prives') ? 1 : 0;
				EditerTicket($_GET['id'], $_POST['titre'], $_POST['texte'], $_POST['lien'], $prive);
				return redirect(362, 'demande-'.$_GET['id'].'-'.rewrite($InfosTicket['ticket_titre']).'.html');
			}

			//Inclusion de la vue
			fil_ariane(array(
						htmlspecialchars($InfosTicket['ticket_titre']) => 'demande-'.$_GET['id'].'.html',
						'Modifier l\'anomalie'
			));
			
			return render_to_response(array('InfosTicket' => $InfosTicket));
		}

		/**
		 * Souscrire quelqu'un d'autre.
		 */
		elseif(!empty($_GET['id2']) && is_numeric($_GET['id2']) && $_GET['id2'] == 3 && verifier('tracker_forcer_suivi'))
		{
			//Si on veut forcer le suivi
			if(!empty($_POST['pseudo']))
			{
				$InfosUtilisateur = InfosUtilisateur($_POST['pseudo']);
				if(empty($InfosUtilisateur))
					return redirect(123, 'demande-'.$_GET['id'].'.html', MSG_ERROR, -1);
				ChangerSuiviTicket($InfosUtilisateur['utilisateur_id'], $_GET['id'], 1);
				
				return redirect(474, 'demande-'.$_GET['id'].'.html');
			}

			//Inclusion de la vue
			fil_ariane(array(
						htmlspecialchars($InfosTicket['ticket_titre']) => 'demande-'.$_GET['id'].'.html',
						'Ajouter quelqu\'un à la liste de suivi'
			));
			
			return render_to_response('ZcoEvolutionBundle::forcerSuivi.html.php', array('InfosTicket' => $InfosTicket));
		}
		
		/**
		 * Sinon erreur.
		 */
		throw new AccessDeniedHttpException();
	}
}
