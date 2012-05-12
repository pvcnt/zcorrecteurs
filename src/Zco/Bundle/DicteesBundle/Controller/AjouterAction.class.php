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
 * Ajout d'une dictée.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class AjouterAction extends DicteesActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();
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
					return redirect(509, '', MSG_ERROR);
				elseif($r instanceof Response)
					return $r;
				return redirect(500, 'index.html');
			}
		}
		fil_ariane('Ajout d\'une dictée');
		$this->get('zco_vitesse.resource_manager')->requireResource('@ZcoDicteesBundle/Resources/public/js/upload.js');
		
		return render_to_response(compact('Form'));
	}
}
