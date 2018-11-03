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
        if (empty($_GET['token']) || $_GET['token'] != $_SESSION['token'])
            throw new AccessDeniedHttpException();

        //Inclusion des modèles
        include(__DIR__ . '/../modeles/messages.php');
        include(__DIR__ . '/../modeles/membres.php');

        //Si on n'a pas envoyé de message
        if (empty($_GET['id']) || !is_numeric($_GET['id']))
            throw new NotFoundHttpException();

        $InfosMessage = InfosMessage($_GET['id']);
        if (empty($InfosMessage) || !verifier('voir_sujets', $InfosMessage['sujet_forum_id']))
            throw new NotFoundHttpException();

        if (!$InfosMessage['lunonlu_utilisateur_id'])
            return redirect('Vous n\'avez jamais lu ce sujet.', 'sujet-' . $InfosMessage['sujet_id'] . '-' . rewrite($InfosMessage['sujet_titre']) . '.html', MSG_ERROR);

        $titre = @substr($InfosMessage['message_texte'], 0, strpos($InfosMessage['message_texte'], ' ', 20));

        MarquerDernierMessageLu($_GET['id'], $InfosMessage['sujet_id']);
        return redirect(
            'Le message a bien été marqué comme le dernier lu du sujet',
            'forum-' . $InfosMessage['sujet_forum_id'] . '.html'
        );
    }
}
