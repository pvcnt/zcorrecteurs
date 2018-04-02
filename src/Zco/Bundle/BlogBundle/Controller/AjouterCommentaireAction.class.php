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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contrôleur gérant l'ajout d'un commentaire sur un billet du blog.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */

class AjouterCommentaireAction extends BlogActions
{
	public function execute()
	{
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
		zCorrecteurs::VerifierFormatageUrl(null, true, true);

		//Si on a bien demandé à voir un billet
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			//On récupère des infos sur le billet
			$ret = $this->initBillet();
			if ($ret instanceof Response)
				return $ret;
			Page::$titre .= ' - Ajouter un commentaire';

			//Si on a bien le droit de voir ce billet et de poster un commentaire
			if($this->verifier_voir)
			{
				//Si on a envoyé quelque chose
				if(!empty($_POST['texte']))
				{
					$id = AjouterCommentaire($_GET['id'], $_SESSION['id'], $_POST['texte']);
					
					return redirect('Le commentaire a bien été ajouté.', 'billet-'.$_GET['id'].'-'.$id.'-'.rewrite($this->InfosBillet['version_titre']).'.html');
				}

				//Si on veut citer un message
				if(!empty($_GET['id2']) && is_numeric($_GET['id2']))
				{
					$InfosCommentaire = InfosCommentaire($_GET['id2']);
					$this->texte_zform = '<citation nom="'.htmlspecialchars($InfosCommentaire['utilisateur_pseudo']).'">' .
										$InfosCommentaire['commentaire_texte'].'' .
									'</citation>';
				}
				else
				{
					$this->texte_zform = '';
				}

				//Inclusion de la vue
				fil_ariane($this->InfosBillet['cat_id'], array(
					htmlspecialchars($this->InfosBillet['version_titre']) => 'billet-'.$_GET['id'].'-'.rewrite($this->InfosBillet['version_titre']).'.html',
					'Ajouter un commentaire'
				));
				$this->get('zco_vitesse.resource_manager')->requireResource(
				    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css'
				);
				$this->get('zco_vitesse.resource_manager')->requireResource(
				    '@ZcoForumBundle/Resources/public/css/forum.css'
				);
				
				$this->ListerCommentaires = ListerCommentairesBillet($_GET['id'], -1);
				return render_to_response($this->getVars());
			}
			else
				throw new AccessDeniedHttpException();
		}
		else
			throw new NotFoundHttpException();
	}
}
