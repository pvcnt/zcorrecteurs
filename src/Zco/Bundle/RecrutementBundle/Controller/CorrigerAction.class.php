<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosCandidature = InfosCandidature($_GET['id']);
			if(empty($InfosCandidature))
				return redirect(227, '/recrutement/', MSG_ERROR);
			zCorrecteurs::VerifierFormatageUrl($InfosCandidature['candidature_pseudo'], true);

			if(!in_array($InfosCandidature['candidature_etat'], array(CANDIDATURE_ATTENTE_TEST, CANDIDATURE_TESTE)))
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

			if(!is_null($InfosCandidature['candidature_correcteur']) && !isset($_GET['delete']))
				return redirect(346, 'candidature-'.$_GET['id'].'.html', MSG_ERROR);

			if(isset($_GET['delete']) && $InfosCandidature['candidature_correcteur']!=$_SESSION['id'] && !verifier('recrutements_desattribuer_copie'))
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

			if(isset($_POST['submit']))
			{
				if(!isset($_GET['delete']))
				{
					DevenirCorrecteurCandidature($_GET['id']);
					return redirect(345, 'recrutement-'.$InfosCandidature['recrutement_id'].'.html');
				}
				else
				{
					SupprimerCorrecteurCandidature($_GET['id']);
					return redirect(347, 'recrutement-'.$InfosCandidature['recrutement_id'].'.html');
				}
			}

			fil_ariane(array(
				htmlspecialchars($InfosCandidature['recrutement_nom']) => 'recrutement-'.$InfosCandidature['recrutement_id'].'.html',
				'Corriger une copie'
			));
			return render_to_response(array('InfosCandidature' => $InfosCandidature));
		}
		else
			return redirect(226, '/recrutement/', MSG_ERROR);
	}
}
