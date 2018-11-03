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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contrôleur pour la suppression d'un message.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class SupprimerMessageAction extends ForumActions
{
	public function execute()
	{
		//Inclusion des modèles
		include(__DIR__.'/../modeles/messages.php');
		include(__DIR__.'/../modeles/moderation.php');

		//Si on n'a pas envoyé de message
		if(empty($_GET['id']) || !is_numeric($_GET['id']))
			throw new NotFoundHttpException();

		$InfosMessage = InfosMessage($_GET['id']);
		if(empty($InfosMessage) || !verifier('voir_sujets', $InfosMessage['sujet_forum_id']))
            throw new NotFoundHttpException();

		//Si on a le droit de supprimer ce message
		if(
			(
				verifier('suppr_messages', $InfosMessage['sujet_forum_id'])
				|| (verifier('suppr_ses_messages', $InfosMessage['sujet_forum_id']) && $InfosMessage['message_auteur'] == $_SESSION['id'])
			)
			&& !$InfosMessage['sujet_corbeille']
			&&
			(
				!$InfosMessage['sujet_ferme']
				|| verifier('repondre_sujets_fermes', $InfosMessage['sujet_forum_id'])
			)
		)
		{
			$titre = @substr($InfosMessage['message_texte'], 0, strpos($InfosMessage['message_texte'], ' ', 20));

			//Si on confirme la suppression
			if(isset($_POST['confirmer']))
			{
				SupprimerMessage($_GET['id'], $InfosMessage['sujet_id'], $InfosMessage['sujet_dernier_message'],  $InfosMessage['sujet_forum_id'], $InfosMessage['sujet_corbeille']);
				return redirect(
				    'Le message a bien été supprimé.',
                    'sujet-'.$InfosMessage['sujet_id'].'-'.rewrite($InfosMessage['sujet_titre']).'.html'
                );
			}
			//Si on annule
			elseif(isset($_POST['annuler']))
			{
				return new RedirectResponse('sujet-'.$InfosMessage['sujet_id'].'-'.$_GET['id'].'-'.rewrite($InfosMessage['sujet_titre']).'.html');
			}

			//Si le message n'est pas le premier message
			if($_GET['id'] != $InfosMessage['sujet_premier_message'])
			{
				fil_ariane($InfosMessage['sujet_forum_id'], array(
					htmlspecialchars($InfosMessage['sujet_titre']) => 'sujet-'.$_GET['id'].'-'.rewrite($InfosMessage['sujet_titre']).'.html',
					'Supprimer un message du sujet'
				));
				return render_to_response(array('InfosMessage' => $InfosMessage));
			}
			else
				return redirect(
				    'La suppression du message a échoué : on ne peut pas supprimer le premier message du sujet.',
                    'sujet-'.$_GET['id'].'-'.rewrite($InfosMessage['sujet_titre']).'.html',
                    MSG_ERROR
                );
		}
		else
			throw new AccessDeniedHttpException();
	}
}
