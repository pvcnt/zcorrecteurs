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
 * Contrôleur pour l'édition d'un message.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class EditerAction extends ForumActions
{
	public function execute()
	{
		//Inclusion du modèle
		include(__DIR__.'/../modeles/messages.php');
		include(__DIR__.'/../modeles/sujets.php');

		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosMessage = InfosMessage($_GET['id']);
			if (!$InfosMessage)
			{
				throw new NotFoundHttpException();
			}
			if
			(
				!(
					(
						verifier('editer_ses_messages', $InfosMessage['sujet_forum_id']) AND $InfosMessage['message_auteur'] == $_SESSION['id']
					)
					OR verifier('editer_messages_autres', $InfosMessage['sujet_forum_id'])
				)
			)
			{
				throw new AccessDeniedHttpException();
			}
			
			$InfosForum = InfosCategorie($InfosMessage['sujet_forum_id']);
			$InfosSujet = InfosSujet($InfosMessage['sujet_id']);

			Page::$titre .= ' - Modifier un message';
			
			//Si on n'a rien posté
			if (empty($_POST['send']) || $_POST['send'] != 'Envoyer')
			{

				//Inclusion de la vue
				fil_ariane($InfosMessage['sujet_forum_id'], array(
					htmlspecialchars($InfosMessage['sujet_titre']) => 'sujet-'.$InfosMessage['sujet_id'].'-'.rewrite($InfosMessage['sujet_titre']).'.html',
					'Modifier un message'
				));
				$this->get('zco_core.resource_manager')->requireResources(array(
				    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
				));
				
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
				if (empty($_POST['texte']))
				{
					return redirect(
					    'Vous devez remplir tous les champs nécessaires !',
                        'sujet-'.$InfosMessage['message_sujet_id'].'-'.rewrite($InfosMessage['sujet_titre']).'.html',
                        MSG_ERROR
                    );
				}
				else
				{
					$InfosMessage['sujet_annonce'] = isset($_POST['annonce']) ? 1 : 0;
					$InfosMessage['sujet_ferme'] = isset($_POST['ferme']) ? 1 : 0;
					$InfosMessage['sujet_resolu'] = isset($_POST['resolu']) ? 1 : 0;
					EditerMessage($_GET['id'], $InfosMessage['sujet_forum_id'], $InfosMessage['message_sujet_id'], $InfosMessage['sujet_annonce'], $InfosMessage['sujet_ferme'], $InfosMessage['sujet_resolu'], $InfosMessage['sujet_auteur']);

					return redirect(
					    'Le message a bien été édité.',
                        'sujet-'.$InfosMessage['message_sujet_id'].'-'.$_GET['id'].'-'.rewrite($InfosMessage['sujet_titre']).'.html'
                    );
				}
			}
		}
		else
            throw new NotFoundHttpException();
	}
}
