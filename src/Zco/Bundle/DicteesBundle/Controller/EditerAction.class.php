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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Modification d'une dictée.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class EditerAction extends Controller
{
	public function execute()
	{
		// Vérification de l'existence de la dictée
		$Dictee = $_GET['id'] ? Dictee($_GET['id']) : null;
		if(!$Dictee)
			throw new NotFoundHttpException();

		// Vérification du droit
		if(!DicteeDroit($Dictee, 'editer'))
			throw new AccessDeniedHttpException();

		if(isset($_SESSION['dictee_data']))
		{
			$_POST = $_SESSION['dictee_data'];
			unset($_SESSION['dictee_data']);
		}

		zCorrecteurs::VerifierFormatageUrl($Dictee->titre, true);
		Page::$titre = 'Modifier une dictée';

		include(dirname(__FILE__).'/../forms/AjouterForm.class.php');
		$Form = new AjouterForm;

		$url = '-'.$Dictee->id.'-'.rewrite($Dictee->titre).'.html';

		$data = $Dictee->toArray();
		$data['publique'] = $data['etat'] == DICTEE_VALIDEE;
		$data['tags'] = $Dictee->getTags();
		$data['auteur'] = $data['auteur_id']; unset($data['auteur_id']);
		$Form->setDefaults($data);

		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			if($r = zCorrecteurs::verifierToken()) return $r;
			$Form->bind($_POST);
			if($Form->isValid())
			{
				$r = EditerDictee($Dictee, $Form);
				if(!$r)
				{
					$_SESSION['dictee_data'] = $_POST;
					return redirect('Une erreur est survenue lors de l\'envoi du fichier audio.', 'editer'.$url, MSG_ERROR);
				}
				elseif($r instanceof Response)
					return $r;
				return redirect('La dictée a été modifiée.', 'dictee'.$url);
			}
			$Form->setDefaults($_POST);
		}

		fil_ariane(array(
			htmlspecialchars($Dictee->titre) => 'dictee'.$url,
			'Editer'
		));
		$this->get('zco_vitesse.resource_manager')->requireResource('@ZcoDicteesBundle/Resources/public/js/upload.js');
		
		return render_to_response(compact('Dictee', 'Form'));
	}
}
