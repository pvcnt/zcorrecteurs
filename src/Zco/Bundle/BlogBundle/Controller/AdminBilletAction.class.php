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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Contrôleur gérant la page de modification du billet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class AdminBilletAction extends BlogActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);

		//Si on a bien demandé à voir un billet
		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$ret = $this->initBillet();
			if ($ret instanceof Response)
				return $ret;

			if (!$this->verifier_admin_billet)
				throw new AccessDeniedHttpException();

			$this->ListerTags = Doctrine_Core::getTable('Tag')->findAll();
			$this->ListerTagsBillet = ListerTagsBillet($_GET['id']);
			$Tags = array();
			foreach($this->ListerTagsBillet as $tag)
				$Tags[$tag['id']] = mb_strtolower(htmlspecialchars($tag['nom']));
			$this->setRef('Tags', $Tags);
			
			//--- Si on veut modifier tous les tags ---
			if(isset($_POST['tags']) && $this->verifier_editer)
			{
				$TagsExtraits = Doctrine_Core::getTable('Tag')->Extraire($_POST['tags'], false);

				foreach($TagsExtraits as $tag)
				{
					if(!array_key_exists($tag, $Tags))
						AjouterTagBillet($_GET['id'], $tag);
				}
				foreach($Tags as $cle => $tag)
				{
					if(!in_array($cle, $TagsExtraits))
						SupprimerTagBillet($_GET['id'], $cle);
				}
				return redirect(427, 'admin-billet-'.$_GET['id'].'.html');
			}

			//--- Si on veut ajouteur un auteur ---
			if(isset($_POST['ajouter_auteur']) && ($this->createur == true || verifier('blog_toujours_createur')))
			{
				$InfosUtilisateur = InfosUtilisateur($_POST['pseudo']);
				if(!empty($InfosUtilisateur))
				{
					AjouterAuteur($_GET['id'], $InfosUtilisateur['utilisateur_id'], $_POST['statut']);
					return redirect(172, 'admin-billet-'.$_GET['id'].'.html');
				}
				else
				{
					return redirect(123, 'admin-billet-'.$_GET['id'].'.html', MSG_ERROR, -1);
				}
			}

			//--- Si on veut changer de logo ---
			if(!empty($_POST['image']) && $this->verifier_editer)
			{
				$urlimage = AjouterBilletImage($_GET['id'], $_POST['image']);

				if($urlimage[0] === false)
				{
					if($urlimage[1] == 0)
						return redirect(194, '', MSG_ERROR, -1); // pas de fichier à uploader
					elseif($urlimage[1] == 1)
						return redirect(197, '', MSG_ERROR, -1); // extension inconnue
					elseif($urlimage[1] == 2)
						return redirect(198, '', MSG_ERROR, -1); // imagepng fail
					else
						exit('unknown code');
				}

				EditerBillet($_GET['id'], array('image' => $urlimage[1]));
				return redirect(443, 'admin-billet-'.$_GET['id'].'.html');
			}

			//--- Si on veut changer le type de commentaires ---
			if(isset($_POST['commentaires']) && is_numeric($_POST['commentaires']) && verifier('blog_choisir_comms'))
			{
				EditerBillet($_GET['id'], array(
					'commentaires' => $_POST['commentaires'],
					'lien_topic' => $_POST['lien']
				));
				return redirect(445, 'admin-billet-'.$_GET['id'].'.html');
			}

			//--- Si on veut changer l'url de redirection ---
			if(isset($_POST['redirection']) && $this->verifier_editer)
			{
				if(empty($_POST['redirection']) || $_POST['redirection'] == 'http://')
					$_POST['redirection'] = null;

				EditerBillet($_GET['id'], array(
					'url_redirection' => $_POST['redirection']
				));
				return redirect(446, 'admin-billet-'.$_GET['id'].'.html');
			}

			//--- Si on veut changer la date de publication ---
			if(isset($_POST['changer_date']) && verifier('blog_valider'))
			{
				EditerBillet($_GET['id'], array(
					'date_publication' => $_POST['date_pub']
				));
				return redirect(444, 'admin-billet-'.$_GET['id'].'.html');
			}

			//Inclusion de la vue
			fil_ariane($this->InfosBillet['cat_id'], array(
				htmlspecialchars($this->InfosBillet['version_titre']) => 'billet-'.$_GET['id'].'-'.rewrite($this->InfosBillet['version_titre']).'.html',
				'Modification du billet'
			));

			return render_to_response($this->getVars());
		}
		else
			return redirect(20, '/blog/', MSG_ERROR);
	}
}
