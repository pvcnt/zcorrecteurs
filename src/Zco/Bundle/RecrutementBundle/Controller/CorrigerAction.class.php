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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contrôleur en charge de la prise en correction d'une copie, ou bien du retrait
 * du correcteur associé.
 *
 * @author		Vanger
 */
class CorrigerAction extends Controller
{
	public function execute()
	{
        include_once(__DIR__.'/../modeles/candidatures.php');
        include_once(__DIR__.'/../modeles/recrutements.php');

        if (!verifier('recrutements_attribuer_copie')) {
            throw new AccessDeniedHttpException();
        }
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosCandidature = InfosCandidature($_GET['id']);
			if(empty($InfosCandidature))
				throw new NotFoundHttpException();

			if(!in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_ATTENTE_TEST, CANDIDATURE_TESTE)))
				throw new AccessDeniedHttpException();

			if(!is_null($InfosCandidature['candidature_correcteur']) && !isset($_GET['delete']))
				return redirect('Cette copie est déjà attribuée à un correcteur.', 'candidature-'.$_GET['id'].'.html', MSG_ERROR);

			if(isset($_GET['delete']) && $InfosCandidature['candidature_correcteur']!=$_SESSION['id'] && !verifier('recrutements_desattribuer_copie'))
				throw new AccessDeniedHttpException();

			if(isset($_POST['submit']))
			{
				if(!isset($_GET['delete']))
				{
					DevenirCorrecteurCandidature($_GET['id']);
					return redirect('Vous êtes à présent le correcteur de cette copie.', 'recrutement-'.$InfosCandidature['recrutement_id'].'.html');
				}
				else
				{
					SupprimerCorrecteurCandidature($_GET['id']);
					return redirect('Le correcteur de cette copie a bien été supprimé.', 'recrutement-'.$InfosCandidature['recrutement_id'].'.html');
				}
			}

			fil_ariane(array(
				htmlspecialchars($InfosCandidature['recrutement_nom']) => 'recrutement-'.$InfosCandidature['recrutement_id'].'.html',
				'Corriger une copie'
			));
			return render_to_response(array('InfosCandidature' => $InfosCandidature));
		}
		else
            throw new NotFoundHttpException();
	}
}
