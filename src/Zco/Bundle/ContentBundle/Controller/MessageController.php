<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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

namespace Zco\Bundle\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\ContentBundle\Domain\CategoryDAO;
use Zco\Bundle\ContentBundle\Domain\MessageDAO;
use Zco\Bundle\ContentBundle\Domain\TopicDAO;

/**
 * @author Original DJ Fox <marthe59@yahoo.fr>
 */
final class MessageController extends Controller
{
    public function markHelpedAction($id, Request $request)
    {
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
        if ($request->get('token') != $_SESSION['token']) {
            // Protection CSRF.
            throw new AccessDeniedHttpException();
        }

        $InfosMessage = $this->getMessage($id);
        if ($InfosMessage['sujet_corbeille'] && !verifier('corbeille_sujets', $InfosMessage['sujet_forum_id'])) {
            throw new AccessDeniedHttpException();
        }
        if ($_SESSION['id'] != $InfosMessage['sujet_auteur'] && !verifier('indiquer_messages_aide', $InfosMessage['sujet_forum_id'])) {
            throw new AccessDeniedHttpException();
        }
        $status = (boolean)$request->get('status', false);
        MessageDAO::ChangerHelp($id, $status);
        $message = $status ? 'Le message a bien été marqué comme vous ayant aidé(e).' : 'Le message a bien été marqué comme ne vous ayant pas aidé(e).';

        return redirect(
            $message,
            $this->generateUrl('zco_topic_show', ['id' => $InfosMessage['sujet_id'], 'c' => $InfosMessage['message_id'], 'slug' => rewrite($InfosMessage['sujet_titre'])])
        );
    }

    public function editAction($id, Request $request)
    {
        $InfosMessage = $this->getMessage($id);
        if (!($InfosMessage['message_auteur'] == $_SESSION['id'] || verifier('editer_messages_autres', $InfosMessage['sujet_forum_id']))) {
            throw new AccessDeniedHttpException();
        }

        $InfosForum = CategoryDAO::InfosCategorie($InfosMessage['sujet_forum_id']);
        $InfosSujet = TopicDAO::InfosSujet($InfosMessage['sujet_id']);

        if ($request->isMethod('POST')) {
            //Si on a posté quelque chose
            //On a validé le formulaire. Des vérifications s'imposent.
            if (empty($_POST['texte'])) {
                return redirect(
                    'Vous devez remplir tous les champs nécessaires !',
                    $this->generateUrl('zco_topic_show', ['id' => $InfosSujet['sujet_id'], 'slug' => rewrite($InfosSujet['sujet_titre'])]),
                    MSG_ERROR
                );
            }
            $InfosMessage['sujet_annonce'] = isset($_POST['annonce']) ? 1 : 0;
            $InfosMessage['sujet_ferme'] = isset($_POST['ferme']) ? 1 : 0;
            $InfosMessage['sujet_resolu'] = isset($_POST['resolu']) ? 1 : 0;
            MessageDAO::EditerMessage($id, $InfosMessage['sujet_forum_id'], $InfosMessage['message_sujet_id'], $InfosMessage['sujet_annonce'], $InfosMessage['sujet_ferme'], $InfosMessage['sujet_resolu'], $InfosMessage['sujet_auteur']);

            return redirect(
                'Le message a bien été édité.',
                $this->generateUrl('zco_topic_show', ['id' => $InfosSujet['sujet_id'], 'c' => $id, 'slug' => rewrite($InfosSujet['sujet_titre'])])
            );
        }

        fil_ariane($InfosMessage['sujet_forum_id'], array(
            htmlspecialchars($InfosMessage['sujet_titre']) => $this->generateUrl('zco_topic_show', ['id' => $InfosSujet['sujet_id'], 'slug' => rewrite($InfosSujet['sujet_titre'])]),
            'Modifier un message'
        ));
        $this->get('zco_core.resource_manager')->requireResources([
            '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
            '@ZcoContentBundle/Resources/public/css/forum.css',
        ]);

        return $this->render('ZcoContentBundle:Forum:editer.html.php', array(
            'tabindex_zform' => 1,
            'sujet_titre' => $InfosMessage['sujet_titre'],
            'sujet_id' => $InfosMessage['message_sujet_id'],
            'RevueSujet' => TopicDAO::RevueSujet($InfosMessage['message_sujet_id']),
            'InfosMessage' => $InfosMessage,
            'InfosForum' => $InfosForum,
            'InfosSujet' => $InfosSujet
        ));
    }

    public function deleteAction($id, Request $request)
    {
        $InfosMessage = $this->getMessage($id);
        if (empty($InfosMessage) || !verifier('voir_sujets', $InfosMessage['sujet_forum_id'])) {
            throw new NotFoundHttpException();
        }

        $url = $this->generateUrl('zco_topic_show', ['id' => $InfosMessage['sujet_id'], 'slug' => rewrite($InfosMessage['sujet_titre'])]);

        //Si on a le droit de supprimer ce message
        if (!(
                verifier('suppr_messages', $InfosMessage['sujet_forum_id'])
                || (verifier('suppr_ses_messages', $InfosMessage['sujet_forum_id']) && $InfosMessage['message_auteur'] == $_SESSION['id'])
            ) && !$InfosMessage['sujet_corbeille']
            && (!$InfosMessage['sujet_ferme'] || verifier('repondre_sujets_fermes', $InfosMessage['sujet_forum_id'])
            )
        ) {
            throw new AccessDeniedHttpException();
        }
        //Si on confirme la suppression
        if ($request->isMethod('POST')) {
            MessageDAO::SupprimerMessage($id, $InfosMessage['sujet_id'], $InfosMessage['sujet_dernier_message'], $InfosMessage['sujet_forum_id'], $InfosMessage['sujet_corbeille']);

            return redirect('Le message a bien été supprimé.', $url);
        }

        if ($id == $InfosMessage['sujet_premier_message']) {
            // Si le message est le premier message
            return redirect(
                'La suppression du message a échoué : on ne peut pas supprimer le premier message du sujet.',
                $url,
                MSG_ERROR
            );
        }

        fil_ariane($InfosMessage['sujet_forum_id'], array(
            htmlspecialchars($InfosMessage['sujet_titre']) => $url,
            'Supprimer un message du sujet'
        ));

        return $this->render('ZcoContentBundle:Forum:supprimerMessage.html.php', array(
            'InfosMessage' => $InfosMessage,
            'url' => $url,
        ));
    }

    public function newAction($id, Request $request)
    {
        $InfosSujet = $this->getTopic($id);
        $InfosForum = CategoryDAO::InfosCategorie($InfosSujet['sujet_forum_id']);

        if ((!verifier('repondre_sujets', $InfosSujet['sujet_forum_id']) && !$InfosSujet['sujet_ferme'])
            || (!verifier('repondre_sujets_fermes', $InfosSujet['sujet_forum_id']) && $InfosSujet['sujet_ferme'])) {
            throw new AccessDeniedHttpException();
        }

        // Si le forum est archivé
        if ($InfosForum['cat_archive']) {
            throw new NotFoundHttpException();
        }

        if (empty($InfosSujet['dernier_message_auteur'])) {
            $InfosSujet['dernier_message_auteur'] = $InfosSujet['sujet_auteur'];
            $InfosSujet['dernier_message_date'] = $InfosSujet['sujet_date'];
        }

        \Zco\Page::$titre = htmlspecialchars($InfosSujet['sujet_titre']) . ' - Ajout d\'une réponse';

        //On a validé le formulaire. Des vérifications s'imposent.
        if (empty($_POST['texte'])) {
            return redirect('Vous devez remplir tous les champs nécessaires !', $this->generateUrl('zco_forum_index'), MSG_ERROR);
        }

        $nouveau_message_id = MessageDAO::EnregistrerNouveauMessage($id, $InfosSujet['sujet_forum_id'], $InfosSujet['sujet_corbeille']);

        return redirect(
            'Le message a bien été ajouté.',
            $this->generateUrl('zco_topic_show', ['id' => $id, 'c' => $nouveau_message_id, 'slug' => rewrite($InfosSujet['sujet_titre'])])
        );
    }

    private function getTopic($id)
    {
        $InfosSujet = TopicDAO::InfosSujet($id);
        if (empty($InfosSujet)) {
            throw new NotFoundHttpException();
        }
        if (!verifier('voir_sujets', $InfosSujet['sujet_forum_id'])) {
            throw new NotFoundHttpException();
        }
        \Zco\Page::$titre = htmlspecialchars($InfosSujet['sujet_titre']);

        return $InfosSujet;
    }

    private function getMessage($id)
    {
        $InfosMessage = MessageDAO::InfosMessage($id);
        if (!$InfosMessage) {
            throw new NotFoundHttpException();
        }
        return $InfosMessage;
    }
}