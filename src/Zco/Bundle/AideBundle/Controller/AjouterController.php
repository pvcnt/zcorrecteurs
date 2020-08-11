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

namespace Zco\Bundle\AideBundle\Controller;

/**
 * Ajout d'un nouveau sujet d'aide.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class AjouterController
{
	public function defaultAction()
	{
		\Page::$titre = 'Nouveau sujet d\'aide';

		if (!empty($_POST['texte']) && !empty($_POST['titre']))
		{
			$aide = new \Aide();
			$aide['categorie_id'] = $_POST['categorie'];
			$aide['icone']        = $_POST['icone'];
			$aide['titre']        = $_POST['titre'];
			$aide['contenu']      = $_POST['texte'];
			$aide['racine']       = isset($_POST['racine']);
			$aide->save();

			return redirect(2, 'index.html');
		}
		
		return render_to_response(array(
		    'categories' => \Doctrine_Core::getTable('Categorie')->ListerEnfants(GetIDCategorie('aide')),
		));
	}
}