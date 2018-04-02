<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
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
 * Contrôleur gérant le retrait des droits de maître à un participant.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class StatutNormalAction extends Controller
{
	public function execute()
	{
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
		zCorrecteurs::VerifierFormatageUrl(null, true, true);
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/lire.php');
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/participants.php');
		if(!empty($_GET['id']) AND is_numeric($_GET['id']) AND !empty($_GET['id2']) AND is_numeric($_GET['id2']))
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
					//Vérification : le participant à "démastoriser" existe-t-il ?
					$InfoParticipant = InfoParticipant();
					if(empty($InfoParticipant['mp_participant_id']) OR $InfoParticipant['mp_participant_statut'] == MP_STATUT_SUPPRIME)
					{
						return redirect(270, 'lire-'.$_GET['id'].'.html', MSG_ERROR);
					}
					//Vérification : a-t-on le droit de rendre ce participant "plus maître de conversation" ?
					if($InfoParticipant['mp_participant_statut'] == MP_STATUT_MASTER AND ($InfoMP['mp_participant_statut'] == MP_STATUT_OWNER OR verifier('mp_tous_droits_participants') OR ($InfoMP['mp_participant_statut'] == MP_STATUT_MASTER AND $_GET['id2'] == $_SESSION['id'])))
					{
						PlusMaitreConversation();
						return redirect(273, 'lire-'.$_GET['id'].'.html');
					}
					else
					{
						return redirect(274, 'lire-'.$_GET['id'].'.html', MSG_ERROR);
					}
				}
				else
				{
					throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
				}
			}
			else
			{
				return redirect(274, 'index.html', MSG_ERROR);
			}
		}
		else
		{
			return redirect(263, 'index.html', MSG_ERROR);
		}
	}
}
