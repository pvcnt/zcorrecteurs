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
 * Suppression d'une dictée.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */

class SupprimerAction extends DicteesActions
{
	public function execute()
	{
		// Vérification de l'existence de la dictée
		$Dictee = $_GET['id'] ? Dictee($_GET['id']) : null;
		if(!$Dictee)
			return redirect(501, 'index.html', MSG_ERROR);

		// Vérification du droit
		if(!DicteeDroit($Dictee, 'supprimer'))
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

		zCorrecteurs::VerifierFormatageUrl($Dictee->titre, true);
		Page::$titre = 'Supprimer une dictée';

		$url = 'dictee-'.$Dictee->id.'-'.rewrite($Dictee->titre).'.html';

		// Suppression / Annulation
		if(isset($_POST['confirmer']))
		{
			if($r = zCorrecteurs::verifierToken()) return $r;
			SupprimerDictee($Dictee);
			return redirect(504, 'index.html');
		}
		if(isset($_POST['annuler']))
			return new Symfony\Component\HttpFoundation\RedirectResponse($url);

		fil_ariane(array(
			htmlspecialchars($Dictee->titre) => $url,
			'Supprimer'
		));

		return render_to_response(compact('Dictee', 'url'));
	}
}
