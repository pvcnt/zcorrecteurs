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
 * Contrôleur gérant l'affichage des messages automatiques.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class GestionMessagesAutoAction extends ForumActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();
		Page::$titre = 'Gérer les messages automatiques';

		//Si on veut supprimer un message
		if(!empty($_GET['supprimer']) && is_numeric($_GET['supprimer']))
		{
			$message = Doctrine_Core::getTable('ForumMessageAuto')->find($_GET['supprimer']);
			if($message !== false)
			{
				$message->delete();
				return redirect(36, 'gestion-messages-auto.html');
			}
		}

		//Inclusion de la vue
		fil_ariane('Gérer les messages automatiques');
		$this->get('zco_vitesse.resource_manager')->requireResource(
		    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css'
		);
		
		return render_to_response(array(
		    'ListerMessages' => Doctrine_Core::getTable('ForumMessageAuto')->Lister(),
		));
	}
}
