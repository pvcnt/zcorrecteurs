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
 * Contrôleur gérant le marquage en non-lu d'un MP.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class MarquerNonLuAction extends Controller
{
	public function execute()
	{
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/lire.php');
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/action_etendue_plusieurs_mp.php');
		if(!empty($_GET['id']) AND is_numeric($_GET['id']))
		{
			$InfoMP = InfoMP();

			$autoriser_ecrire = true;
			if(empty($InfoMP['mp_participant_mp_id']) AND verifier('mp_espionner'))
			{
				$autoriser_ecrire = false;
			}

			if(isset($InfoMP['mp_id']) AND !empty($InfoMP['mp_id']) AND !empty($InfoMP['mp_participant_mp_id']) AND $autoriser_ecrire)
			{
				RendreMPNonLus($_GET['id']);
				unset($_SESSION['MPsnonLus']);
				return redirect(285, 'index.html');

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
