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

/**
 * Contrôleur gérant l'édition d'un billet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EditerAction extends BlogActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);

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
						return redirect(17, 'editer-'.$_GET['id'].'.html', MSG_ERROR, -1);

					EditerBillet($_GET['id'], array(
						'titre' => $_POST['titre'],
						'sous_titre' => $_POST['sous_titre'],
						'intro' => $_POST['intro'],
						'texte' => $_POST['texte'],
						'id_categorie' => $_POST['categorie'],
						'lien_nom' => $_POST['lien_nom'],
						'lien_url' => $_POST['lien_url'],
						'commentaire' => $_POST['commentaire'],
					));

					return redirect(8, 'admin-billet-'.$_GET['id'].'.html');
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
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
			}
		}
		else
		{
			return redirect(20, '/blog/', MSG_ERROR);
		}
	}
}
