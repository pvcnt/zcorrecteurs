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
 * Contrôleur gérant la suppression d'un sondage.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class SupprimerAction
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);

		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$sondage = Doctrine_Core::getTable('Sondage')->find($_GET['id']);
			if ($sondage == false)
				return redirect(7, 'index.html', MSG_ERROR);

			if (verifier('sondages_supprimer') || ($sondage['utilisateur_id'] == $_SESSION['id'] && verifier('sondages_supprimer_siens')))
			{
				//Suppression du sondage demandée.
				if (isset($_POST['confirmer']))
				{
					$sondage->delete();
					return redirect(3, 'gestion.html');
				}
				elseif (isset($_POST['annuler']))
				{
					return new Symfony\Component\HttpFoundation\RedirectResponse('gestion.html');
				}

				Page::$titre = $sondage['nom'];
				fil_ariane(array(
					'Gestion des sondages' => 'gestion.html',
					htmlspecialchars($sondage['nom']) => 'sondage-'.$sondage['id'].'-'.rewrite($sondage['nom']).'.html',
					'Supprimer le sondage',
				));
				
				return render_to_response(array('sondage'   => $sondage));
			}
			else
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}
		else
			return redirect(7, 'index.html', MSG_ERROR);
	}
}