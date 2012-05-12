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
 * Contrôleur se chargeant de gérer la participation d'un membre 
 * aux avant-premières.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class TestsAction
{
	public function execute()
	{
		$preferences = Doctrine_Core::getTable('UserPreference')->find($_SESSION['id']);
		
		if (isset($_GET['token']) && $_GET['token'] == $_SESSION['token'])
		{
			$preferences['beta_tests'] = (isset($_GET['participer']) && $_GET['participer']);
			$preferences->save();
			
			$_SESSION['prefs']['beta_tests'] = $preferences['beta_tests'];
			
			if ($preferences['beta_tests'])
			{
				setcookie('beta_tests', 'participer', time() + 3600*24*365, '/');
			}
			else
			{
				setcookie('beta_tests', '', time() - 3600, '/');
			}
			
			return redirect(6, (isset($_GET['prod']) && !$preferences['beta_tests']) ? URL_SITE : 'tests.html');
		}
		
		Page::$titre = 'Participer aux tests en avant-première';

		return render_to_response(array('participer' => $preferences['beta_tests']));
	}
}