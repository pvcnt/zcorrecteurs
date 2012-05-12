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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant la suppression d'un recrutement.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class SupprimerRecrutementAction extends Controller
{
	public function execute()
	{
		if (empty($_GET['id']) || !is_numeric($_GET['id']))
		{
			return redirect(228, '/recrutement/', MSG_ERROR);
		}

		$recrutement = Doctrine_Core::getTable('Recrutement')->recuperer($_GET['id']);
		if (!$recrutement)
		{
			return redirect(229, 'gestion.html', MSG_ERROR);
		}
		
		zCorrecteurs::VerifierFormatageUrl($recrutement['nom'], true);
		Page::$titre = htmlspecialchars($recrutement['nom']);

		//Si on veut supprimer
		if (isset($_POST['confirmer']))
		{
			$recrutement->delete();
			return redirect(3, 'gestion.html');
		}
		//Si on annule
		elseif (isset($_POST['annuler']))
		{
			return new RedirectResponse('gestion.html');
		}

		//Inclusion de la vue
		fil_ariane(array(
			htmlspecialchars($recrutement['nom']) => 'recrutement-'.$recrutement['id'].'.html',
			'Supprimer le recrutement'
		));
		return render_to_response(array('recrutement' => $recrutement));
	}
}
