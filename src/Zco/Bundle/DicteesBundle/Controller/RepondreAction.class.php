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
 * Réponse à une soumission.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class RepondreAction extends DicteesActions
{
	public function execute()
	{
		// Vérification de l'existence de la dictée
		$Dictee = $_GET['id'] ? Dictee($_GET['id']) : null;
		if(!$Dictee)
			return redirect(501, 'index.html', MSG_ERROR);

		if($Dictee->etat != DICTEE_PROPOSEE)
			return redirect(508, 'propositions.html', MSG_ERROR);

		zCorrecteurs::VerifierFormatageUrl($Dictee->titre, true);
		Page::$titre = 'Répondre à une soumission';

		include(dirname(__FILE__).'/../forms/RepondreForm.class.php');
		$Form = new RepondreForm;

		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			if($r = zCorrecteurs::verifierToken()) return $r;
			$Form->bind($_POST);
			if($Form->isValid())
				return redirect(RepondreDictee($Dictee, $Form) ? 506 : 507, 'propositions.html');
		}

		fil_ariane(Page::$titre);
        $this->get('zco_vitesse.resource_manager')->requireResources(array(
		    '@ZcoDicteesBundle/Resources/public/css/dictees.css',
		    '@ZcoLivredorBundle/Resources/public/css/livredor.css',
		));
		
		return render_to_response(compact('Dictee', 'Form'));
	}
}
