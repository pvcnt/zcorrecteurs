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
 * Contrôleur se chargeant de réinitialiser le compteur
 * de clics d'une publicité pour une date donnée.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class RazClicsAction
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);
		
		if (empty($_GET['token']) || $_GET['token'] != $_SESSION['token'])
		{
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}

		if (!empty($_GET['id']) && is_numeric($_GET['id']) && !empty($_GET['date']))
		{
			$publicite = Doctrine_Core::getTable('Publicite')->find($_GET['id']);
			if($publicite === false)
				return redirect(6, 'index.html', MSG_ERROR);

			if (strtotime($_GET['date']) === false)
				return redirect(8, 'publicite-'.$publicite['id'].'.html', MSG_ERROR);

			$publicite->razClics($_GET['date']);
			return redirect(7, 'publicite-'.$publicite['id'].'.html');
		}
		else
			return redirect(6, 'index.html', MSG_ERROR);
	}
}