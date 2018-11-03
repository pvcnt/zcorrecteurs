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

/**
 * Contrôleur gérant la division d'un sujet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DiviserAction extends ForumActions
{
    public function execute()
    {
        //Inclusion des modèles
        include(__DIR__ . '/../modeles/sujets.php');
        include(__DIR__ . '/../modeles/forums.php');
        include(__DIR__ . '/../modeles/moderation.php');
        include(__DIR__ . '/../modeles/categories.php');

        if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
            throw new NotFoundHttpException();
        }
        $InfosSujet = InfosSujet($_GET['id']);
        if (!$InfosSujet) {
            throw new NotFoundHttpException();
        }

        Page::$titre = $InfosSujet['sujet_titre'] . ' - Diviser le sujet';

        if (verifier('diviser_sujets', $InfosSujet['sujet_forum_id'])) {
            //Si on veut diviser le sujet
            if (isset($_POST['submit'])) {
                //Si des champs sont vides
                if (empty($_POST['titre']) || empty($_POST['msg']) || empty($_POST['forum']) || !is_numeric($_POST['forum']))
                    return redirect('Vous devez remplir tous les champs nécessaires !', 'diviser-' . $_GET['id'] . '.html', MSG_ERROR);

                //Si le forum n'existe pas
                $InfosForum = CategoryDAO::InfosCategorie($_POST['forum']);
                if (empty($InfosForum) || !verifier('voir_sujets', $InfosForum['cat_id']))
                    throw new NotFoundHttpException();

                //Si tout va bien on divise
                DiviserSujet($InfosSujet, $InfosSujet['sujet_corbeille']);
                return redirect('Le sujet a bien été divisé.', 'sujet-' . $_GET['id'] . '-' . rewrite($InfosSujet['sujet_titre']) . '.html');
            }

            $ListerCategories = ListerCategoriesForum();
            $ListerMessages = ListerMessages($_GET['id'], 0, $InfosSujet['nombre_de_messages']);

            if (count($ListerMessages) < 2)
                return redirect(
                    'Vous ne pouvez pas diviser ce sujet : il contient moins de deux messages.',
                    'sujet-' . $_GET['id'] . '-' . rewrite($InfosSujet['sujet_titre']) . '.html',
                    MSG_ERROR
                );

            //Inclusion de la vue
            fil_ariane($InfosSujet['sujet_forum_id'], array(
                htmlspecialchars($InfosSujet['sujet_titre']) => 'sujet-' . $_GET['id'] . '-' . rewrite($InfosSujet['sujet_titre']) . '.html',
                'Diviser le sujet'
            ));

            return render_to_response(array(
                'ListerCategories' => $ListerCategories,
                'ListerMessages' => $ListerMessages,
                'InfosSujet' => $InfosSujet,
            ));

        } else
            throw new AccessDeniedHttpException();
    }
}
