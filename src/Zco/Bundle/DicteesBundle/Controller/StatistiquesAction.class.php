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
use Zco\Bundle\DicteesBundle\Domain\Dictation;

/**
 * Statistiques sur un membre.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class StatistiquesAction extends Controller
{
	public function execute()
	{
        include_once(__DIR__.'/../modeles/dictees.php');
        include_once(__DIR__.'/../modeles/statistiques.php');

		if (!empty($_GET['id2'])) // Graphiques
		{
			$d = null;
			if($_GET['id'] == GRAPHIQUE_FREQUENCE)
				$d = GraphiqueFrequenceNotes();
			elseif($_GET['id'] == GRAPHIQUE_EVOLUTION)
				$d = GraphiqueEvolutionNotes($_GET['id2']);
			else
				return new Symfony\Component\HttpFoundation\RedirectResponse('statistiques.html');

			$Response = new Symfony\Component\HttpFoundation\Response($d);
			$Response->headers->set('Content-Type', 'image/png');
			return $Response;
		}

		$_POST['participations'] = isset($_POST['participations']) ?
			$_POST['participations'] : 10;

		Page::$titre = 'Mes statistiques';
		fil_ariane(Page::$titre);

		return render_to_response('ZcoDicteesBundle::statistiques.html.php', [
			'participations' => $_POST['participations'],
			'DernieresNotes' => DernieresNotes(
				$_POST['participations']
			),
			'MesStatistiques'=> MesStatistiques(),
            'DicteeCouleurs' => Dictation::COLORS,
            'DicteeDifficultes' => Dictation::LEVELS,
		]);
	}
}
