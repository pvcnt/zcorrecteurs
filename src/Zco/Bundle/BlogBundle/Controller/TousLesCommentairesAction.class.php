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

use Zco\Bundle\BlogBundle\Controller\BaseController;

/**
 * Contrôleur gérant l'affichage de tous les commentaires du blog.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class TousLesCommentairesAction extends BaseController
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, false, false, 1);

		$nbCommentairesParPage = 15;
		$page = !empty($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
		$ListerCommentaires = ListerTousLesCommentaires($page);
		$CompterCommentaires = CompterTousLesCommentaires();
		$ListePages = liste_pages($page, ceil($CompterCommentaires / $nbCommentairesParPage), $CompterCommentaires, $nbCommentairesParPage, 'tous-les-commentaires-p%s.html');

		//Inclusion de la vue
		Page::$titre = 'Liste de tous les commentaires';
		fil_ariane('Voir la liste de tous les commentaires');
		$this->get('zco_vitesse.resource_manager')->requireResources(array(
		    '@ZcoForumBundle/Resources/public/css/forum.css',
		    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
		));
		
		return render_to_response(array(
			'ListerCommentaires' => $ListerCommentaires,
			'CompterCommentaires' => $CompterCommentaires,
			'ListePages' => $ListePages,
		));
	}
}
