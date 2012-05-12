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

/**
 * Modification d'une dictée.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class EditerAction extends DicteesActions
{
	public function execute()
	{
		// Vérification de l'existence de la dictée
		$Dictee = $_GET['id'] ? Dictee($_GET['id']) : null;
		if(!$Dictee)
			return redirect(501, 'index.html', MSG_ERROR);

		// Vérification du droit
		if(!DicteeDroit($Dictee, 'editer'))
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
					return redirect(509, 'editer'.$url, MSG_ERROR);
				}
				elseif($r instanceof Response)
					return $r;
				return redirect(505, 'dictee'.$url);
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
