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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Page permettant de changer la personne responsable d'un partenariat.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ModifierCreateurAction extends Controller
{
	public function execute()
	{
		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$campagne = \Doctrine_Core::getTable('PubliciteCampagne')->find($_GET['id']);
			if (!$campagne)
				return redirect(12, 'index.html', MSG_ERROR);

			if (verifier('publicite_editer_createur') ||
				(verifier('publicite_editer_createur_siens') && $campagne['utilisateur_id'] == $_SESSION['id'])
			)
			{
				if (!empty($_POST['pseudo']))
				{
					$utilisateur = \Doctrine_Core::getTable('Utilisateur')->getOneByPseudo($_POST['pseudo']);
					if (!$utilisateur)
						return redirect(15, 'modifier-createur-'.$campagne['id'].'.html', MSG_ERROR);

					$campagne['utilisateur_id'] = $utilisateur['id'];
					$campagne->save();
					return redirect(16, 'campagne-'.$campagne['id'].'.html');
				}

				fil_ariane(array(
					htmlspecialchars($campagne['nom']) => 'campagne-'.$campagne['id'].'.html',
					'Créateur',
				));
				
				return render_to_response(array('campagne'   => $campagne));
			}
			else
				throw new AccessDeniedHttpException;
		}
		else
			return redirect(12, 'index.html', MSG_ERROR);
	}
}