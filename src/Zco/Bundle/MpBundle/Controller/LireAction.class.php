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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contrôleur gérant la lecture d'un MP et les actions associées.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class LireAction extends Controller
{
	public function execute()
	{
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
		zCorrecteurs::VerifierFormatageUrl(null, true, true, 1);
		include(__DIR__.'/../modeles/lire.php');
		include(__DIR__.'/../modeles/participants.php');
		include(__DIR__.'/../modeles/dossiers.php');
		include(__DIR__.'/../modeles/action_etendue_plusieurs_mp.php');

		if(!empty($_GET['id']))
		{
			$InfoMP = InfoMP();

			if(isset($InfoMP['mp_id']) AND !empty($InfoMP['mp_id']))
			{
				if(!empty($_GET['id2']) AND is_numeric($_GET['id2']))
				{
					$page = TrouverLaPageDeCeMessage($_GET['id2']);
					$page = ($page > 1) ? '-p'.$page : '';
					return new RedirectResponse('lire-'.$_GET['id'].$page.'.html#m'.$_GET['id2']);
				}

				$autoriser_ecrire = true;
				if(empty($InfoMP['mp_participant_mp_id']) AND verifier('mp_espionner'))
				{
					$autoriser_ecrire = false;
				}

				$ListerDossiers = ListerDossiers();

				if($autoriser_ecrire)
				{
					if(isset($_POST['deplacer_lieu']) AND is_numeric($_POST['deplacer_lieu']) AND $ListerDossiers)
					{
						DeplacerMP($_GET['id'], $_POST['deplacer_lieu']);
						return redirect('Le MP a bien été déplacé.', 'lire-'.$_GET['id'].'.html');
					}
				}

				$page = !empty($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
				$_GET['p'] = $page;
				$ListePages = liste_pages($page, ceil(($InfoMP['mp_reponses']+1) / 20), $InfoMP['mp_reponses']+1, 20, 'lire-'.$_GET['id'].'-p%s.html');

				$ListerParticipants = ListerParticipants($_GET['id']);
				$ListerMessages = ListerMessages($page);

				$InfosLuNonlu = array(
					'mp_lunonlu_utilisateur_id' => $_SESSION['id'],
					'mp_lunonlu_message_id' =>  $InfoMP['mp_lunonlu_message_id']
				);
				if($autoriser_ecrire)
				{
					RendreLeMPLu($_GET['id'], ceil(($InfoMP['mp_reponses']+1) / 20), $InfoMP['mp_dernier_message_id'], $ListerMessages, $InfosLuNonlu);
				}

				if($autoriser_ecrire && !verifier('mp_editer_ses_messages_deja_lus'))
				{
					//On va ici déterminer si au moins un des participants a lu un message (pour chaque message). Ceci afin de savoir pour chaque message si on autorise l'édition ou pas. (Dès qu'un message est lu au moins par un participant, hop l'auteur de ce message ne peut plus l'éditer. ;)
					foreach($ListerMessages as $clef => $valeur)
					{
						$stop = false;
						foreach($ListerParticipants as $valeur2)
						{
							if(!$stop)
							{
								if($valeur2['mp_participant_id'] != $valeur['mp_message_auteur_id'])
								{
									if($valeur2['mp_lunonlu_message_id'] >= $valeur['mp_message_id'])
									{
										$stop = true;
										$ListerMessages[$clef]['pas_autoriser_edition'] = true;
									}
								}
							}
						}
					}
				}
				Page::$titre = $InfoMP['mp_titre'].' - '.Page::$titre;
				fil_ariane(array(
					htmlspecialchars($InfoMP['mp_titre']) => 'lire-'.$_GET['id'].'.html',
					'Lire le message privé'
				));
				$this->get('zco_core.resource_manager')->requireResources(array(
				    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
				    '@ZcoForumBundle/Resources/public/css/forum.css',
				    '@ZcoCoreBundle/Resources/public/js/zform.js',
				));
				
				return render_to_response(array(
					'MPTotal' => $_SESSION['MPs'],
					'InfoMP' => $InfoMP,
					'autoriser_ecrire' => $autoriser_ecrire,
					'ListerDossiers' => $ListerDossiers,
					'ListePages' => $ListePages,
					'ListerParticipants' => $ListerParticipants,
					'ListerMessages' => $ListerMessages,
					'InfosLuNonlu' => $InfosLuNonlu,
					'page' => $page,
				));
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
