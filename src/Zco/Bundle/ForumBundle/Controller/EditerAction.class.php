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
 * Contrôleur pour l'édition d'un message.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class EditerAction extends ForumActions
{
	public function execute()
	{
		//Inclusion du modèle
		include(dirname(__FILE__).'/../modeles/messages.php');
		include(dirname(__FILE__).'/../modeles/sujets.php');

		$this->get('zco_vitesse.resource_manager')->requireResource('@ZcoForumBundle/Resources/public/js/sujet.js');

		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosMessage = InfosMessage($_GET['id']);

			if(!$InfosMessage)
			{
				return redirect(46, '/forum/', MSG_ERROR);
			}
			elseif
			(
				!(
					(
						verifier('editer_ses_messages', $InfosMessage['sujet_forum_id']) AND $InfosMessage['message_auteur'] == $_SESSION['id']
					)
					OR verifier('editer_messages_autres', $InfosMessage['sujet_forum_id'])
				)
			)
			{
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
			}

			$InfosForum = InfosCategorie($InfosMessage['sujet_forum_id']);
			$InfosSujet = InfosSujet($InfosMessage['sujet_id']);

			zCorrecteurs::VerifierFormatageUrl($InfosMessage['sujet_titre'], true);
			Page::$titre .= ' - Modifier un message';
			/*$ListerTags = ListerTagsSujet($InfosMessage['message_sujet_id']);
			$Tags = array();
			foreach($ListerTags as $tag)
				$Tags[$tag['tag_id']] = mb_strtolower(htmlspecialchars($tag['tag_nom']));*/

			//Si on n'a rien posté
			if(empty($_POST['send']) || $_POST['send'] != 'Envoyer')
			{

				//Inclusion de la vue
				fil_ariane($InfosMessage['sujet_forum_id'], array(
					htmlspecialchars($InfosMessage['sujet_titre']) => 'sujet-'.$InfosMessage['sujet_id'].'-'.rewrite($InfosMessage['sujet_titre']).'.html',
					'Modifier un message'
				));
				$this->get('zco_vitesse.resource_manager')->requireResource(
				    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css'
				);
				
				return render_to_response(array(
					'tabindex_zform' => 1,
					'sujet_titre' => $InfosMessage['sujet_titre'],
					'sujet_id' => $InfosMessage['message_sujet_id'],
					'RevueSujet' => RevueSujet($InfosMessage['message_sujet_id']),
					'InfosMessage' => $InfosMessage,
					'InfosForum' => $InfosForum,
					'InfosSujet' => $InfosSujet
				));
			}

			//Si on a posté quelque chose
			else
			{
				//On a validé le formulaire. Des vérifications s'imposent.
				if(empty($_POST['texte']))
				{
					return redirect(17, 'sujet-'.$InfosMessage['message_sujet_id'].'-'.rewrite($InfosMessage['sujet_titre']).'.html', MSG_ERROR);
				}
				else
				{
					if(isset($_POST['annonce']))
					{
						$InfosMessage['sujet_annonce'] = 1;
					}
					else
					{
						$InfosMessage['sujet_annonce'] = 0;
					}
					if(isset($_POST['ferme']))
					{
						$InfosMessage['sujet_ferme'] = 1;
					}
					else
					{
						$InfosMessage['sujet_ferme'] = 0;
					}
					if(isset($_POST['resolu']))
					{
						$InfosMessage['sujet_resolu'] = 1;
					}
					else
					{
						$InfosMessage['sujet_resolu'] = 0;
					}

					//On envoie le message à la BDD.
					EditerMessage($_GET['id'], $InfosMessage['sujet_forum_id'], $InfosMessage['message_sujet_id'], $InfosMessage['sujet_annonce'], $InfosMessage['sujet_ferme'], $InfosMessage['sujet_resolu'], $InfosMessage['sujet_auteur']);

					/*if(verifier('editer_sujets', $InfosMessage['sujet_forum_id']) || (verifier('editer_ses_sujets', $InfosMessage['sujet_forum_id']) && $_SESSION['id'] == $InfosMessage['message_auteur']))
					{
						$TagsExtraits = ExtraireTags($_POST['tags']);
						foreach($TagsExtraits as $tag)
						{
							if(!array_key_exists(mb_strtolower($tag), $Tags))
								AjouterTagSujet($InfosMessage['message_sujet_id'], $tag);
						}
					}*/
					return redirect(35, 'sujet-'.$InfosMessage['message_sujet_id'].'-'.$_GET['id'].'-'.rewrite($InfosMessage['sujet_titre']).'.html');
				}
			}
		}
		else
			return redirect(44, 'index.html', MSG_ERROR);
	}
}
