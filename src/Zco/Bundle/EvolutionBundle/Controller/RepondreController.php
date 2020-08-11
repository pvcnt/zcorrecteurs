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
 * Contrôleur se chargeant de l'ajout d'une réponse à une demande.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class RepondreController extends Controller
{
	public function defaultAction()
	{
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosTicket = InfosTicket($_GET['id']);
			if(empty($InfosTicket) || !verifier('tracker_voir'))
				return redirect(357, 'index.html', MSG_ERROR);
		}
		else
		{
			return redirect(364, 'index.html', MSG_ERROR);
		}

		//Détermination des actions possibles
		$can_editer = (verifier('tracker_editer') || ($InfosTicket['id_demandeur'] == $_SESSION['id'] && verifier('tracker_editer_siens')));
		if(($InfosTicket['id_demandeur'] == $_SESSION['id'] && verifier('tracker_changer_priorite_siens')) || verifier('tracker_changer_priorite'))
			$can_changer_priorite = true;
		else
			$can_changer_priorite = false;
		if(($InfosTicket['id_demandeur'] == $_SESSION['id'] && verifier('tracker_changer_resolution_siens')) || ($InfosTicket['id_admin'] == $_SESSION['id'] && verifier('tracker_changer_resolution_siens_assignes')) || verifier('tracker_changer_resolution'))
			$can_changer_resolution = true;
		else
			$can_changer_resolution = false;
		if(verifier('tracker_assigner'))
			$can_changer_assigne = true;
		else
			$can_changer_assigne = false;

		//Si on veut envoyer une réponse
		if(isset($_POST['send']))
		{
			if($can_changer_assigne)
			{
				$assigner = $_POST['assigner'] != 0 ? $_POST['assigner'] : null;
			}
			else
				$assigner = $InfosTicket['id_admin'];
			if($can_changer_priorite)
				$priorite = $_POST['priorite'];
			else
				$priorite = $InfosTicket['version_priorite'];
			if($can_changer_resolution)
			{
				$etat = $_POST['etat'];
			}
			else
			{
				$etat = $InfosTicket['version_etat'];
			}
			if($can_editer)
			{
				$categorie = $_POST['categorie'];
			}
			else
			{
				$categorie = $InfosTicket['cat_id'];
			}

			if(!$can_editer && !$can_changer_resolution && !$can_changer_priorite && !$can_changer_assigne && empty($_POST['texte']))
				return redirect(17, 'repondre-'.$_GET['id'].'.html', MSG_ERROR, -1);

			$id_rep = AjouterReponse($_GET['id'], $_POST['texte'], $categorie, $priorite, $etat, $assigner);
			
			//Si on doit envoyer des MP de contact
			$ListerSuivisTicket = ListerSuivisTicket($_GET['id']);

			if(!empty($ListerSuivisTicket))
			{
				foreach($ListerSuivisTicket as $s)
				{
					if(!$s['lunonlu_suivi_envoye'] && $s['utilisateur_id'] != $_SESSION['id'])
					{
						$message = render_to_string('::mp_auto/demandes_suivi.html.php', array(
							'pseudo'  => $s['utilisateur_pseudo'],
							'id'      => $s['utilisateur_id'],
							'nom'     => $InfosTicket['ticket_titre'],
							'url'     => 'demande-'.$_GET['id'].'-'.rewrite($InfosTicket['ticket_titre']).'.html',
							'posteur' => $_SESSION['pseudo'],
							'texte'    => $_POST['texte'],
						));

						AjouterMPAuto('Une demande que vous suivez a été mise à jour', $InfosTicket['ticket_titre'], $s['utilisateur_id'], $message);
					}
				}
			}
			MarquerSuivisTicketEnvoyes($_GET['id']);

			return redirect(362, 'demande-'.$_GET['id'].'.html#r'.$id_rep);
		}

		//Si on veut citer un message
		if(!empty($_GET['id2']) && is_numeric($_GET['id2']))
		{
			if($_GET['id2'] != 0)
			{
				$InfosReponse = InfosReponse($_GET['id2']);
				if($InfosReponse['version_id_ticket'] == $_GET['id'] && !empty($InfosReponse['version_commentaire']))
					$texte_zform = '<citation nom="'.htmlspecialchars($InfosReponse['utilisateur_pseudo']).'">'.htmlspecialchars($InfosReponse['version_commentaire']).'</citation>';
			}
			else
			{
				$texte_zform = '<citation nom="'.htmlspecialchars($InfosTicket['pseudo_demandeur']).'">'.htmlspecialchars($InfosTicket['ticket_description']).'</citation>';
			}
		}
		else
			$texte_zform = '';

		//Inclusion de la vue
		fil_ariane(array(
			htmlspecialchars($InfosTicket['ticket_titre']) => 'demande-'.$_GET['id'].'.html',
			'Déposer une réponse'
		));
		
		return render_to_response(array(
			'InfosTicket' => $InfosTicket,
			'ListerEquipe' => ListerUtilisateursDroit('tracker_etre_assigne'),
			'ListerCategories' => ListerCategories(!verifier('code')),
			'texte_zform' => $texte_zform,
			'can_editer' => $can_editer,
			'can_changer_priorite' => $can_changer_priorite,
			'can_changer_resolution' => $can_changer_resolution,
			'can_changer_assigne' => $can_changer_assigne,
		));
	}
}
