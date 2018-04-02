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
 * Commentaires non lus par l'équipe de modération
 *
 * @author mwsaz
 */
class NouveauxCommentairesAction extends BlogActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, false, false, 1);

		$nbCommentairesParPage = 15;
		$page = !empty($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
		$ListerCommentaires = ListerCommentairesNonValides($page);
		$CompterCommentaires = $this->get('zco_admin.manager')->get('commentairesBlog');
		$ListePages = liste_pages($page, ceil($CompterCommentaires / $nbCommentairesParPage), $CompterCommentaires, $nbCommentairesParPage, 'nouveaux-commentaires-p%s.html');

		if(isset($_POST['commentaires']) && is_array($_POST['commentaires']))
		{
			$commentairesAValider = array();
			foreach($_POST['commentaires'] as &$com)
				if(isset($ListerCommentaires[$com]))
					$commentairesAValider[] = &$ListerCommentaires[$com];

			$billets = array();
			foreach($commentairesAValider as &$com)
			{
				if(!isset($billets[$com['blog_id']]))
					$billets[$com['blog_id']] = 0;
				if($com['commentaire_id'] > $billets[$com['blog_id']])
					$billets[$com['blog_id']] = $com['commentaire_id'];
			}
			if($billets)
				MarquerCommentairesLus2($billets);

			return redirect('Les commentaires sélectionnés ont été marqués comme lus.', 'nouveaux-commentaires.html');
		}

		//Inclusion de la vue
		Page::$titre = 'Nouveaux commentaires';
		fil_ariane('Commentaires non validés');
		$resourceManager = $this->get('zco_vitesse.resource_manager');
		$resourceManager->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css');
		
		return render_to_response(array(
			'ListerCommentaires' => $ListerCommentaires,
			'CompterCommentaires' => $CompterCommentaires,
			'ListePages' => $ListePages,
		));
	}
}
