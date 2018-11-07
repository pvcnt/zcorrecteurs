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
 * Contrôleur gérant l'ajout d'une nouvelle alerte concernant un MP.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class AlerterAction extends Controller
{
	public function execute()
	{
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/lire.php');
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/participants.php');
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/alertes.php');

		if(!empty($_GET['id']) AND is_numeric($_GET['id']))
		{
			$InfoMP = InfoMP();

			if(isset($InfoMP['mp_id']) AND !empty($InfoMP['mp_id']))
			{
				$autoriser_ecrire = true;
				if(empty($InfoMP['mp_participant_mp_id']) AND verifier('mp_espionner'))
				{
					$autoriser_ecrire = false;
				}
				if($autoriser_ecrire)
				{
					$ListerParticipants = ListerParticipants($InfoMP['mp_id']);
					$AlerteDejaPostee = VerifierAlerteDejaPostee();
					$NombreParticipants = 0;
					foreach($ListerParticipants as $valeur)
					{
						if($valeur['mp_participant_statut'] > MP_STATUT_SUPPRIME)
						{
							$NombreParticipants++;
						}
					}
					if($InfoMP['mp_ferme'] AND !verifier('mp_repondre_mp_fermes'))
					{
						return redirect('Ce MP est fermé.', 'lire-'.$_GET['id'].'.html', MSG_ERROR);
					}
					elseif($NombreParticipants < 2)
					{
						return redirect(
						    'Vous êtes seul dans ce MP ! <img src="/images/smilies/siffle.png" alt=":-°" title=":-°" />',
                            'lire-'.$_GET['id'].'.html',
                            MSG_ERROR
                        );
					}
					elseif($AlerteDejaPostee)
					{
						return redirect('Les modérateurs ont déjà été prévenus.', 'lire-'.$_GET['id'].'.html', MSG_ERROR);
					}
					else
					{
						if(!isset($_POST['texte']))
						{
							//Inclusion de la vue
							fil_ariane(array(htmlspecialchars($InfoMP['mp_titre']) => 'lire-'.$_GET['id'].'.html', 'Alerter les modérateurs'));
							$this->get('zco_core.resource_manager')->requireResources(array(
            				    '@ZcoForumBundle/Resources/public/css/forum.css',
            				    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
            				));
            				
							Page::$titre = 'Alerter les modérateurs - '.Page::$titre;
							return $this->render('ZcoMpBundle::alerter.html.php', array(
							    'InfoMP' => $InfoMP,
                            ));
						}
						elseif(trim($_POST['texte']) == '')
							return redirect(
							    'Vous devez indiquer une raison.',
                                'alerter-'.$_GET['id'].'.html',
                                MSG_ERROR
                            );
						else
						{
							AjouterAlerte();
							return redirect('Les modérateurs ont bien été prévenus.', 'lire-'.$_GET['id'].'.html');
						}
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
