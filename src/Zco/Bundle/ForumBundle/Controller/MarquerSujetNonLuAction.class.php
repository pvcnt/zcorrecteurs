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

/**
 * Contrôleur pour le marquage d'un sujet ponctuel comme étant non-lu
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class MarquerSujetNonLuAction extends ForumActions
{
    public function execute()
    {
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }

        //Vérification du token.
        if (empty($_GET['token']) || $_GET['token'] != $_SESSION['token'])
            throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

        //Inclusion des modèles
        include(dirname(__FILE__) . '/../modeles/sujets.php');
        include(dirname(__FILE__) . '/../modeles/membres.php');

        //Si on n'a pas envoyé de sujet
        if (empty($_GET['id']) || !is_numeric($_GET['id']))
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();

        $InfosSujet = InfosSujet($_GET['id']);
        if (empty($InfosSujet) || !verifier('voir_sujets', $InfosSujet['sujet_forum_id']))
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();

        if (!$InfosSujet['lunonlu_utilisateur_id'])
            return redirect(
                'Vous n\'avez jamais lu ce sujet.',
                'forum-' . $InfosSujet['sujet_forum_id'] . '.html',
                MSG_ERROR
            );

        MarquerSujetLu($_GET['id'], false);
        return redirect(
            'Le sujet a bien été marqué comme non-lu.',
            'forum-' . $InfosSujet['sujet_forum_id'] . '.html'
        );
    }
}
