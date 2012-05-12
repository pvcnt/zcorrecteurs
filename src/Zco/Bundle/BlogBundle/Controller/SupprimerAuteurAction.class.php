<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Contrôleur gérant la suppression d'un auteur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class SupprimerAuteurAction extends BlogActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true, true);

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
						return redirect(175, 'auteurs-'.$_GET['id'].'.html', MSG_ERROR);
					$InfosUtilisateur = InfosUtilisateur($_GET['id2']);
					$this->setRef('InfosUtilisateur', $InfosUtilisateur);

					//Si on veut supprimer l'auteur
					if(isset($_POST['confirmer']))
					{
						SupprimerAuteur($_GET['id2'], $_GET['id']);
						return redirect(174, 'admin-billet-'.$_GET['id'].'.html');
					}
					//Si on annule
					elseif(isset($_POST['annuler']))
					{
						return new Symfony\Component\HttpFoundation\RedirectResponse('admin-billet-'.$_GET['id'].'.html');
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
					throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
			}
			else
				return redirect(123, 'admin-billet-'.$_GET['id'].'.html', MSG_ERROR);
		}
		else
			return redirect(20, 'index.html', MSG_ERROR);
	}
}
