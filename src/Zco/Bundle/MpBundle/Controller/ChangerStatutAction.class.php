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
 * Contrôleur gérant la fermeture / ouverture d'un MP.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class ChangerStatutAction extends Controller
{
	public function execute()
	{
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/lire.php');
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/action_etendue_plusieurs_mp.php');

		if(!empty($_GET['id']) AND is_numeric($_GET['id']) AND !empty($_GET['id2']) AND is_numeric($_GET['id2']) AND ($_GET['id2'] == 0 OR $_GET['id2'] == 1))
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
					//Vérification : a-t-on le droit de fermer/ouvrir un MP ?
					if(verifier('mp_fermer'))
					{
						if($_GET['id2'] == 0)
						{
							OuvrirMP($_GET['id']);
							return redirect('Le MP a bien été ouvert.', 'lire-'.$_GET['id'].'.html');
						}
						elseif($_GET['id2'] == 1)
						{
							FermerMP($_GET['id']);
							return redirect('Le MP a bien été fermé.', 'lire-'.$_GET['id'].'.html');
						}
					}
					else
					{
						return redirect(
						    'Vous ne pouvez pas changer le statut d\'un MP.',
                            'lire-'.$_GET['id'].'.html',
                            MSG_ERROR
                        );
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
