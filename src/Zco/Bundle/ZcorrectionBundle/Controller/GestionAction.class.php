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
 * Contrôleur gérant l'administration de la zCorrection.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class GestionAction extends ZcorrectionActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();
		Page::$titre .= ' - Voir les tutoriels actifs';

		//Si on a demandé à passer une correction en prioritaire/non prioritaire
		if (!empty($_GET['prioritaire']) && is_numeric($_GET['prioritaire']) && verifier('zcorrection_priorite'))
		{
			SoumissionPrioritaire($_GET['prioritaire']);
			return redirect(152);
		}
		if (!empty($_GET['nonprioritaire']) && is_numeric($_GET['nonprioritaire']) && verifier('zcorrection_priorite'))
		{
			SoumissionNonPrioritaire($_GET['nonprioritaire']);
			return redirect(153);
		}

		//Si on a demandé à retirer la correction à quelqu'un
		if (!empty($_GET['retirer']) && is_numeric($_GET['retirer']) && verifier('zcorrection_retirer'))
		{
			if (RetirerCorrection($_GET['retirer']) == true)
			{
				return redirect(155);
			}
			else
			{
				return redirect(154);
			}
		}

		//(Ajout de DJ Fox) Si on demande la suppression de la soumission
		if(!empty($_GET['supprimer']) AND is_numeric($_GET['supprimer']) && verifier('zcorrection_supprimer'))
		{
			//Si on veut supprimer
			if(isset($_POST['confirmer']))
			{
				SupprimerSoumission($_GET['supprimer']);
				return redirect(156);
			}
			//Si on annule
			elseif(isset($_POST['annuler']))
			{
				return new Symfony\Component\HttpFoundation\RedirectResponse('gestion.html');
			}

			return render_to_response('ZcoZcorrectionBundle::supprimer.html.php');
		}

		//Inclusion de la vue
		fil_ariane('Liste des tutoriels actifs');
		$this->get('zco_vitesse.resource_manager')->requireResources(array(
		    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
		    '@ZcoCoreBundle/Resources/public/css/zcode.css',
		));
		
		return render_to_response(array(
			'ListerSoumissionsAdmin' => ListerSoumissionsAdmin(),
		));
	}
}
