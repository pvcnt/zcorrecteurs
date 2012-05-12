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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant l'ajout d'un participant à un MP.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class AjouterParticipantAction extends Controller
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);
		include(__DIR__.'/../modeles/lire.php');
		include(__DIR__.'/../modeles/participants.php');

		$xhr = (isset($_GET['xhr']) && $_GET['xhr'] == 1);

		if(!empty($_GET['id']))
		{
			$InfoMP = InfoMP();
			if($InfoMP['mp_crypte'])
				return redirect(294, 'index.html', MSG_ERROR);
			$autoriser_ecrire = true;
			if(empty($InfoMP['mp_participant_mp_id']) && verifier('mp_espionner'))
			{
				$autoriser_ecrire = false;
			}
			if($autoriser_ecrire)
			{
				//Vérification de la limite du nombre de participants
				if(verifier('mp_nb_participants_max') != -1)
				{
					$ListerParticipants = ListerParticipants($_GET['id']);
					$NombreParticipants = 0;
					foreach($ListerParticipants as $valeur)
					{
						if($valeur['mp_participant_statut'] != MP_STATUT_SUPPRIME)
						{
							$NombreParticipants++;
						}
					}
					if($NombreParticipants >= verifier('mp_nb_participants_max'))
					{
						return redirect(268, 'lire-'.$_GET['id'].'.html', MSG_ERROR);
					}
				}

				if(	isset($InfoMP['mp_id']) &&
					!empty($InfoMP['mp_id']) &&
					($InfoMP['mp_participant_statut'] >= MP_STATUT_MASTER || verifier('mp_tous_droits_participants'))
				)
				{
					if(empty($_POST['pseudo']))
					{
						//Inclusion de la vue
						if (!$xhr)
							fil_ariane(array(htmlspecialchars($InfoMP['mp_titre'])
								=> 'lire-'.$_GET['id'].'.html',
								'Ajouter un participant au message'));
						Page::$titre = 'Ajout d\'un participant - '.Page::$titre;
						
						return render_to_response(array('InfoMP' => $InfoMP, 'xhr' => $xhr));
					}
					else
					{
						$return = AjouterParticipant();
						if($return == 265)
						{
							return redirect(265, 'lire-'.$_GET['id'].'.html');
						}
						else
						{
							return redirect($return, 'lire-'.$_GET['id'].'.html', MSG_ERROR);
						}
					}
				}
				else
				{
					return redirect(264, 'index.html', MSG_ERROR);
				}
			}
			else
			{
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
			}
		}
		else
		{
			return redirect(263, 'index.html', MSG_ERROR);
		}
	}
}
