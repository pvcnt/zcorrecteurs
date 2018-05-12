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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contrôleur gérant la création d'un nouveau sujet.
 *
 * @author Original DJ Fox <marthe59@yahoo.fr>
 */
class NouveauAction extends ForumActions
{
	public function execute()
	{
		//Inclusion des modèles
		include(dirname(__FILE__).'/../modeles/sujets.php');
		include(dirname(__FILE__).'/../modeles/forums.php');
		include(dirname(__FILE__).'/../modeles/sondages.php');

		if (empty($_GET['id']) || !is_numeric($_GET['id']))
		{
            throw new NotFoundHttpException();
		}
		else
		{
			$InfosForum = InfosCategorie($_GET['id']);
			if (!$InfosForum)
			{
                throw new NotFoundHttpException();
			}
			if (!verifier('creer_sujets', $_GET['id']))
			{
				throw new AccessDeniedHttpException();
			}
			if (!empty($_GET['trash']) AND !verifier('corbeille_sujets', $_GET['id']))
			{
				throw new AccessDeniedHttpException();
			}
			if ( $InfosForum['cat_archive'] == 1 )
			{
				return redirect('Le forum n\'est plus accessible.', '/forum/', MSG_ERROR);
			}
		}

		Page::$titre = htmlspecialchars($InfosForum['cat_nom']).' - Nouveau sujet';
		
		//Mise à jour de la position sur le site.
		\Doctrine_Core::getTable('Online')->updateUserPosition($_SESSION['id'], 'ZcoForumBundle:nouveau');

		if (empty($_POST['send']) || $_POST['send'] != 'Envoyer')
		{

			//Inclusion de la vue
			fil_ariane($_GET['id'], 'Créer un nouveau sujet');
			$this->get('zco_vitesse.resource_manager')->requireResource(
			    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css'
			);

			if (isset($_SESSION['forum_message_texte']))
			{
				$texte = $_SESSION['forum_message_texte'];
				unset($_SESSION['forum_message_texte']);
			}
			else
			{
				$texte = $InfosForum['cat_map'];
			}

			return render_to_response(array(
				'InfosForum' => $InfosForum,
				'tabindex_zform' => 4,
				'texte_zform' => $texte,
			));
		}
		
		//On a validé le formulaire. Des vérifications s'imposent.
		if(empty($_POST['titre']) || empty($_POST['texte']))
		{
			$_SESSION['forum_message_texte'] = $_POST['texte'];
			return redirect('Vous devez remplir tous les champs nécessaires !', $_SERVER['REQUEST_URI'], MSG_ERROR);
		}
		else
		{
			$nouveau_sondage_id = 0;

			$annonce = 0;
			$ferme = 0;
			$corbeille = 0;
			$resolu = 0;
			if (isset($_POST['annonce']) AND verifier('epingler_sujets', $_GET['id']))
			{
				$annonce = 1;
			}
			if (isset($_POST['ferme']) AND verifier('fermer_sujets', $_GET['id']))
			{
				$ferme = 1;
			}
			if (isset($_POST['resolu']) AND verifier('resolu_sujets', $_GET['id']))
			{
				$resolu = 1;
			}
			if (isset($_POST['corbeille']) AND verifier('corbeille_sujets', $_GET['id']))
			{
				$corbeille = 1;
			}

			//On envoie le sujet à la BDD.
			$nouveau_sujet_id = EnregistrerNouveauSujet($_GET['id'], $nouveau_sondage_id, $annonce, $ferme, $resolu, $corbeille);

			// Sondage ?
			if (verifier('poster_sondage', $_GET['id']) &&
				!empty($_POST['sondage_question']))
			{
				// Nettoyage des réponses
				$reponses = isset($_POST['reponses']) ? $_POST['reponses'] : array();
				foreach ($reponses as $k => &$v)
				{
					$v = trim($v);
					if ($v === '')
					{
						unset($reponses[$k]);
					}
				}

				// Au moins deux réponses
				if (count($reponses) >= 2)
				{
					CreerSondageSujet($nouveau_sujet_id, $reponses);
				}
			}

			return redirect(
			    'Le sujet a bien été créé.',
                'sujet-'.$nouveau_sujet_id.'-'.rewrite($_POST['titre']).'.html'
            );
		}
	}
}
