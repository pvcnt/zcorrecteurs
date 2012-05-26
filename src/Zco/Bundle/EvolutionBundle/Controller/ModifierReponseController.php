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

namespace Zco\Bundle\EvolutionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Contrôleur gérant la modification d'une réponse à une anomalie.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ModifierReponseController extends Controller
{
	public function defaultAction()
	{
		if(!empty($_GET['id2']) && is_numeric($_GET['id2']) && !empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosReponse = InfosReponse($_GET['id2']);
			if(empty($InfosReponse) || $InfosReponse['version_id_ticket'] != $_GET['id'])
				return redirect(358, 'index.html', MSG_ERROR);
		}
		else
		{
			return redirect(400, 'index.html', MSG_ERROR);
		}

		if(
			verifier('tracker_editer_reponses') ||
			($InfosReponse['utilisateur_id'] == $_SESSION['id'] && verifier('tracker_editer_reponses_siennes'))
		)
		{
			//Si on veut éditer la réponse
			if(isset($_POST['submit']))
			{
				EditerReponseTicket($_GET['id2'], $_POST['texte']);
				return redirect(192, 'demande-'.$_GET['id'].'-'.rewrite($InfosReponse['ticket_titre']).'.html');
			}

			//Inclusion de la vue
			fil_ariane(array(
				htmlspecialchars($InfosReponse['ticket_titre']) => 'demande-'.$_GET['id'].'.html',
				'Modifier une réponse'
			));
			
			return render_to_response('ZcoEvolutionBundle::editerReponse.html.php', array(
				'InfosReponse' => $InfosReponse,
				'titre' => $InfosReponse['ticket_titre'],
			));
		}
		else
			throw new AccessDeniedHttpException;
	}
}
