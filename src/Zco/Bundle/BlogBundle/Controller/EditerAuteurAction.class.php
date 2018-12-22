<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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
use \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\BlogBundle\Domain\BlogDAO;
use Zco\Bundle\UserBundle\Domain\UserDAO;

/**
 * Contrôleur gérant l'édition d'un auteur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EditerAuteurAction extends BlogActions
{
	public function execute()
	{
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$ret = $this->initBillet();
			if ($ret instanceof Response)
				return $ret;
			Page::$titre .= ' - Modifier un auteur';

			if(!empty($_GET['id2']) && is_numeric($_GET['id2']))
			{
				if(
					in_array($this->InfosBillet['blog_etat'], array(BLOG_BROUILLON, BLOG_REFUSE))
					||
					verifier('blog_toujours_createur', $this->InfosBillet['blog_id_categorie'])
				)
				{
					if($this->autorise == false)
						return redirect('Cet auteur n\'est pas affecté à ce billet.', 'admin-billet-'.$_GET['id'].'.html', MSG_ERROR);

					//Si on veut éditer l'auteur
					if(!empty($_POST['pseudo']) && !empty($_POST['statut']) && is_numeric($_POST['statut']))
					{
						$InfosUtilisateur = UserDAO::InfosUtilisateur($_POST['pseudo']);
						if(!empty($InfosUtilisateur))
						{
                            BlogDAO::EditerAuteur($_GET['id2'], $_GET['id'], $InfosUtilisateur['utilisateur_id'], $_POST['statut']);
							return redirect('L\'auteur a bien été modifié.', 'admin-billet-'.$_GET['id'].'.html');
						}
						else
						{
							return redirect('Ce membre n\'existe pas.', 'admin-billet-'.$_GET['id'].'.html', MSG_ERROR);
						}
					}

					$InfosUtilisateur = UserDAO::InfosUtilisateur($_GET['id2']);
					foreach($this->Auteurs as $a)
					{
						if($a['utilisateur_id'] == $_GET['id2'])
							$InfosUtilisateur['auteur_statut'] = $a['auteur_statut'];
					}
					$this->setRef('InfosUtilisateur', $InfosUtilisateur);

					//Inclusion de la vue
					fil_ariane($this->InfosBillet['cat_id'], array(
						htmlspecialchars($this->InfosBillet['version_titre']) => 'admin-billet-'.$_GET['id'].'-'.rewrite($this->InfosBillet['version_titre']).'.html',
						'Modifier un auteur'
					));
					
					return render_to_response('ZcoBlogBundle::editerAuteur.html.php', $this->getVars());
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
