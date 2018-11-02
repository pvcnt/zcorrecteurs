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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contrôleur gérant le désistement d'un candidat du recrutement.
 *
 * @author		Vanger
 */
class DesisterAction extends Controller
{
	public function execute()
	{
        include_once(__DIR__.'/../modeles/candidatures.php');
        include_once(__DIR__.'/../modeles/recrutements.php');

        if (!verifier('recrutements_desistement')) {
            throw new AccessDeniedHttpException();
        }
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosCandidature = InfosCandidature($_GET['id']);
			if(empty($InfosCandidature))
				throw new NotFoundHttpException();

			if($InfosCandidature['candidature_etat'] == CANDIDATURE_DESISTE)
                throw new AccessDeniedHttpException();

			if(isset($_POST['submit']))
			{
				DesisterCandidature($_GET['id']);
				return redirect(
				    'Le désistement de cette candidature a bien été pris en compte.',
                    'recrutement-'.$InfosCandidature['recrutement_id'].'.html'
                );
			}

			fil_ariane(array(
				htmlspecialchars($InfosCandidature['recrutement_nom']) => 'recrutement-'.$InfosCandidature['recrutement_id'].'.html',
				'Se désister'
			));
			return render_to_response(array('InfosCandidature' => $InfosCandidature));
		}
		else
            throw new NotFoundHttpException();
	}
}
