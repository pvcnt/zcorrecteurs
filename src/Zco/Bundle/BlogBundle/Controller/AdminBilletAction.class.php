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
use Zco\Bundle\ContentBundle\Domain\TagRepository;

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

			$Tags = array();
			foreach(ListerTagsBillet($_GET['id']) as $tag)
				$Tags[$tag['id']] = mb_strtolower(htmlspecialchars($tag['nom']));
			$this->setRef('Tags', $Tags);

			//--- Si on veut modifier tous les tags ---
			if(isset($_POST['tags']) && $this->verifier_editer)
			{
				$TagsExtraits = TagRepository::instance()->extract($_POST['tags']);

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
				return redirect('Les mots clés ont bien été modifiés.', 'admin-billet-'.$_GET['id'].'.html');
			}

			//--- Si on veut ajouteur un auteur ---
			if(isset($_POST['ajouter_auteur']) && ($this->createur == true || verifier('blog_toujours_createur')))
			{
				$InfosUtilisateur = InfosUtilisateur($_POST['pseudo']);
				if(!empty($InfosUtilisateur))
				{
					AjouterAuteur($_GET['id'], $InfosUtilisateur['utilisateur_id'], $_POST['statut']);
					return redirect('L\'auteur a bien été ajouté.', 'admin-billet-'.$_GET['id'].'.html');
				}
				else
				{
					return redirect('Ce membre n\'existe pas.', 'admin-billet-'.$_GET['id'].'.html', MSG_ERROR, -1);
				}
			}

			//--- Si on veut changer de logo ---
			if(!empty($_POST['image']) && $this->verifier_editer)
			{
				$urlimage = AjouterBilletImage($_GET['id'], $_POST['image']);

				if($urlimage[0] === false)
				{
					if($urlimage[1] == 0)
						return redirect('Erreur : pas de fichier à uploader ?', '', MSG_ERROR, -1); // pas de fichier à uploader
					elseif($urlimage[1] == 1)
						return redirect('Erreur serveur : transfert bloqué à cause de l\'extension.', '', MSG_ERROR, -1); // extension inconnue
					elseif($urlimage[1] == 2)
						return redirect('Erreur serveur : impossible d\'enregistrer l\'image.', '', MSG_ERROR, -1); // imagepng fail
					else
						exit('unknown code');
				}

				EditerBillet($_GET['id'], array('image' => $urlimage[1]));
				return redirect('Le logo de ce billet a bien été changé.', 'admin-billet-'.$_GET['id'].'.html');
			}

			//--- Si on veut changer le type de commentaires ---
			if(isset($_POST['commentaires']) && is_numeric($_POST['commentaires']) && verifier('blog_choisir_comms'))
			{
				EditerBillet($_GET['id'], array(
					'commentaires' => $_POST['commentaires'],
					'lien_topic' => $_POST['lien']
				));
				return redirect('Le nouveau choix de commentaires a bien été mémorisé.', 'admin-billet-'.$_GET['id'].'.html');
			}

			//--- Si on veut changer l'url de redirection ---
			if(isset($_POST['redirection']) && $this->verifier_editer)
			{
				if(empty($_POST['redirection']) || $_POST['redirection'] == 'http://')
					$_POST['redirection'] = null;

				EditerBillet($_GET['id'], array(
					'url_redirection' => $_POST['redirection']
				));
				return redirect('La nouvelle adresse de l\'article virtuel a bien été mémorisée.', 'admin-billet-'.$_GET['id'].'.html');
			}

			//--- Si on veut changer la date de publication ---
			if(isset($_POST['changer_date']) && verifier('blog_valider'))
			{
				EditerBillet($_GET['id'], array(
					'date_publication' => $_POST['date_pub']
				));
				return redirect('La date de publication de ce billet a bien été changée.', 'admin-billet-'.$_GET['id'].'.html');
			}

			//Inclusion de la vue
			fil_ariane($this->InfosBillet['cat_id'], array(
				htmlspecialchars($this->InfosBillet['version_titre']) => 'billet-'.$_GET['id'].'-'.rewrite($this->InfosBillet['version_titre']).'.html',
				'Modification du billet'
			));

			return render_to_response($this->getVars());
		}
		else
			throw new NotFoundHttpException();
	}
}
