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

namespace Zco\Bundle\EvolutionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Contrôleur se chargeant de la suppression d'une réponse.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class SupprimerReponseController extends Controller
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

		//Si on veut supprimer la demande
		if(isset($_POST['confirmer']))
		{
			SupprimerReponse($_GET['id2'], $_GET['id'], $_GET['id2'] == $InfosReponse['ticket_id_version_courante']);
			return redirect(363, 'demande-'.$_GET['id'].'.html#reponses');
		}
		//Si on annule
		elseif(isset($_POST['annuler']))
		{
			return new RedirectResponse('demande-'.$_GET['id'].'.html#reponses');
		}

		//Inclusion de la vue
		fil_ariane(array(
			htmlspecialchars($InfosReponse['ticket_titre']) => 'demande-'.$_GET['id'].'.html',
			'Supprimer une réponse'
		));
		
		return render_to_response(array('InfosReponse' => $InfosReponse));
	}
}
