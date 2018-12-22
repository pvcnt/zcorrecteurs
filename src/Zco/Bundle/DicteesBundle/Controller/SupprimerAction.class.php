<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\DicteesBundle\Domain\DictationDAO;

/**
 * Suppression d'une dictée.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class SupprimerAction extends Controller
{
	public function execute()
	{
		// Vérification de l'existence de la dictée
		$Dictee = $_GET['id'] ? DictationDAO::Dictee($_GET['id']) : null;
		if(!$Dictee)
			throw new NotFoundHttpException();

		// Vérification du droit
		if(!DictationDAO::DicteeDroit($Dictee, 'supprimer'))
			throw new AccessDeniedHttpException();

		zCorrecteurs::VerifierFormatageUrl($Dictee->titre, true);
		Page::$titre = 'Supprimer une dictée';

		$url = 'dictee-'.$Dictee->id.'-'.rewrite($Dictee->titre).'.html';

		// Suppression / Annulation
		if(isset($_POST['confirmer']))
		{
			if($r = zCorrecteurs::verifierToken()) return $r;
            DictationDAO::SupprimerDictee($Dictee);
			return redirect('La dictée a été supprimée.', 'index.html');
		}
		if(isset($_POST['annuler']))
			return new RedirectResponse($url);

		fil_ariane(array(
			htmlspecialchars($Dictee->titre) => $url,
			'Supprimer'
		));

		return render_to_response('ZcoDicteesBundle::supprimer.html.php', compact('Dictee', 'url'));
	}
}
