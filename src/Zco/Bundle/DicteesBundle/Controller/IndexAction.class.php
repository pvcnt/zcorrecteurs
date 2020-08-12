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

use Zco\Bundle\DicteesBundle\Controller\BaseController;

/**
 * Accueil des dictées.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class IndexAction extends BaseController
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();

		Page::$titre = 'Accueil des dictées';
		fil_ariane(Page::$titre);
        $this->get('zco_vitesse.resource_manager')->requireResources(array(
		    '@ZcoAccueilBundle/Resources/public/css/home.css',
		    '@ZcoDicteesBundle/Resources/public/css/dictees.css',
		));
        /** @var \Doctrine\Common\Cache\Cache $cache */
        $cache = $this->get('zco_core.cache');

        return render_to_response(array(
			'DicteesAccueil'=> DicteesAccueil($cache),
			'DicteeHasard'	=> DicteeHasard($cache),
			'DicteesLesPlusJouees' => DicteesLesPlusJouees($cache),
			'Statistiques'	=> DicteesStatistiques($cache)
		));
	}
}
