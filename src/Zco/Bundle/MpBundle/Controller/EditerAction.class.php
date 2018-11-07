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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
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
						return redirect('Ce MP est fermé.', 'lire-'.$InfoMessage['mp_id'].'-'.$_GET['id'].'.html', MSG_ERROR);
					}
					elseif($InfoMessage['pas_autoriser_edition'] && !verifier('mp_editer_ses_messages_deja_lus'))
					{
                        throw new AccessDeniedHttpException();
					}
					if(empty($_POST['texte']))
					{
						//Inclusion de la vue
						fil_ariane(array(htmlspecialchars($InfoMessage['mp_titre']) => 'lire-'.$_GET['id'].'.html', '&Eacute;diter un message'));
						$this->get('zco_core.resource_manager')->requireResources(array(
        				    '@ZcoForumBundle/Resources/public/css/forum.css',
        				    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
        				));
        				
						Page::$titre = 'Modification d\'un message - '.Page::$titre;
						return $this->render('ZcoMpBundle::editer.html.php', array(
							'InfoMessage' => $InfoMessage,
						));
					}
					else
					{
						//On édite la réponse en BDD
						if(EditerReponse() === false)
							return redirect(
							    'Le destinataire n\'a pas renseigné de clé PGP, le MP ne peut donc pas être crypté.',
                                'editer-'.$_GET['id'].'.html',
                                MSG_ERROR
                            );

						return redirect('Le message a bien été modifié.', 'lire-'.$InfoMessage['mp_id'].'-'.$_GET['id'].'.html');
					}
				}
				else
				{
					throw new AccessDeniedHttpException();
				}
			}
			else
			{
                throw new NotFoundHttpException();
			}
		}
		else
		{
			throw new NotFoundHttpException();
		}
	}
}
