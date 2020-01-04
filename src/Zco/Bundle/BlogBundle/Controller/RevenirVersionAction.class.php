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

use Symfony\Component\HttpFoundation\Response;
use Zco\Bundle\BlogBundle\Controller\BaseController;

/**
 * Contrôleur gérant le retour à une ancienne version.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class RevenirVersionAction extends BaseController
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true, true);

		//Si on a bien demandé à voir un billet
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$ret = $this->initBillet();
			if ($ret instanceof Response)
				return $ret;

			//Si on a bien le droit d'éditer ce billet et de voir les versions
			if(
				$this->verifier_editer
				&&
				(verifier('blog_voir_versions') || $this->autorise == true)
			)
			{
				if(!empty($_GET['id2']) && is_numeric($_GET['id2']))
				{
					$id_version = $_GET['id2'];
					$InfosVersion = InfosVersion($id_version);

					//Si la version est invalide
					if(($id_version != 0 && empty($InfosVersion)) || (!empty($id_version) && $InfosVersion['version_id_billet'] != $_GET['id']))
					{
						return redirect(0, 'versions-'.$_GET['id'].'.html', MSG_ERROR);
					}

					//Si on veut revenir à une ancienne version
					if(isset($_POST['confirmer']) && $id_version != 0)
					{
						EditerBillet($_GET['id'], array(
							'titre' => $InfosVersion['version_titre'],
							'sous_titre' => $InfosVersion['version_sous_titre'],
							'intro' => $InfosVersion['version_intro'],
							'texte' => $InfosVersion['version_texte'],
						));
						return redirect(206, 'versions-'.$_GET['id'].'.html');
					}
					//Sinon on annule
					elseif(isset($_POST['annuler']))
						return new Symfony\Component\HttpFoundation\RedirectResponse('versions-'.$_GET['id'].'.html');

					//Inclusion de la vue
					fil_ariane($this->InfosBillet['cat_id'], array(
						htmlspecialchars($this->InfosBillet['version_titre']) => 'admin-billet-'.$_GET['id'].'-'.rewrite($this->InfosBillet['version_titre']).'.html',
						'Voir l\'historique des versions' => 'versions-'.$_GET['id'].'.html',
						'Revenir à une ancienne version'
					));
					
					return render_to_response(array(
						'InfosBillet' => $this->InfosBillet,
						'id_version' => $id_version,
					));
				}
				else
					return redirect(475, 'admin-billet-'.$_GET['id'].'.html');
			}
			else
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
		}
		else
			return redirect(20, 'index.html', MSG_ERROR);
	}
}
