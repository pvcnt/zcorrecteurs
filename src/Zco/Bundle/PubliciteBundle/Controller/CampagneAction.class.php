<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
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

/**
 * Page affichant la liste des publicités créées dans le cadre d'une
 * campagne, ainsi que les options de modification des propriétés de
 * la campagne.
 *
 * @package		zCorrecteurs.fr
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class CampagneAction extends PubliciteActions
{
	public function execute()
	{
		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$campagne = Doctrine_Core::getTable('PubliciteCampagne')->find($_GET['id']);
			if ($campagne == false)
				return redirect(12, 'index.html', MSG_ERROR);

			if (verifier('publicite_voir') ||
				(verifier('publicite_proposer') && $campagne['utilisateur_id'] == $_SESSION['id'])
			)
			{
				Page::$titre = htmlspecialchars($campagne['nom']);
				$publicites = Doctrine_Core::getTable('Publicite')->findByCampagneId($campagne['id']);
				fil_ariane(htmlspecialchars($campagne['nom']));
				
				return render_to_response(array(
					'campagne'   => $campagne,
					'publicites' => $publicites,
				));
			}
			else
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}
		else
			return redirect(12, 'index.html', MSG_ERROR);
	}
}