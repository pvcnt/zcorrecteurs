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
use Zco\Bundle\ForumBundle\Domain\TopicDAO;

/**
 * Contrôleur gérant la division d'un sujet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class FusionnerAction extends ForumActions
{
    public function execute()
    {
        //Inclusion des modèles
        include(__DIR__ . '/../modeles/forums.php');

        !isset($_POST['titre']) && $_POST['titre'] = null;
        if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
            throw new NotFoundHttpException();
        }
        $InfosSujet = TopicDAO::InfosSujet($_GET['id']);
        if (!$InfosSujet) {
            throw new NotFoundHttpException();
        }

        Page::$titre = $InfosSujet['sujet_titre'] . ' - Fusionner le sujet';

        if (verifier('fusionner_sujets', $InfosSujet['sujet_forum_id'])) {
            //Si on veut fusionner le sujet
            if (isset($_POST['submit'])) {
                if (empty($_POST['sujet']) || count($_POST['sujet']) == 1)
                    return redirect(
                        'Vous devez remplir tous les champs nécessaires !',
                        'fusionner-' . $_GET['id'] . '.html',
                        MSG_ERROR
                    );

                //Si tout va bien on fusionner
                TopicDAO::FusionnerSujets($InfosSujet, $InfosSujet['sujet_corbeille']);

                return redirect(
                    'Les sujets ont bien été fusionnés.',
                    'sujet-' . $_GET['id'] . '-' . rewrite($InfosSujet['sujet_titre']) . '.html'
                );
            }

            //Si chercher des sujets
            if (isset($_POST['search']) && !empty($_POST['titre'])) {
                $ListerSujets = ListerSujetsTitre($_POST['titre']);
            }

            //On récupère la liste des sujets
            $in = array();
            if (isset($_POST['sujet'])) {
                foreach ($_POST['sujet'] as $cle => $valeur)
                    $in[] = $cle;
            }
            if (!in_array($_GET['id'], $in))
                $in[] = $_GET['id'];

            $ListerSujetsSelectionnes = ListerSujetsIn($in);

            //Inclusion de la vue
            fil_ariane($InfosSujet['sujet_forum_id'], array(
                htmlspecialchars($InfosSujet['sujet_titre']) => 'sujet-' . $_GET['id'] . '-' . rewrite($InfosSujet['sujet_titre']) . '.html',
                'Fusionner le sujet'
            ));

            return render_to_response(array(
                'InfosSujet' => $InfosSujet,
                'ListerSujetsSelectionnes' => $ListerSujetsSelectionnes,
                'ListerSujets' => !empty($ListerSujets) ? $ListerSujets : null,
            ));
        } else
            throw new AccessDeniedHttpException();
    }
}
