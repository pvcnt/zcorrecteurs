<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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

        if (!verifier('recrutements_supprimer')) {
            throw new AccessDeniedHttpException();
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
