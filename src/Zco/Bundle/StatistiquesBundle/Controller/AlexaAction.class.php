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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Statistiques Alexa
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class AlexaAction
{
	public function execute()
	{
        if (!verifier('voir_stats_generales')) {
            throw new AccessDeniedHttpException();
        }
	    include_once(__DIR__.'/../modeles/alexa.php');
		Page::$titre = 'Classement Alexa';

		$anneeActuelle = date('Y');
		if (isset($_GET['annee']) && $_GET['annee'] >= 2000 && $_GET['annee'] <= $anneeActuelle)
			$annee = $_GET['annee'];
		else
			$annee = $anneeActuelle;

		if (isset($_GET['mois']) && $_GET['mois'] >= 1 && $_GET['mois'] <= 12)
			$mois = $_GET['mois'];
		else
			$mois = null;

		if ($_GET['id'])
		{
			$graphique = DessinerGraphique($annee, $mois);
			
			return new Response($graphique);
		}

		return render_to_response(array(
			'Rangs' => GetAlexaRanks($annee, $mois),
			'Mois'  => $mois,
			'Annee' => $annee
		));
	}
}
