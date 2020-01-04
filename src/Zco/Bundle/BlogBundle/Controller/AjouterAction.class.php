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

use Zco\Bundle\BlogBundle\Controller\BaseController;

/**
 * Contrôleur gérant l'ajout d'un billet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class AjouterAction extends BaseController
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl();
		Page::$titre .= ' - Ajouter un billet';

		//Si on a posté un nouveau billet
		if(isset($_POST['submit']))
		{
			if(!empty($_POST['titre']) && !empty($_POST['texte']) && !empty($_POST['intro']))
			{
				$IdBillet = AjouterBillet();
				
				return redirect(10, 'mes-billets.html');
			}
			else
				return redirect(17, '', MSG_ERROR, -1);
		}
		//Inclusion de la vue
		fil_ariane(array('Mes billets' => 'mes-billets.html', 'Ajouter un billet'));
		
		return render_to_response(array(
			'Categories' => ListerEnfants(GetIDCategorieCourante()),
			'tabindex_zform' => 5,
		));
	}
}
