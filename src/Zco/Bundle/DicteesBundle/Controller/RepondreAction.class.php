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
 * Réponse à une soumission.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class RepondreAction extends Controller
{
	public function execute()
	{
        include_once(__DIR__.'/../modeles/dictees.php');

        if (!verifier('dictees_publier')) {
            throw new AccessDeniedHttpException();
        }
		// Vérification de l'existence de la dictée
		$Dictee = $_GET['id'] ? Dictee($_GET['id']) : null;
		if(!$Dictee)
			throw new NotFoundHttpException();

		if($Dictee->etat != DICTEE_PROPOSEE)
			return redirect('Cette dictée n\'est pas proposée.', 'propositions.html', MSG_ERROR);

		zCorrecteurs::VerifierFormatageUrl($Dictee->titre, true);
		Page::$titre = 'Répondre à une soumission';

		include(__DIR__.'/../forms/RepondreForm.class.php');
		$Form = new RepondreForm;

		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			if($r = zCorrecteurs::verifierToken()) return $r;
			$Form->bind($_POST);
			if($Form->isValid())
				return redirect(
				    RepondreDictee($Dictee, $Form) ? 'La proposition a été acceptée.' : 'La proposition a été refusée.',
                    'propositions.html'
                );
		}

		fil_ariane(Page::$titre);
        $this->get('zco_core.resource_manager')->requireResources(array(
		    '@ZcoDicteesBundle/Resources/public/css/dictees.css',
		));
		
		return render_to_response(compact('Dictee', 'Form'));
	}
}
