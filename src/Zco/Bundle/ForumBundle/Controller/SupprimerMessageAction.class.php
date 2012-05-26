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
		include(dirname(__FILE__).'/../modeles/messages.php');
		include(dirname(__FILE__).'/../modeles/moderation.php');

		//Si on n'a pas envoyé de message
		if(empty($_GET['id']) || !is_numeric($_GET['id']))
			return redirect(44, '/forum/', MSG_ERROR);

		$InfosMessage = InfosMessage($_GET['id']);
		if(empty($InfosMessage) || !verifier('voir_sujets', $InfosMessage['sujet_forum_id']))
			return redirect(46, '/forum/', MSG_ERROR);

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
			zCorrecteurs::VerifierFormatageUrl($titre, true);

			//Si on confirme la suppression
			if(isset($_POST['confirmer']))
			{
				SupprimerMessage($_GET['id'], $InfosMessage['sujet_id'], $InfosMessage['sujet_dernier_message'],  $InfosMessage['sujet_forum_id'], $InfosMessage['sujet_corbeille']);
				return redirect(36, 'sujet-'.$InfosMessage['sujet_id'].'-'.rewrite($InfosMessage['sujet_titre']).'.html');
			}
			//Si on annule
			elseif(isset($_POST['annuler']))
			{
				return new Symfony\Component\HttpFoundation\RedirectResponse('sujet-'.$InfosMessage['sujet_id'].'-'.$_GET['id'].'-'.rewrite($InfosMessage['sujet_titre']).'.html');
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
				return redirect(74, 'sujet-'.$_GET['id'].'-'.rewrite($InfosMessage['sujet_titre']).'.html', MSG_ERROR);
		}
		else
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
	}
}
