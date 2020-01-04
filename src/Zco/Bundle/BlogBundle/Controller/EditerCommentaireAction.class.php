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
 * Contrôleur gérant l'édition d'un commentaire sur un billet du blog.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EditerCommentaireAction extends BaseController
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);

		//Si on a bien demandé à voir un commentaire
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			//On récupère des infos sur le commentaire
			$InfosCommentaire = InfosCommentaire($_GET['id']);
			$ListerCommentaires = ListerCommentairesBillet($InfosCommentaire['blog_id'], -1);

			Page::$titre = htmlspecialchars($InfosCommentaire['version_titre']).' - Modifier un commentaire';

			//Si on a bien le droit d'éditer le commentaire
			if(
				(
					($InfosCommentaire['utilisateur_id'] == $_SESSION['id'] && verifier('blog_editer_ses_commentaires', $InfosCommentaire['blog_id_categorie']))
					||
					verifier('blog_editer_commentaires', $InfosCommentaire['blog_id_categorie'])
				)
				 &&
				 ($InfosCommentaire['blog_commentaires'] == COMMENTAIRES_OK || verifier('blog_poster_commentaires_fermes', $InfosBillet['blog_id_categorie']))
			)
			{
				//Si on a envoyé quelque chose
				if(!empty($_POST['submit']))
				{
					EditerCommentaire($_GET['id'], $_SESSION['id'], $_POST['texte']);
					
					return redirect(136, 'billet-'.$InfosCommentaire['blog_id'].'-'.$_GET['id'].'-'.rewrite($InfosCommentaire['version_titre']).'.html#commentaires');
				}

				//Inclusion de la vue
				fil_ariane($InfosCommentaire['blog_id_categorie'], array(
					htmlspecialchars($InfosCommentaire['version_titre']) => 'billet-'.$InfosCommentaire['blog_id'].'-'.rewrite($InfosCommentaire['version_titre']).'.html',
					'Modifier un commentaire'
				));
				$this->get('zco_vitesse.resource_manager')->requireResources(array(
				    '@ZcoForumBundle/Resources/public/css/forum.css',
				    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
				));
				
				return render_to_response(compact(
					'InfosCommentaire',
					'InfosBillet',
					'ListerCommentaires'
				));
			}
			else
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}
		else
			return redirect(138, 'index.html', MSG_ERROR);
	}
}
