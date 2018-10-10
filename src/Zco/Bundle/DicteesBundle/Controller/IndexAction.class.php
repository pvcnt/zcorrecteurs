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

/**
 * Accueil des dictées.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class IndexAction extends Controller
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();

		Page::$titre = 'Accueil des dictées';
		fil_ariane(Page::$titre);
		$this->get('zco_core.resource_manager')->requireResources(array(
		    '@ZcoPagesBundle/Resources/public/css/home.css',
		    '@ZcoDicteesBundle/Resources/public/css/dictees.css',
		));

		return render_to_response(array(
			'DicteesAccueil'=> DicteesAccueil(),
			'DicteeHasard'	=> DicteeHasard(),
			'DicteesLesPlusJouees' => DicteesLesPlusJouees(),
			'Statistiques'	=> DicteesStatistiques()
		));
	}
}
