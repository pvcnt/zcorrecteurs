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
 * Contrôleur chargé du changement du statut ayant aidé ou non d'une
 * réponse à un sujet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ReponseHelpAction extends ForumActions
{
    public function execute()
    {
        include(__DIR__ . '/../modeles/moderation.php');
        include(__DIR__ . '/../modeles/messages.php');
        list($InfosSujet, $InfosForum) = $this->initSujet();

        //Vérification du token.
        if (empty($_GET['token']) || $_GET['token'] != $_SESSION['token'])
            throw new AccessDeniedHttpException;

        if (($_SESSION['id'] == $InfosSujet['sujet_auteur'] &&
                verifier('indiquer_ses_messages_aide', $InfosSujet['sujet_forum_id'])
            ) ||
            verifier('indiquer_messages_aide', $InfosSujet['sujet_forum_id'])
        ) {
            if (empty($_GET['id2']) || !is_numeric($_GET['id2']))
                throw new NotFoundHttpException();
            if (!VerifierValiditeMessage($_GET['id2']))
                throw new NotFoundHttpException();
            ChangerHelp($_GET['id2'], $_GET['help_souhaite']);

            return redirect(
                ($_GET['help_souhaite'] ? 'Le message a bien été marqué comme vous ayant aidé(e).' : 'Le message a bien été marqué comme ne vous ayant pas aidé(e).'),
                'sujet-' . $_GET['id'] . '-' . $_GET['id2'] . '-' . rewrite($InfosSujet['sujet_titre']) . '.html'
            );
        } else {
            throw new AccessDeniedHttpException();
        }
    }
}
