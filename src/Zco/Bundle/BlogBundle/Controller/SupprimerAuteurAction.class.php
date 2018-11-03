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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\BlogBundle\Domain\BlogDAO;
use Zco\Bundle\UserBundle\Domain\UserDAO;

/**
 * Contrôleur gérant la suppression d'un auteur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class SupprimerAuteurAction extends BlogActions
{
	public function execute()
	{
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$ret = $this->initBillet();
			if ($ret instanceof Response)
				return $ret;
			Page::$titre .= ' - Supprimer un auteur';

			if(!empty($_GET['id2']) && is_numeric($_GET['id2']))
			{
				if(
					(
						in_array($this->InfosBillet['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE))
						&&
						$this->createur == true
					)
					||
					verifier('blog_toujours_createur', $this->InfosBillet['blog_id_categorie'])
				)
				{
					//On vérifie que ce soit bien un auteur de ce billet
					$this->valide = false;
					foreach($this->Auteurs as $a)
					{
						if($a['utilisateur_id'] == $_GET['id2'])
							$valide = true;
					}
					if($valide == false)
						return redirect(
						    'Cet auteur n\'est pas affecté à ce billet.',
                            'auteurs-'.$_GET['id'].'.html',
                            MSG_ERROR
                        );
					$InfosUtilisateur = UserDAO::InfosUtilisateur($_GET['id2']);
					$this->setRef('InfosUtilisateur', $InfosUtilisateur);

					//Si on veut supprimer l'auteur
					if(isset($_POST['confirmer']))
					{
                        BlogDAO::SupprimerAuteur($_GET['id2'], $_GET['id']);
						return redirect('L\'auteur a bien été supprimé.', 'admin-billet-'.$_GET['id'].'.html');
					}
					//Si on annule
					elseif(isset($_POST['annuler']))
					{
						return new RedirectResponse('admin-billet-'.$_GET['id'].'.html');
					}

					//Inclusion de la vue
					fil_ariane($this->InfosBillet['cat_id'], array(
						htmlspecialchars($this->InfosBillet['version_titre']) => 'billet-'.$_GET['id'].'-'.rewrite($this->InfosBillet['version_titre']).'.html',
						'Supprimer un auteur'
					));
					return render_to_response(array(
						'InfosBillet' => $this->InfosBillet,
						'InfosUtilisateur' => $this->InfosUtilisateur,
					));
				}
				else
					throw new AccessDeniedHttpException();
			}
			else
                throw new NotFoundHttpException();
		}
		else
			throw new NotFoundHttpException();
	}
}
