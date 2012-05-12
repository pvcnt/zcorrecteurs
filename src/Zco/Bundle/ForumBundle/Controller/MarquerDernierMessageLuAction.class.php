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
 * Contrôleur pour le marquage du dernier message lu d'un sujet
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class MarquerDernierMessageLuAction extends ForumActions
{
	public function execute()
	{
		//Vérification du token.
		if(empty($_GET['token']) || $_GET['token'] != $_SESSION['token'])
			throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

		//Inclusion des modèles
		include(dirname(__FILE__).'/../modeles/messages.php');
		include(dirname(__FILE__).'/../modeles/membres.php');

		//Si on n'a pas envoyé de message
		if(empty($_GET['id']) || !is_numeric($_GET['id']))
			return redirect(44, '/forum/', MSG_ERROR);

		$InfosMessage = InfosMessage($_GET['id']);
		if(empty($InfosMessage) || !verifier('voir_sujets', $InfosMessage['sujet_forum_id']))
			return redirect(46, '/forum/', MSG_ERROR);

                if(!$InfosMessage['lunonlu_utilisateur_id'])
                        return redirect(354, 'sujet-'.$InfosMessage['sujet_id'].'-'.rewrite($InfosMessage['sujet_titre']).'.html', MSG_ERROR);

                $titre = @substr($InfosMessage['message_texte'], 0, strpos($InfosMessage['message_texte'], ' ', 20));
                zCorrecteurs::VerifierFormatageUrl($titre, true);

                MarquerDernierMessageLu($_GET['id'], $InfosMessage['sujet_id']);
                return redirect(355, 'forum-'.$InfosMessage['sujet_forum_id'].'.html');
        }
}
