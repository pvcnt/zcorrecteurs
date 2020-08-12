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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant la réponse à un MP.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class RepondreAction extends Controller
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true, true);
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/lire.php');
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/participants.php');
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/ecrire.php');

		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfoMP = InfoMP();
			$autoriser_ecrire = true;
			if(empty($InfoMP['mp_participant_mp_id']) && verifier('mp_espionner'))
			{
				$autoriser_ecrire = false;
			}
			if($autoriser_ecrire)
			{
				if(isset($InfoMP['mp_id']) && !empty($InfoMP['mp_id']))
				{
					$ListerParticipants = ListerParticipants($InfoMP['mp_id']);
					$NombreParticipants = 0;
					foreach($ListerParticipants as $valeur)
					{
						if($valeur['mp_participant_statut'] > MP_STATUT_SUPPRIME)
						{
							$NombreParticipants++;
						}
					}
					if($InfoMP['mp_ferme'] && !verifier('mp_repondre_mp_fermes'))
					{
						return redirect(281, 'lire-'.$_GET['id'].'.html', MSG_ERROR);
					}
					elseif($NombreParticipants < 2)
					{
						return redirect(275, 'lire-'.$_GET['id'].'.html', MSG_ERROR);
					}
					else
					{
						$nouveauMessage = (isset($_POST['dernier_message']) &&
							$InfoMP['mp_dernier_message_id'] > $_POST['dernier_message']);

						MarquerMPLu($_GET['id']);

						if(isset($_POST['texte']))
						{
							$_POST['texte'] = trim($_POST['texte']);
						}
						if(empty($_POST['texte']) || (!isset($_POST['send']) && !isset($_POST['send_reponse_rapide'])) || $nouveauMessage)
						{
							if(!empty($_GET['id2']) && is_numeric($_GET['id2']))
							{
								$InfoMessage = InfoMessage($_GET['id2']);
								if(isset($InfoMessage['mp_message_id']) && !empty($InfoMessage['mp_message_id']))
								{
									if(empty($_POST['texte']))
									{
										$_POST['texte'] = '<citation nom="'.$InfoMessage['utilisateur_pseudo'].'">';
										$_POST['texte'] .= $InfoMessage['mp_message_texte'];
										$_POST['texte'] .= '</citation>';
									}
								}
							}
							fil_ariane(array(
								htmlspecialchars($InfoMP['mp_titre']) => 'lire-'.$_GET['id'].'.html',
								'Ajout d\'une réponse'
							));

							Page::$titre = $InfoMP['mp_titre'].' - Ajout d\'une réponse - '.Page::$titre;
							$this->get('zco_vitesse.resource_manager')->requireResources(array(
            				    '@ZcoForumBundle/Resources/public/css/forum.css',
            				    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
            				));

							return render_to_response(array(
								'InfoMP' => $InfoMP,
								'ListerParticipants' => $ListerParticipants,
								'NombreParticipants' => $NombreParticipants,
								//'DernieresReponses'  => ListerMessages(1, true),
								'RevueMP' => RevueMP(),
								'nouveauMessage' => $nouveauMessage,
							));
						}
						else
						{
							//On ajoute la réponse en BDD
							$NouveauMessageID = AjouterReponse();

							//On vide les caches de tous les participants
							$current_participant = 0;
							foreach($ListerParticipants as $valeur)
							{
								if($valeur['mp_participant_id'] != $_SESSION['id'] &&
								   $current_participant != $valeur['mp_participant_id'])
								{
									$current_participant = $valeur['mp_participant_id'];
									$this->get('zco_core.cache')->save('MPnonLu'.$valeur['mp_participant_id'], true, strtotime('+1 hour'));
								}
							}
							return redirect(34, 'lire-'.$_GET['id'].'-'.$NouveauMessageID.'.html');
						}
					}
				}
				else
				{
					return redirect(262, 'index.html', MSG_ERROR);
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
