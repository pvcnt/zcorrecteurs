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
use Zco\Bundle\BlogBundle\Domain\BlogDAO;

/**
 * Contrôleur gérant l'édition d'un billet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EditerAction extends BlogActions
{
	public function execute()
	{
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			//On récupère des infos sur le billet
			$ret = $this->initBillet();
			if ($ret instanceof Response)
				return $ret;
			Page::$titre .= ' - Modifier le billet';

			if($this->verifier_editer)
			{
				//Si on a édité le billet
				if(isset($_POST['submit']))
				{
					if(empty($_POST['titre']) || empty($_POST['intro']) || empty($_POST['texte']))
						return redirect('Vous devez remplir tous les champs nécessaires !', 'editer-'.$_GET['id'].'.html', MSG_ERROR);

                    BlogDAO::EditerBillet($_GET['id'], array(
						'titre' => $_POST['titre'],
						'sous_titre' => $_POST['sous_titre'],
						'intro' => $_POST['intro'],
						'texte' => $_POST['texte'],
						'id_categorie' => $_POST['categorie'],
						'lien_nom' => $_POST['lien_nom'],
						'lien_url' => $_POST['lien_url'],
						'commentaire' => $_POST['commentaire'],
					));

					return redirect('Le billet a bien été édité.', 'admin-billet-'.$_GET['id'].'.html');
				}

				$this->Categories = ListerEnfants(GetIDCategorieCourante());

				//Inclusion de la vue
				fil_ariane($this->InfosBillet['cat_id'], array(
					htmlspecialchars($this->InfosBillet['version_titre']) => 'admin-billet-'.$_GET['id'].'-'.rewrite($this->InfosBillet['version_titre']).'.html',
					'Modifier le billet'));
				$this->tabindex_zform = 5;
				
				return render_to_response($this->getVars());
			}
			else
			{
				throw new AccessDeniedHttpException();
			}
		}
		else
		{
			throw new NotFoundHttpException();
		}
	}
}
