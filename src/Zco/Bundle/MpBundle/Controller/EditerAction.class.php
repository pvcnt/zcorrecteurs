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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant la modification d'un message d'un MP si et
 * seulement si il n'a pas été lu entre temps.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class EditerAction extends Controller
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/lire.php');
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/participants.php');
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/ecrire.php');

		if(!empty($_GET['id']))
		{
			$InfoMessage = InfoMessage($_GET['id']);
			if(isset($InfoMessage['mp_message_id']) AND !empty($InfoMessage['mp_message_id']))
			{
				if($_SESSION['id'] == $InfoMessage['mp_message_auteur_id'])
				{
					$ListerParticipants = ListerParticipants($InfoMessage['mp_id']);

					//On va ici déterminer si au moins un des participants a lu le message que le type veut éditer. (Dès qu'un message est lu au moins par un participant, hop l'auteur de ce message ne peut plus l'éditer. ;)
					$stop = false;
					$InfoMessage['pas_autoriser_edition'] = false;
					foreach($ListerParticipants as $valeur)
					{
						if(!$stop)
						{
							if($valeur['mp_participant_id'] != $InfoMessage['mp_message_auteur_id'])
							{
								if($valeur['mp_lunonlu_message_id'] >= $InfoMessage['mp_message_id'])
								{
									$stop = true;
									$InfoMessage['pas_autoriser_edition'] = true;
								}
							}
						}
					}
					if($InfoMessage['mp_ferme'] AND !verifier('mp_repondre_mp_fermes'))
					{
						return redirect(281, 'lire-'.$InfoMessage['mp_id'].'-'.$_GET['id'].'.html', MSG_ERROR);
					}
					elseif($InfoMessage['pas_autoriser_edition'] && !verifier('mp_editer_ses_messages_deja_lus'))
					{
						return redirect(48, 'lire-'.$InfoMessage['mp_id'].'-'.$_GET['id'].'.html', MSG_ERROR);
					}
					if(empty($_POST['texte']))
					{
						//Inclusion de la vue
						fil_ariane(array(htmlspecialchars($InfoMessage['mp_titre']) => 'lire-'.$_GET['id'].'.html', '&Eacute;diter un message'));
						$this->get('zco_vitesse.resource_manager')->requireResources(array(
        				    '@ZcoForumBundle/Resources/public/css/forum.css',
        				    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
        				));
        				
						Page::$titre = 'Modification d\'un message - '.Page::$titre;
						return render_to_response(array(
							'InfoMessage' => $InfoMessage,
						));
					}
					else
					{
						//On édite la réponse en BDD
						if(EditerReponse() === false)
							return redirect(292, 'editer-'.$_GET['id'].'.html', MSG_ERROR);

						return redirect(35, 'lire-'.$InfoMessage['mp_id'].'-'.$_GET['id'].'.html');
					}
				}
				else
				{
					return redirect(280, 'lire-'.$InfoMessage['mp_id'].'-'.$_GET['id'].'.html', MSG_ERROR);
				}
			}
			else
			{
				return redirect(262, 'index.html', MSG_ERROR);
			}
		}
		else
		{
			return redirect(263, 'index.html', MSG_ERROR);
		}
	}
}
