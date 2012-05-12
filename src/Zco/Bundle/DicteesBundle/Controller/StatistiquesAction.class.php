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

/**
 * Statistiques sur un membre.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */

include(dirname(__FILE__).'/../modeles/statistiques.php');

class StatistiquesAction extends DicteesActions
{
	public function execute()
	{
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

		zCorrecteurs::VerifierFormatageUrl();

		$_POST['participations'] = isset($_POST['participations']) ?
			$_POST['participations'] : 10;

		Page::$titre = 'Mes statistiques';
		fil_ariane(Page::$titre);

		return render_to_response(array(
			'participations' => $_POST['participations'],
			'DernieresNotes' => DernieresNotes(
				$_POST['participations']
			),
			'MesStatistiques'=> MesStatistiques()
		));
	}
}
