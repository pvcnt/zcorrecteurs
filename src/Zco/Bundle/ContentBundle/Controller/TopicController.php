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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\ContentBundle\Domain\CategoryDAO;
use Zco\Bundle\ContentBundle\Domain\ForumDAO;
use Zco\Bundle\ContentBundle\Domain\PollDAO;
use Zco\Bundle\ContentBundle\Domain\TopicDAO;

/**
 * @author Original DJ Fox <marthe59@yahoo.fr>
 */
final class TopicController extends Controller
{
    public function newAction($id, Request $request)
    {
        $InfosForum = CategoryDAO::InfosCategorie($id);
        if (!$InfosForum) {
            throw new NotFoundHttpException();
        }
        if (!verifier('creer_sujets', $id)) {
            throw new AccessDeniedHttpException();
        }
        if (!empty($_GET['trash']) AND !verifier('corbeille_sujets', $id)) {
            throw new AccessDeniedHttpException();
        }
        if ($InfosForum['cat_archive']) {
            return redirect('Le forum n\'est plus accessible.', $this->generateUrl('zco_forum_index'), MSG_ERROR);
        }

        \Zco\Page::$titre = htmlspecialchars($InfosForum['cat_nom']) . ' - Nouveau sujet';

        if ($request->isMethod('POST')) {
            if (empty($_POST['titre']) || empty($_POST['texte'])) {
                $_SESSION['forum_message_texte'] = $_POST['texte'];
                return redirect('Vous devez remplir tous les champs nécessaires !', $_SERVER['REQUEST_URI'], MSG_ERROR);
            }
            $annonce = 0;
            $ferme = 0;
            $corbeille = 0;
            $resolu = 0;
            if (isset($_POST['annonce']) AND verifier('epingler_sujets', $id)) {
                $annonce = 1;
            }
            if (isset($_POST['ferme']) AND verifier('fermer_sujets', $id)) {
                $ferme = 1;
            }
            if (isset($_POST['resolu']) AND verifier('resolu_sujets', $id)) {
                $resolu = 1;
            }
            if (isset($_POST['corbeille']) AND verifier('corbeille_sujets', $id)) {
                $corbeille = 1;
            }

            $nouveau_sujet_id = TopicDAO::EnregistrerNouveauSujet($id, $annonce, $ferme, $resolu, $corbeille);

            return redirect(
                'Le sujet a bien été créé.',
                $this->generateUrl('zco_topic_show', ['id' => $nouveau_sujet_id, 'slug' => rewrite($_POST['titre'])])
            );
        }

        fil_ariane($id, 'Créer un nouveau sujet');
        $this->get('zco_core.resource_manager')->requireResources([
            '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
            '@ZcoContentBundle/Resources/public/css/forum.css',
        ]);

        if (isset($_SESSION['forum_message_texte'])) {
            $texte = $_SESSION['forum_message_texte'];
            unset($_SESSION['forum_message_texte']);
        } else {
            $texte = '';
        }

        return $this->render('ZcoContentBundle:Forum:nouveau.html.php', [
            'InfosForum' => $InfosForum,
            'tabindex_zform' => 4,
            'texte_zform' => $texte,
        ]);
    }

    public function showAction($id, $slug, Request $request)
    {
        $InfosSujet = $this->getTopic($id);
        $InfosForum = CategoryDAO::InfosCategorie($InfosSujet['sujet_forum_id']);
        $url = $this->generateUrl('zco_topic_show', ['id' => $id, 'slug' => rewrite($InfosSujet['sujet_titre'])]);
        //TODO zCorrecteurs::VerifierFormatageUrl($InfosSujet['sujet_titre'], true, true, 1);

        // Si le forum est archivé
        if ($InfosForum['cat_archive'] == 1 && !verifier('voir_archives')) {
            return redirect('Le forum n\'est plus accessible.', '/forum/', MSG_ERROR);
        }

        // Détermination de la page courante
        $page = (int)$request->get('p', 1);
        if ($page > 1) {
            \Zco\Page::$titre .= ' - Page ' . $page;
        }

        //--- Redirection de la mort qui tue pour le référencement. :D ---
        $messageId = $request->get('c');
        if ($messageId) {
            $page = TopicDAO::TrouverLaPageDeCeMessage($id, $messageId);
            if ($page == 1) {
                return new RedirectResponse($url . '#m' . $messageId, 301);
            }
            return new RedirectResponse($url . '?p=' . $page . '#m' . $messageId, 301);
        }

        //On récupère la liste des numéros des pages.
        $nbMessagesParPage = 20;
        $NombreDePages = ceil($InfosSujet['nombre_de_messages'] / $nbMessagesParPage);
        if ($page > $NombreDePages)
            throw new NotFoundHttpException();
        $tableau_pages = liste_pages($page, $NombreDePages, $url . '?p=%s');
        $debut = ($page - 1) * $nbMessagesParPage;

        if ($page > 1) {
            $debut--;
            $nombreDeMessagesAafficher = $nbMessagesParPage + 1;
        } else {
            $nombreDeMessagesAafficher = $nbMessagesParPage;
        }

        $ListerMessages = TopicDAO::ListerMessages($id, $debut, $nombreDeMessagesAafficher);
        $SautRapide = ForumDAO::RecupererSautRapide($InfosSujet['sujet_forum_id']);
        $PremierMessage = TopicDAO::ListerMessages($id, 0, 1);

        //--- Gestion des lus / non-lus ---
        $InfosLuNonlu = [
            'lunonlu_utilisateur_id' => $InfosSujet['lunonlu_utilisateur_id'],
            'lunonlu_message_id' => $InfosSujet['lunonlu_message_id']
        ];
        if (verifier('connecte')) {
            TopicDAO::RendreLeSujetLu($id, $page, $NombreDePages, $InfosSujet['sujet_dernier_message'], $ListerMessages, $InfosLuNonlu);
        }

        //Pour un meilleur référencement : ajout du début du premier message de la
        //page courante en balise meta description.
        $haystack = strip_tags($ListerMessages[0]['message_texte']);
        if (mb_strlen($haystack) > 10) {
            $offset = mb_strlen($haystack) - 10;
            $mettre_description = true;
        } else {
            $mettre_description = false;
        }
        if (mb_strlen($haystack) > 250) {
            $offset = 240;
        }

        if ($mettre_description) {
            \Zco\Page::$description = htmlspecialchars(mb_substr($haystack, 0, mb_strpos($haystack, ' ', $offset)));
            if ($page > 1) {
                \Zco\Page::$description .= ' - Page ' . $page;
            }
        }

        //Si le sujet est un sondage, on récupère les infos du sondage.
        if ($InfosSujet['sujet_sondage'] > 0) {
            $ListerResultatsSondage = PollDAO::ListerResultatsSondage($InfosSujet['sujet_sondage']);

            //On compte le nombre total de votes
            $nombre_total_votes = 0;
            foreach ($ListerResultatsSondage as $clef => $valeur) {
                $nombre_total_votes += $valeur['nombre_votes'];
            }
        } else {
            $ListerResultatsSondage = null;
            $nombre_total_votes = null;
        }

        //Inclusion des vues
        fil_ariane($InfosSujet['sujet_forum_id'], array(
            htmlspecialchars($InfosSujet['sujet_titre']) => $url,
            'Voir le sujet'
        ));
        $this->get('zco_core.resource_manager')->requireResources([
            '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
            '@ZcoCoreBundle/Resources/public/js/zform.js',
            '@ZcoContentBundle/Resources/public/css/forum.css',
        ]);

        if (verifier('deplacer_sujets', $InfosSujet['sujet_forum_id'])) {
            $CategoriesForums = ForumDAO::ListerCategoriesForum();
        } else {
            $CategoriesForums = [];
        }

        //Cette big condition permet de savoir si on affiche ou pas les options de modération.
        if
        (
            (
                $_SESSION['id'] == $InfosSujet['sujet_auteur']
                OR verifier('resolu_sujets', $InfosSujet['sujet_forum_id'])
            )
            OR verifier('epingler_sujets', $InfosSujet['sujet_forum_id'])
            OR verifier('fermer_sujets', $InfosSujet['sujet_forum_id'])
            OR verifier('editer_sujets', $InfosSujet['sujet_forum_id'])
            OR verifier('deplacer_sujets', $InfosSujet['sujet_forum_id'])
            OR verifier('corbeille_sujets', $InfosSujet['sujet_forum_id'])
            OR verifier('suppr_sujets', $InfosSujet['sujet_forum_id'])
        ) {
            $afficher_options = true;
        } else {
            $afficher_options = false;
        }

        return $this->render('ZcoContentBundle:Forum:sujet.html.php', [
            'InfosSujet' => $InfosSujet,
            'InfosForum' => $InfosForum,
            'tableau_pages' => $tableau_pages,
            'ListerMessages' => $ListerMessages,
            'SautRapide' => $SautRapide,
            'InfosLuNonlu' => $InfosLuNonlu,
            'afficher_options' => $afficher_options,
            'ListerResultatsSondage' => $ListerResultatsSondage,
            'nombre_total_votes' => $nombre_total_votes,
            'NombreDePages' => $NombreDePages,
            'page' => $page,
            'PremierMessage' => $PremierMessage[0],
            'CategoriesForums' => $CategoriesForums,
        ]);
    }

    public function moveAction($id, Request $request)
    {
        $InfosSujet = $this->getTopic($id);

        if ($InfosSujet['sujet_corbeille']) {
            throw new AccessDeniedHttpException();
        }
        if (!verifier('deplacer_sujets', $InfosSujet['sujet_forum_id'])) {
            throw new AccessDeniedHttpException();
        }
        if (!verifier('voir_sujets', $_POST['forum_cible'])) {
            // Si on n'a pas le droit de voir le forum de destination.
            throw new NotFoundHttpException();
        }

        if (empty($_POST['forum_cible']) || !is_numeric($_POST['forum_cible'])) {
            // Forum cible non envoyé.
            throw new NotFoundHttpException();
        }

        $url = $this->generateUrl('zco_topic_show', ['id' => $id, 'slug' => rewrite($InfosSujet['sujet_titre'])]);

        //Si forum source et cible sont identiques.
        if ($InfosSujet['sujet_forum_id'] == $_POST['forum_cible']) {
            return redirect('Le forum source doit être différent du forum cible.', $url, MSG_ERROR);
        }

        TopicDAO::DeplacerSujet($id, $InfosSujet['sujet_forum_id'], $_POST['forum_cible']);
        return redirect('Le sujet a bien été déplacé.', $url);
    }

    public function trashAction($id, Request $request)
    {
        $InfosSujet = $this->getTopic($id);

        //Vérification du token.
        if (empty($_GET['token']) || $_GET['token'] != $_SESSION['token']) {
            throw new AccessDeniedHttpException();
        }

        $url = $this->generateUrl('zco_topic_show', ['id' => $id, 'slug' => rewrite($InfosSujet['sujet_titre'])]);
        $status = (boolean)$request->get('status', false);
        if ($status) {
            if (!verifier('corbeille_sujets', $InfosSujet['sujet_forum_id'])) {
                throw new AccessDeniedHttpException();
            }
            TopicDAO::Corbeille($InfosSujet['sujet_id'], $InfosSujet['sujet_forum_id']);

            return redirect('Le sujet a bien été mis en corbeille.', $url);
        } else {
            if (!verifier('corbeille_sujets', $InfosSujet['sujet_forum_id'])) {
                throw new AccessDeniedHttpException();
            }
            TopicDAO::Restaurer($InfosSujet['sujet_id'], $InfosSujet['sujet_forum_id']);

            return redirect('Le sujet a bien été mis en restauré.', $url);
        }
    }

    public function markSolvedAction($id)
    {
        $InfosSujet = $this->getTopic($id);

        //Vérification du token.
        if (empty($_GET['token']) || $_GET['token'] != $_SESSION['token']) {
            throw new AccessDeniedHttpException();
        }

        $allowed = $_SESSION['id'] == $InfosSujet['sujet_auteur']
            || verifier('resolu_sujets', $InfosSujet['sujet_forum_id']);
        if (!$allowed) {
            throw new AccessDeniedHttpException();
        }
        TopicDAO::ChangerResoluSujet($id, $InfosSujet['sujet_resolu']);
        $message = $InfosSujet['sujet_resolu'] ? 'Le sujet a bien été marqué comme non résolu.' : 'Le sujet a bien été marqué comme résolu.';

        return redirect(
            $message,
            $this->generateUrl('zco_topic_show', ['id' => $id, 'slug' => rewrite($InfosSujet['sujet_titre'])])
        );
    }

    public function markPinnedAction($id)
    {
        $InfosSujet = $this->getTopic($id);

        //Vérification du token.
        if (empty($_GET['token']) || $_GET['token'] != $_SESSION['token']) {
            throw new AccessDeniedHttpException();
        }

        if (!verifier('epingler_sujets', $InfosSujet['sujet_forum_id'])) {
            throw new AccessDeniedHttpException();
        }
        TopicDAO::ChangerTypeSujet($id, $InfosSujet['sujet_annonce']);
        $message = $InfosSujet['sujet_annonce'] ? 'Le sujet a bien été désépinglé.' : 'Le sujet a bien été épinglé.';

        return redirect(
            $message,
            $this->generateUrl('zco_topic_show', ['id' => $id, 'slug' => rewrite($InfosSujet['sujet_titre'])])
        );
    }

    public function markClosed($id)
    {
        $InfosSujet = $this->getTopic($id);

        //Vérification du token.
        if (empty($_GET['token']) || $_GET['token'] != $_SESSION['token']) {
            throw new AccessDeniedHttpException();
        }

        if (!verifier('fermer_sujets', $InfosSujet['sujet_forum_id'])) {
            throw new AccessDeniedHttpException();
        }
        TopicDAO::ChangerStatutSujet($id, $InfosSujet['sujet_ferme']);
        $message = $InfosSujet['sujet_ferme'] ? 'Le sujet a bien été ouvert.' : 'Le sujet a bien été fermé.';

        return redirect(
            $message,
            $this->generateUrl('zco_topic_show', ['id' => $id, 'slug' => rewrite($InfosSujet['sujet_titre'])])
        );
    }

    public function deleteAction($id, Request $request)
    {
        $InfosSujet = $this->getTopic($id);
        $InfosForum = CategoryDAO::InfosCategorie($InfosSujet['sujet_forum_id']);

        if (!verifier('suppr_sujets', $InfosSujet['sujet_forum_id'])) {
            throw new AccessDeniedHttpException();
        }
        if ($request->isMethod('POST')) {
            TopicDAO::Supprimer($InfosSujet['sujet_id'], $InfosSujet['sujet_forum_id'], $InfosSujet['sujet_corbeille']);

            return redirect(
                'Le sujet a bien été supprimé.',
                $this->generateUrl('zco_forum_show', ['id' => $InfosSujet['sujet_forum_id'], 'slug' => rewrite($InfosForum['cat_nom'])])
            );
        }

        fil_ariane($InfosSujet['sujet_forum_id'], [
            htmlspecialchars($InfosSujet['sujet_titre']) => $this->generateUrl('zco_topic_show', ['id' => $id, 'slug' => rewrite($InfosSujet['sujet_titre'])]),
            'Supprimer le sujet'
        ]);

        return $this->render('ZcoContentBundle:Forum:supprimerSujet.html.php', [
            'InfosSujet' => $InfosSujet,
            'InfosForum' => $InfosForum,
        ]);
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
}