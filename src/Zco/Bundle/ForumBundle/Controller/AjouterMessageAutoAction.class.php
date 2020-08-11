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

use Zco\Bundle\ForumBundle\Controller\BaseController;

/**
 * Action responsable de l'ajout d'un nouveau message automatique
 * à destination des modérateurs.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class AjouterMessageAutoAction extends BaseController
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();
		Page::$titre = 'Ajouter un message automatique';

		//Si on veut ajouter un message
		if(isset($_POST['send']))
		{
			if(empty($_POST['nom']) || empty($_POST['texte']))
				return redirect(17, '', MSG_ERROR);

			$message = new ForumMessageAuto;
			$message['tag'] = $_POST['tag'];
			$message['nom'] = $_POST['nom'];
			$message['texte'] = $_POST['texte'];
			$message['ferme'] = isset($_POST['ferme']);
			$message['resolu'] = isset($_POST['resolu']);
			$message->save();

			return redirect(34, 'gestion-messages-auto.html');
		}

		//Inclusion de la vue
		fil_ariane('Ajouter un message automatique');
		
		return render_to_response(array());
	}
}
