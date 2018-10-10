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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Ajout d'une dictée.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class AjouterAction extends Controller
{
	public function execute()
	{
		if (!verifier('dictees_ajouter')) {
		    throw new AccessDeniedHttpException();
        }
		Page::$titre = 'Ajouter une dictée';

		include(dirname(__FILE__).'/../forms/AjouterForm.class.php');
		$Form = new AjouterForm;

		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			if($r = zCorrecteurs::verifierToken()) return $r;
			$Form->bind($_POST);
			if($Form->isValid())
			{
				$r = AjouterDictee($Form);
				if(!$r)
					return redirect('Une erreur est survenue lors de l\'envoi du fichier audio.', '', MSG_ERROR);
				elseif($r instanceof Response)
					return $r;
				return redirect('La dictée a été ajoutée.', 'index.html');
			}
		}
		fil_ariane('Ajout d\'une dictée');
		$this->get('zco_vitesse.resource_manager')->requireResource('@ZcoDicteesBundle/Resources/public/js/upload.js');
		
		return render_to_response(compact('Form'));
	}
}
