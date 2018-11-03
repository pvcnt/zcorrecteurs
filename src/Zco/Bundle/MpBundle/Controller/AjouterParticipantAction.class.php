<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Contrôleur gérant l'ajout d'un participant à un MP.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class AjouterParticipantAction extends Controller
{
	public function execute()
	{
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
		include(__DIR__.'/../modeles/lire.php');
		include(__DIR__.'/../modeles/participants.php');

		$xhr = (isset($_GET['xhr']) && $_GET['xhr'] == 1);

		if(!empty($_GET['id']))
		{
			$InfoMP = InfoMP();
			if($InfoMP['mp_crypte'])
				return redirect(
				    'Il n\'est pas possible d\'ajouter de participants à un MP crypté.',
                    'index.html',
                    MSG_ERROR
                );
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
						return redirect(
						    'Vous ne pouvez pas ajouter de participant à ce MP : la limite a été atteinte ou dépassée.',
                            'lire-'.$_GET['id'].'.html',
                            MSG_ERROR
                        );
					}
				}

				if(	isset($InfoMP['mp_id']) && !empty($InfoMP['mp_id']) &&
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
						if (AjouterParticipant()) {
							return redirect('Le participant a bien été ajouté.', 'lire-'.$_GET['id'].'.html');
						} else {
							return redirect(
							    'Impossible d\'ajouter ce membre au MP.',
                                'lire-'.$_GET['id'].'.html',
                                MSG_ERROR
                            );
						}
					}
				}
				else
				{
                    throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
				}
			}
			else
			{
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
			}
		}
		else
		{
            throw new Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}
	}
}
