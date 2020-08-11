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
use Symfony\Component\HttpFoundation\Request;

/**
 * Contrôleur se chargeant de la récupération des retours des utilisateurs.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class RetourExperienceController extends Controller
{
	public function defaultAction(Request $request)
	{
		\zCorrecteurs::VerifierFormatageUrl();
		\Page::$titre = 'Retour d\'expérience utilisateur';

		//Si on veut ajouter un projet
		if (!empty($_POST['contenu']))
		{
			if (verifier('connecte') || $_POST['captcha'] == $_POST['captcha1'] + $_POST['captcha2'])
			{
				$feedback = new \EvolutionFeedback();
				$feedback['utilisateur_id'] = verifier('connecte') ? $_SESSION['id'] : null;
				$feedback['email'] = $_POST['email'];
				$feedback['contenu'] = $_POST['contenu'];
				$feedback['ip'] = ip2long($request->getClientIp(true));
				$feedback->save();
				
				return redirect(1);
			}
			
			$_SESSION['erreur'][] = 'Votre réponse à la question est incorrecte.';
		}

		return render_to_response(array(
			'captcha1' => mt_rand(1, 10), 
			'captcha2' => mt_rand(1, 10)
		));
	}
}
