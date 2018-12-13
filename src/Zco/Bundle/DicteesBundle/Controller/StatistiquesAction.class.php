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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Zco\Bundle\DicteesBundle\Domain\Dictation;
use Zco\Bundle\DicteesBundle\Domain\DictationScoreDAO;

/**
 * Statistiques sur un membre.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class StatistiquesAction extends Controller
{
	public function execute()
	{
		$_POST['participations'] = $_POST['participations'] ?? 10;

		Page::$titre = 'Mes statistiques';

		return render_to_response('ZcoDicteesBundle::statistiques.html.php', [
			'participations' => $_POST['participations'],
			'DernieresNotes' => DictationScoreDAO::DernieresNotes($_POST['participations']),
			'MesStatistiques'=> DictationScoreDAO::MesStatistiques(),
            'DicteeCouleurs' => Dictation::COLORS,
            'DicteeDifficultes' => Dictation::LEVELS,
		]);
	}
}
