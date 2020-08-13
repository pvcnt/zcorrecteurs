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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zco\Bundle\DicteesBundle\Controller\BaseController;

/**
 * Listage des dictées d'un membre.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class ProposerAction extends BaseController
{
	public function execute()
	{
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }

		// On veut proposer une dictée
		if(!empty($_GET['id']))
		{
			// Vérification de l'existence de la dictée
			$Dictee = $_GET['id'] ? Dictee($_GET['id']) : null;
			if(!$Dictee)
				return redirect(501, 'proposer.html', MSG_ERROR);
			if($Dictee->etat != DICTEE_BROUILLON)
				return redirect(510, 'proposer.html', MSG_ERROR);

			if(isset($_POST['confirmer']))
			{
				if($r = zCorrecteurs::verifierToken()) return $r;
				ProposerDictee($Dictee);
				return redirect(511, 'proposer.html');
			}
			if(isset($_POST['annuler']))
				return new Symfony\Component\HttpFoundation\RedirectResponse('proposer.html');

			Page::$titre = 'Proposer une dictée';
			$url = 'dictee-'.$Dictee->id.'-'.rewrite($Dictee->titre).'.html';
			fil_ariane(array(
				htmlspecialchars($Dictee->titre) => $url,
				'Proposer'
			));
			return render_to_response('ZcoDicteesBundle::confirmerProposition.html.php', compact('Dictee', 'url'));
		}

		Page::$titre = 'Mes dictées';
		fil_ariane(Page::$titre);
		
		return render_to_response(array(
			'Dictees' => DicteesUtilisateur()
		));
	}
}
