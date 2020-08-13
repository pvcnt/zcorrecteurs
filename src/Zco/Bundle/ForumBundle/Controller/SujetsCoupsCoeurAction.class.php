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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zco\Bundle\ForumBundle\Controller\BaseController;

/**
 * Controleur pour l'affichage des sujets en coups de coeur.
 *
 * @author Barbatos <barbatos.tsyke@gmail.com>
 */
class SujetsCoupsCoeurAction extends BaseController
{
	public function execute()
	{
        if (!verifier('mettre_sujets_coup_coeur')) {
            throw new AccessDeniedHttpException();
        }

		zCorrecteurs::VerifierFormatageUrl();
		Page::$titre = 'Gérer les sujets en coups de cœur';

		//Inclusion du modèle
		include(dirname(__FILE__).'/../modeles/moderation.php');

		if ((isset($_GET['action']) == 'changer_coup_coeur') && intval($_GET['id_sujet']) != null)
		{
			ChangerCoupCoeur($_GET['id_sujet'], true);
			return redirect(299, '/forum/sujets-coups-coeur.html');
		}

		//Inclusion de la vue
		fil_ariane('Gérer les sujets en coups de cœur');
		
		return render_to_response(array(
		    'ListerSujets' => ListerSujetsCoupsCoeur(),
		));
	}
}
