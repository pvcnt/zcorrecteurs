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

use Zco\Bundle\ForumBundle\Controller\BaseController;

/**
 * Contrôleur pour l'édition d'un message automatique.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EditerMessageAutoAction extends BaseController
{
	public function execute()
	{
		Page::$titre = 'Modifier un message automatique';

		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$message = Doctrine_Core::getTable('ForumMessageAuto')->find($_GET['id']);
			if($message === false)
				return redirect(46, '', MSG_ERROR);

			zCorrecteurs::VerifierFormatageUrl($message['nom'], true);

			//Si on veut éditer le message
			if(isset($_POST['send']))
			{
				if(empty($_POST['nom']) || empty($_POST['texte']))
					return redirect(17, '', MSG_ERROR);

				$message['tag'] = $_POST['tag'];
				$message['nom'] = $_POST['nom'];
				$message['texte'] = $_POST['texte'];
				$message['ferme'] = isset($_POST['ferme']);
				$message['resolu'] = isset($_POST['resolu']);
				$message->save();

				return redirect(35, 'gestion-messages-auto.html');
			}

			//Inclusion de la vue
			fil_ariane(array(
				'Gérer les messages automatiques' => 'gestion-messages-auto.html',
				'Modifier un message automatique'
			));
			
			return render_to_response(array(
				'InfosMessage' => $message,
			));
		}
		else
			return redirect(44, 'gestion-messages-auto.html', MSG_ERROR);
	}
}
