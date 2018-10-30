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
* Contrôleur gérant la notation d'une copie par son correcteur.
*
* @author vincent1870 <vincent@zcorrecteurs.fr>
*/
class NoterAction extends Controller
{
	public function execute()
	{
        include_once(__DIR__.'/../modeles/candidatures.php');
        include_once(__DIR__.'/../modeles/recrutements.php');

        if (!verifier('recrutements_voir_candidatures')) {
            throw new AccessDeniedHttpException();
        }
		//Si on a bien envoyé une candidature
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosCandidature = InfosCandidature($_GET['id']);
			if(empty($InfosCandidature))
				throw new NotFoundHttpException();
			Page::$titre = 'Candidature de '.htmlspecialchars($InfosCandidature['utilisateur_pseudo']).' - Noter la copie';

			if($InfosCandidature['candidature_correcteur'] == $_SESSION['id'])
			{
				//Si on veut noter la copie
				if(isset($_POST['note']))
				{
					NoterCopie($_GET['id'], $_POST['note']);
					return redirect('Cette copie a bien été notée conformément à vos souhaits.', 'candidature-'.$_GET['id'].'.html');
				}

				//Inclusion de la vue
				fil_ariane(array(
					htmlspecialchars($InfosCandidature['recrutement_nom']) => 'recrutement-'.$InfosCandidature['recrutement_id'].'.html',
					'Candidature de '.htmlspecialchars($InfosCandidature['utilisateur_pseudo']) => 'candidature-'.$_GET['id'].'.html',
					'Noter la copie'
				));
				
				return render_to_response(array('InfosCandidature' => $InfosCandidature));
			}
			else
                throw new AccessDeniedHttpException();
		}
		else
            throw new NotFoundHttpException();
	}
}
