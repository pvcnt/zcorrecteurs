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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zco\Bundle\BlogBundle\Domain\BlogDAO;
use Zco\Bundle\ContentBundle\Domain\CategoryDAO;

/**
 * Contrôleur gérant l'ajout d'un billet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class AjouterAction extends BlogActions
{
	public function execute()
	{
	    if (!verifier('connecte')) {
	        throw new AccessDeniedHttpException();
        }
		Page::$titre .= ' - Ajouter un billet';

		//Si on a posté un nouveau billet
		if(isset($_POST['submit']))
		{
			if(!empty($_POST['titre']) && !empty($_POST['texte']) && !empty($_POST['intro']))
			{
                BlogDAO::AjouterBillet();
				
				return redirect('Le billet a bien été ajouté.', 'mes-billets.html');
			}
			return redirect('Vous devez remplir tous les champs nécessaires !', '', MSG_ERROR);
		}
		fil_ariane(array('Mes billets' => 'mes-billets.html', 'Ajouter un billet'));
		
		return render_to_response('ZcoBlogBundle::ajouter.html.php', array(
			'Categories' => CategoryDAO::ListerEnfants(CategoryDAO::GetIDCategorieCourante()),
			'tabindex_zform' => 5,
		));
	}
}
