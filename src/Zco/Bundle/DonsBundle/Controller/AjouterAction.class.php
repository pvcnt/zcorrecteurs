<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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

/**
 * Contrôleur gérant l'ajout d'un nouveau don sur la page des donateurs.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class AjouterAction extends Controller
{
	public function execute()
	{
		if (isset($_POST['submit']))
		{
			if (!empty($_POST['pseudo']))
			{
				$membre = \Doctrine_Core::getTable('Utilisateur')->getOneByPseudo($_POST['pseudo']);
				if (!$membre)
				{
					redirect(1, 'ajouter.html', MSG_ERROR);
				}
			}
			else
			{
				redirect(1, 'ajouter-don.html', MSG_ERROR);
			}

			$don = new \Don();
			$don['utilisateur_id'] = $membre['id'];
			$don['date']           = $_POST['date'];
			$don['nom']            = $_POST['nom'];
			$don->save();
			
			return redirect(16, 'gestion.html');
		}

		\Page::$titre = 'Enregistrer un don';
		
		return render_to_response(array());
	}
}