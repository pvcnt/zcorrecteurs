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
use Zco\Bundle\DicteesBundle\Domain\Dictation;
use Zco\Bundle\DicteesBundle\Domain\DictationDAO;

/**
 * Listage des dictées d'un membre.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class ProposerAction extends Controller
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
			$Dictee = $_GET['id'] ? DictationDAO::Dictee($_GET['id']) : null;
			if(!$Dictee)
				throw new NotFoundHttpException();
			if($Dictee->etat != DICTEE_BROUILLON)
				return redirect('Vous ne pouvez proposer que vos brouillons.', 'proposer.html', MSG_ERROR);

			if(isset($_POST['confirmer']))
			{
				if($r = zCorrecteurs::verifierToken()) return $r;
                DictationDAO::ProposerDictee($Dictee);
				return redirect('Votre dictée a été proposée.', 'proposer.html');
			}
			if(isset($_POST['annuler']))
				return new RedirectResponse('proposer.html');

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
		
		return render_to_response('ZcoDicteesBundle::proposer.html.php', array(
			'Dictees' => DictationDAO::DicteesUtilisateur(),
            'DicteeEtats' => Dictation::STATUSES,
            'DicteeDifficultes' => Dictation::LEVELS,
		));
	}
}
