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
 * Contrôleur gérant la suppression d'un MP.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class SupprimerAction extends Controller
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/lire.php');
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/participants.php');
		include(BASEPATH.'/src/Zco/Bundle/MpBundle/modeles/action_etendue_plusieurs_mp.php');
		if(!empty($_GET['id']) AND is_numeric($_GET['id']))
		{
			$InfoMP = InfoMP();

			if(isset($InfoMP['mp_id']) AND !empty($InfoMP['mp_id']) AND !empty($InfoMP['mp_participant_mp_id']))
			{
				if(isset($_POST['confirmation']) AND $_POST['confirmation'] == 'Oui')
				{
					$ListerParticipants = ListerParticipants($_GET['id']);
					SupprimerMP($_GET['id'], $InfoMP, $ListerParticipants);
					unset($_SESSION['MPsnonLus']);
					unset($_SESSION['MPs']);
					return redirect(287, 'index.html');
				}
				elseif(isset($_POST['annuler']))
				{
					return new Symfony\Component\HttpFoundation\RedirectResponse('lire-'.$_GET['id'].'.html');
				}
				else
				{
					fil_ariane(array(htmlspecialchars($InfoMP['mp_titre']) => 'lire-'.$_GET['id'].'.html', 'Supprimer le message privé'));
					Page::$titre = $InfoMP['mp_titre'].' - Suppression du MP - '.Page::$titre;
					return render_to_response(array('InfoMP' => $InfoMP));
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
