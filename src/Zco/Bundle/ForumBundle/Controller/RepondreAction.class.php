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
 * Contrôleur gérant la réponse à un sujet.
 *
 * @author Original DJ Fox <marthe59@yahoo.fr>
 */
class RepondreAction extends ForumActions
{
	public function execute()
	{
		//Inclusion des modèles
		include(dirname(__FILE__).'/../modeles/sujets.php');
		include(dirname(__FILE__).'/../modeles/messages.php');

		if (empty($_GET['id']) || !is_numeric($_GET['id']))
		{
			throw new NotFoundHttpException();
		}
		else
		{
			$InfosSujet = InfosSujet($_GET['id']);
			$InfosForum = InfosCategorie($InfosSujet['sujet_forum_id']);
			if (!$InfosSujet)
			{
                throw new NotFoundHttpException();
			}
			if ((!verifier('repondre_sujets', $InfosSujet['sujet_forum_id']) AND !$InfosSujet['sujet_ferme']) OR (!verifier('repondre_sujets_fermes', $InfosSujet['sujet_forum_id']) AND $InfosSujet['sujet_ferme']))
			{
				throw new AccessDeniedHttpException();
			}
			
			// Si le forum est archivé
			if ( $InfosForum['cat_archive'] == 1 ) 
			{
				return redirect('Le forum n\'est plus accessible.', '/forum/', MSG_ERROR);
			}
		}
		
		//Mise à jour de la position sur le site.
		\Doctrine_Core::getTable('Online')->updateUserPosition($_SESSION['id'], 'ZcoForumBundle:repondre');

		zCorrecteurs::VerifierFormatageUrl($InfosSujet['sujet_titre'], true, true);

		if (empty($InfosSujet['dernier_message_auteur']))
		{
			$InfosSujet['dernier_message_auteur'] = $InfosSujet['sujet_auteur'];
			$InfosSujet['dernier_message_date'] = $InfosSujet['sujet_date'];
		}
		
		$InfosSujet['dernier_message_date'] = strtotime($InfosSujet['dernier_message_date']);
		$timestamp_actuel = time();
		if (verifier('anti_up', $InfosSujet['sujet_forum_id']) != 0)
		{
			$secondes = 3600*verifier('anti_up', $InfosSujet['sujet_forum_id']) - ($timestamp_actuel-$InfosSujet['dernier_message_date']);
		}
		else
		{
			$secondes = 0;
		}

		if (!verifier('epargne_anti_up') AND ($secondes > 0) AND $InfosSujet['dernier_message_auteur'] == $_SESSION['id'])
		{
			return redirect(
			    'Vous devez attendre 12 heures pour pouvoir poster deux fois de suite.',
                '/forum/sujet-'.$_GET['id'].'-'.$InfosSujet['sujet_dernier_message'].'-'.rewrite($InfosSujet['sujet_titre']).'.html',
                MSG_ERROR
            );
		}

		Page::$titre = htmlspecialchars($InfosSujet['sujet_titre']).' - Ajout d\'une réponse';

		//--- Si rien n'a été envoyé ---
		if (!isset($_POST['send']) && !isset($_POST['send_reponse_rapide']))
		{
			//La réponse rapide passe par-dessus le reste
			if (isset($_POST['plus_options']))
			{
				$texte_zform = $_POST['texte'];
			}
			//En cas de citation simple
			elseif (!empty($_GET['id2']) AND is_numeric($_GET['id2']))
			{
				$InfosMessage = InfosMessage($_GET['id2']);
				if ($InfosMessage)
				{
					$texte_zform = '<citation rid="'.$_GET['id2'].'">'.$InfosMessage['message_texte'].'</citation>';
				}
			}
			//En cas de citation multiple
			elseif (!empty($_SESSION['forum_citations'][$_GET['id']]))
			{
				$texte_zform = '';
				$i = 0;
				foreach ($_SESSION['forum_citations'][$_GET['id']] as $id_msg)
				{
					$infos = InfosMessage($id_msg);
					$texte_zform .= ($i != 0 ? "\n\n" : '').
						'<citation rid="'.$id_msg.'">'.htmlspecialchars($infos['message_texte']).'</citation>';
					$i++;
				}
				unset($_SESSION['forum_citations'][$_GET['id']]);
			}
			//Si on doit afficher une MAP
			elseif (!empty($InfosForum['cat_map']) && $InfosForum['cat_map_type'] == MAP_ALL)
			{
				!isset($texte_zform) ? $texte_zform = $InfosForum['cat_map'] : $texte_zform .= "\n".$InfosForum['cat_map'];
			}
			else
			{
				$texte_zform = '';
			}

			//On stocke le dernier message du sujet
			$_SESSION['sujet_dernier_message'][$_GET['id']] = $InfosSujet['sujet_dernier_message'];

			//Inclusion de la vue
			fil_ariane($InfosSujet['sujet_forum_id'], array(
				htmlspecialchars($InfosSujet['sujet_titre']) => 'sujet-'.intval($_GET['id']).'-'.rewrite($InfosSujet['sujet_titre']).'.html',
				'Ajout d\'une réponse au sujet'
			));
			$this->get('zco_vitesse.resource_manager')->requireResources(array(
			    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
				'@ZcoForumBundle/Resources/public/js/sujet.js'
			));
			
			return render_to_response(array(
				'InfosSujet' => $InfosSujet,
				'InfosForum' => $InfosForum,
				'tabindex_zform' => 1,
				'antigrilled' => false,
				'texte_zform' => $texte_zform,
				'RevueSujet' => RevueSujet($_GET['id']),
			));
		}
		else
		{
			//On a validé le formulaire. Des vérifications s'imposent.
			if (empty($_POST['texte']))
			{
				return redirect('Vous devez remplir tous les champs nécessaires !', '/forum/', MSG_ERROR);
			}
			elseif (!empty($_SESSION['sujet_dernier_message'][$_GET['id']]) && $_SESSION['sujet_dernier_message'][$_GET['id']] != $InfosSujet['sujet_dernier_message'])
			{
				//On stocke le dernier message du sujet
				$_SESSION['sujet_dernier_message'][$_GET['id']] = $InfosSujet['sujet_dernier_message'];

				//Inclusion de la vue
				fil_ariane($InfosSujet['sujet_forum_id'], 'Ajout d\'une réponse au sujet');
				$this->get('zco_vitesse.resource_manager')->requireResources(array(
				    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
					'@ZcoForumBundle/Resources/public/js/sujet.js'
				));
				
				return render_to_response(array(
					'InfosSujet' => $InfosSujet,
					'InfosForum' => $InfosForum,
					'tabindex_zform' => 1,
					'antigrilled' => true,
					'texte_zform' => $_POST['texte'],
					'RevueSujet' => RevueSujet($_GET['id']),
				));
			}
			else
			{
				unset($_SESSION['sujet_dernier_message'][$_GET['id']]);
				if (!isset($_POST['send_reponse_rapide']))
				{
					if(verifier('epingler_sujets', $InfosSujet['sujet_forum_id']))
						$InfosSujet['sujet_annonce'] = isset($_POST['annonce']);
					if(verifier('fermer_sujets', $InfosSujet['sujet_forum_id']))
						$InfosSujet['sujet_ferme'] = isset($_POST['ferme']);
					if(verifier('resolu_sujets', $InfosSujet['sujet_forum_id'])
					|| ($InfosSujet['sujet_auteur'] == $_SESSION['id']
						&& verifier('resolu_ses_sujets', $InfosSujet['sujet_forum_id'])))
						$InfosSujet['sujet_resolu'] = isset($_POST['resolu']);

					$changer_corbeille = $InfosSujet['sujet_corbeille'];
					if(verifier('corbeille_sujets', $InfosSujet['sujet_forum_id']))
						$changer_corbeille = isset($_POST['corbeille']);
				}
				else
				{
					$changer_corbeille = $InfosSujet['sujet_corbeille'];
				}

				//On envoie le message à la BDD.
				$nouveau_message_id = EnregistrerNouveauMessage($_GET['id'], $InfosSujet['sujet_forum_id'], $InfosSujet['sujet_annonce'], $InfosSujet['sujet_ferme'], $InfosSujet['sujet_resolu'], $InfosSujet['sujet_corbeille'], $InfosSujet['sujet_auteur']);

				//On restaure ou met en corbeille le sujet si besoin
				if ($changer_corbeille != $InfosSujet['sujet_corbeille'])
				{
					if ($changer_corbeille == 1)
					{
						Corbeille($_GET['id'], $InfosSujet['sujet_forum_id']);
					}
					elseif ($changer_corbeille == 0)
					{
						Restaurer($_GET['id'], $InfosSujet['sujet_forum_id']);
					}
				}
				
				return redirect('Le message a bien été ajouté.', 'sujet-'.$_GET['id'].'-'.$nouveau_message_id.'-'.rewrite($InfosSujet['sujet_titre']).'.html');
			}
		}
	}
}
