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

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\ContentBundle\Domain\CategoryDAO;
use Zco\Bundle\ForumBundle\Domain\MessageDAO;
use Zco\Bundle\ForumBundle\Domain\TopicDAO;

/**
 * Contrôleur gérant la réponse à un sujet.
 *
 * @author Original DJ Fox <marthe59@yahoo.fr>
 */
class RepondreAction extends ForumActions
{
	public function execute()
	{
		if (empty($_GET['id']) || !is_numeric($_GET['id']))
		{
			throw new NotFoundHttpException();
		}
		else
		{
			$InfosSujet = TopicDAO::InfosSujet($_GET['id']);
			$InfosForum = CategoryDAO::InfosCategorie($InfosSujet['sujet_forum_id']);
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

		if (empty($InfosSujet['dernier_message_auteur']))
		{
			$InfosSujet['dernier_message_auteur'] = $InfosSujet['sujet_auteur'];
			$InfosSujet['dernier_message_date'] = $InfosSujet['sujet_date'];
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
				$InfosMessage = MessageDAO::InfosMessage($_GET['id2']);
				if ($InfosMessage)
				{
					$texte_zform = '<citation rid="'.$_GET['id2'].'">'.$InfosMessage['message_texte'].'</citation>';
				}
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
			$this->get('zco_core.resource_manager')->requireResources(array(
			    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
			));

			return render_to_response('ZcoForumBundle::repondre.html.php', array(
				'InfosSujet' => $InfosSujet,
				'InfosForum' => $InfosForum,
				'tabindex_zform' => 1,
				'antigrilled' => false,
				'texte_zform' => $texte_zform,
				'RevueSujet' => TopicDAO::RevueSujet($_GET['id']),
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
				$this->get('zco_core.resource_manager')->requireResources(array(
				    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
				));

				return render_to_response('ZcoForumBundle::repondre.html.php', array(
					'InfosSujet' => $InfosSujet,
					'InfosForum' => $InfosForum,
					'tabindex_zform' => 1,
					'antigrilled' => true,
					'texte_zform' => $_POST['texte'],
					'RevueSujet' => TopicDAO::RevueSujet($_GET['id']),
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
				$nouveau_message_id = MessageDAO::EnregistrerNouveauMessage($_GET['id'], $InfosSujet['sujet_forum_id'], $InfosSujet['sujet_annonce'], $InfosSujet['sujet_ferme'], $InfosSujet['sujet_resolu'], $InfosSujet['sujet_corbeille'], $InfosSujet['sujet_auteur']);

				//On restaure ou met en corbeille le sujet si besoin
				if ($changer_corbeille != $InfosSujet['sujet_corbeille'])
				{
					if ($changer_corbeille == 1)
					{
                        TopicDAO::Corbeille($_GET['id'], $InfosSujet['sujet_forum_id']);
					}
					elseif ($changer_corbeille == 0)
					{
                        TopicDAO::Restaurer($_GET['id'], $InfosSujet['sujet_forum_id']);
					}
				}

				return redirect('Le message a bien été ajouté.', 'sujet-'.$_GET['id'].'-'.$nouveau_message_id.'-'.rewrite($InfosSujet['sujet_titre']).'.html');
			}
		}
	}
}
