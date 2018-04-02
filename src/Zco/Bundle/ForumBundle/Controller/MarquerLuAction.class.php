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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Contrôleur marquant tous les sujets comme lu.
 *
 * @author Skydreamer
 */
class MarquerLuAction extends ForumActions
{
	public function execute()
	{
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
		zCorrecteurs::VerifierFormatageUrl(null, true);

		//Inclusion des modèles
		include(dirname(__FILE__).'/../modeles/membres.php');

		if($_GET['id'] == 1)
		{
			MarquerForumsLus(true);
			return redirect('Tous les sujets ont été marqués comme lus.', '/forum/');
		}
		else if($_GET['id'] == 2)
		{
			MarquerForumsLus(false);
			return redirect('Tous les sujets ont été marqués comme non lus.', '/forum/');
		}
	}
}
