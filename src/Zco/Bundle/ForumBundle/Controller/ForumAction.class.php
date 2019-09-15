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
use Zco\Bundle\ContentBundle\Domain\CategoryDAO;
use Zco\Bundle\ForumBundle\Domain\ForumDAO;
use Zco\Bundle\ForumBundle\Domain\ReadMarkerDAO;
use Zco\Bundle\ForumBundle\Domain\TopicDAO;

/**
 * Contrôleur gérant l'affichage de la liste des sujets d'un forum.
 *
 * @author DJ Fox <marthe59@yahoo.fr>
 */
class ForumAction extends ForumActions
{
    public function execute()
    {
        if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
            throw new NotFoundHttpException();
        } else {
            $InfosForum = CategoryDAO::InfosCategorie($_GET['id']);
            if ((!$InfosForum || !verifier('voir_sujets', $_GET['id']))) {
                throw new NotFoundHttpException();
            } elseif (!empty($_GET['trash']) AND !verifier('corbeille_sujets', $_GET['id'])) {
                throw new NotFoundHttpException();
            }
        }

        // Si on est dans une catégorie de redirection
        if (!empty($InfosForum['cat_redirection'])) {
            return new RedirectResponse($InfosForum['cat_redirection'], 301);
        }

        // Si le forum est archiver on redirige l'utilisateur
        if ($InfosForum['cat_archive'] == 1 && !verifier('voir_archives')) {
            return redirect('Le forum n\'est plus accessible.', '/forum/', MSG_ERROR);
        }

        // Si on veut effectuer une action multiple
        if (isset($_POST['action']) AND in_array($_POST['action'], array('annonce', 'plus_annonce', 'resolu', 'nonresolu', 'favori', 'nonfavori', 'fermer', 'ouvrir', 'deplacer', 'corbeille', 'restaurer', 'supprimer', 'lu', 'nonlu')) AND !empty($_POST['sujet'])) {
            switch ($_POST['action']) {
                case 'annonce':
                    if (verifier('epingler_sujets', $_GET['id'])) {
                        foreach ($_POST['sujet'] as $clef => $valeur) {
                            TopicDAO::ChangerTypeSujet($clef, 0, $_GET['id']);
                        }
                        return redirect(
                            'Les opérations multiples ont bien été effectuées.',
                            '/forum/forum-' . $_GET['id'] . '-' . rewrite($InfosForum['cat_nom']) . '.html'
                        );
                    }
                    break;

                case 'plus_annonce':
                    if (verifier('epingler_sujets', $_GET['id'])) {
                        foreach ($_POST['sujet'] as $clef => $valeur) {
                            TopicDAO::ChangerTypeSujet($clef, 1, $_GET['id']);
                        }
                        return redirect(
                            'Les opérations multiples ont bien été effectuées.',
                            '/forum/forum-' . $_GET['id'] . '-' . rewrite($InfosForum['cat_nom']) . '.html'
                        );
                    }
                    break;

                case 'resolu':
                    if (verifier('resolu_sujets', $_GET['id'])) {
                        foreach ($_POST['sujet'] as $clef => $valeur) {
                            TopicDAO::ChangerResoluSujet($clef, 0, 0, $_GET['id']);
                        }
                        return redirect(
                            'Les opérations multiples ont bien été effectuées.',
                            '/forum/forum-' . $_GET['id'] . '-' . rewrite($InfosForum['cat_nom']) . '.html'
                        );
                    }
                    break;

                case 'nonresolu':
                    if (verifier('resolu_sujets', $_GET['id'])) {
                        foreach ($_POST['sujet'] as $clef => $valeur) {
                            TopicDAO::ChangerResoluSujet($clef, 1, 0, $_GET['id']);
                        }
                        return redirect(
                            'Les opérations multiples ont bien été effectuées.',
                            '/forum/forum-' . $_GET['id'] . '-' . rewrite($InfosForum['cat_nom']) . '.html'
                        );
                    }
                    break;

                case 'favori':
                    if (verifier('mettre_sujet_favori')) {
                        foreach ($_POST['sujet'] as $clef => $valeur) {
                            TopicDAO::ChangerFavori($clef, 0);
                        }
                        return redirect(
                            'Les opérations multiples ont bien été effectuées.',
                            '/forum/forum-' . $_GET['id'] . '-' . rewrite($InfosForum['cat_nom']) . '.html'
                        );
                    }
                    break;

                case 'nonfavori':
                    if (verifier('mettre_sujet_favori')) {
                        foreach ($_POST['sujet'] as $clef => $valeur) {
                            TopicDAO::ChangerFavori($clef, 1);
                        }
                        return redirect(
                            'Les opérations multiples ont bien été effectuées.',
                            '/forum/forum-' . $_GET['id'] . '-' . rewrite($InfosForum['cat_nom']) . '.html'
                        );
                    }
                    break;

                case 'fermer':
                    if (verifier('fermer_sujets', $_GET['id'])) {
                        foreach ($_POST['sujet'] as $clef => $valeur) {
                            TopicDAO::ChangerStatutSujet($clef, 0);
                        }
                        return redirect(
                            'Les opérations multiples ont bien été effectuées.',
                            '/forum/forum-' . $_GET['id'] . '-' . rewrite($InfosForum['cat_nom']) . '.html'
                        );
                    }
                    break;

                case 'ouvrir':
                    if (verifier('fermer_sujets', $_GET['id'])) {
                        foreach ($_POST['sujet'] as $clef => $valeur) {
                            TopicDAO::ChangerStatutSujet($clef, 1);
                        }
                        return redirect(
                            'Les opérations multiples ont bien été effectuées.',
                            '/forum/forum-' . $_GET['id'] . '-' . rewrite($InfosForum['cat_nom']) . '.html'
                        );
                    }
                    break;

                case 'deplacer':
                    if (verifier('deplacer_sujets', $_GET['id']) AND !empty($_GET['id']) AND is_numeric($_GET['id']) AND !empty($_POST['forum_cible']) AND is_numeric($_POST['forum_cible']) AND $_GET['id'] !== $_POST['forum_cible']) {
                        foreach ($_POST['sujet'] as $clef => $valeur) {
                            TopicDAO::DeplacerSujet($clef, $_GET['id'], $_POST['forum_cible']);
                        }
                        return redirect(
                            'Les opérations multiples ont bien été effectuées.',
                            '/forum/forum-' . $_GET['id'] . '-' . rewrite($InfosForum['cat_nom']) . '.html'
                        );
                    }
                    break;

                case 'corbeille':
                    if (verifier('corbeille_sujets', $_GET['id'])) {
                        foreach ($_POST['sujet'] as $clef => $valeur) {
                            TopicDAO::Corbeille($clef, $_GET['id']);
                        }
                        return redirect(
                            'Les opérations multiples ont bien été effectuées.',
                            '/forum/forum-' . $_GET['id'] . '-' . rewrite($InfosForum['cat_nom']) . '.html'
                        );
                    }
                    break;

                case 'favoris':
                    foreach ($_POST['sujet'] as $clef => &$valeur) {
                        TopicDAO::ChangerFavori($clef, 1, 0);
                    }
                    return redirect(
                        'Les opérations multiples ont bien été effectuées.',
                        '/forum/forum-' . $_GET['id'] . '-' . rewrite($InfosForum['cat_nom']) . '.html'
                    );
                    break;

                case 'restaurer':
                    if (verifier('corbeille_sujets', $_GET['id'])) {
                        foreach ($_POST['sujet'] as $clef => &$valeur) {
                            TopicDAO::Restaurer($clef, $_GET['id']);
                        }
                        return redirect(
                            'Les opérations multiples ont bien été effectuées.',
                            '/forum/forum-' . $_GET['id'] . '-' . rewrite($InfosForum['cat_nom']) . '.html'
                        );
                    }
                    break;

                case 'supprimer':
                    if (verifier('suppr_sujets', $_GET['id'])) {
                        if (isset($_POST['confirmer'])) {
                            foreach ($_POST['sujet'] as &$sujet) {
                                TopicDAO::Supprimer($sujet, $_GET['id'], isset($_GET['trash']));
                            }
                            return redirect(
                                'Les opérations multiples ont bien été effectuées.',
                                '/forum/forum-' . $_GET['id'] . '-' . rewrite($InfosForum['cat_nom']) . '.html'
                            );
                        } elseif (isset($_POST['annuler'])) {
                            return new RedirectResponse('forum-' . $_GET['id'] . '-' . rewrite($InfosForum['cat_nom']) . '.html' . (isset($_GET['trash']) ? '?trash=1' : ''));
                        } else {
                            Page::$titre = 'Confirmation de la suppression multiple de sujets';
                            fil_ariane(Page::$titre);
                            return render_to_response('ZcoForumBundle::suppressionMultiple.html.php', array('InfosForum' => $InfosForum));
                        }
                    }
                    break;

                case 'lu':
                case 'nonlu':
                    $lu = $_POST['action'] == 'lu';
                    foreach ($_POST['sujet'] as $sujet => &$on) {
                        $on && ReadMarkerDAO::MarquerSujetLu($sujet, $lu);
                    }
                    return redirect(
                        $lu ? 'Les sujets sélectionnés ont été marqués comme lus.' : 'Les sujets sélectionnés ont été marqués comme non lus.',
                        'forum-' . $_GET['id'] . '-' . rewrite($InfosForum['cat_nom']) . '.html'
                    );
                    break;
            }
        } else {
            $nbSujetsParPage = 30;
            $CompterSujets = ForumDAO::CompterSujets($_GET['id']);
            $NombreDePages = ceil($CompterSujets / $nbSujetsParPage);
            //TODO zCorrecteurs::VerifierFormatageUrl($InfosForum['cat_nom'], true, false, $NombreDePages);
            $_GET['p'] = !empty($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : $NombreDePages;

            if ($_GET['p'] > $NombreDePages)
                throw new NotFoundHttpException();

            if ($_GET['p'] < $NombreDePages) {
                Page::$titre .= ' - Page ' . $_GET['p'];
                Page::$description .= ' - Page ' . $_GET['p'];
            }

            // Transmettre le filtrage sur les flags
            $flags = array();
            $f = array('solved', 'closed', 'favori');
            foreach ($f as &$g)
                if (isset($_GET[$g]))
                    $flags[] = $g . '=' . (int)(bool)$_GET[$g];
            $flags = $flags ? '?' . implode('&amp;', $flags) : '';


            // On récupère la liste des numéros des pages.
            $tableau_pages = liste_pages($_GET['p'], $NombreDePages, $CompterSujets, $nbSujetsParPage, 'forum-' . $_GET['id'] . '-p%s-' . rewrite($InfosForum['cat_nom']) . '.html' . $flags, true);
            $debut = ($NombreDePages - $_GET['p']) * $nbSujetsParPage;

            // On récupère les sujets du forum depuis la fonction du modèle.
            $ListerSujets = ForumDAO::ListerSujets($debut, $nbSujetsParPage, $_GET['id']);

            $derniere_lecture = ReadMarkerDAO::DerniereLecture($_SESSION['id']);

            $Lu = array();
            $Pages = array();

            if ($ListerSujets) {
                foreach ($ListerSujets as $clef => $valeur) {
                    // Appel de la fonction lu / non-lu et de la fonction trouver dernier message non lu.
                    $EnvoiDesInfos = array(
                        'lunonlu_utilisateur_id' => $valeur['lunonlu_utilisateur_id'],
                        'lunonlu_sujet_id' => $valeur['lunonlu_sujet_id'],
                        'lunonlu_message_id' => $valeur['lunonlu_message_id'],
                        'lunonlu_participe' => $valeur['lunonlu_participe'],
                        'sujet_dernier_message' => $valeur['message_id'],
                        'date_dernier_message' => $valeur['message_timestamp'],
                        'derniere_lecture_globale' => $derniere_lecture
                    );

                    $Lu[$clef] = ForumDAO::LuNonluForum($EnvoiDesInfos);

                    // Liste des pages
                    $nbMessagesParPage = 20;
                    $NombreDePagesSujet = ceil(($valeur['sujet_reponses'] + 1) / $nbMessagesParPage);
                    $Pages[$clef] = liste_pages(-1, $NombreDePagesSujet, $valeur['sujet_reponses'], $nbMessagesParPage, 'sujet-' . $valeur['sujet_id'] . '-p%s-' . rewrite($valeur['sujet_titre']) . '.html');
                }
            }

            $SautRapide = ForumDAO::RecupererSautRapide($_GET['id']);
            $action_etendue_a_plusieurs_messages_actif =
                verifier('connecte') ||
                verifier('fermer_sujets', $_GET['id']) ||
                verifier('epingler_sujets', $_GET['id']) ||
                verifier('resolu_sujets', $_GET['id']) ||
                verifier('corbeille_sujets', $_GET['id']) ||
                verifier('suppr_sujets', $_GET['id']);

            // Listage des forums fils s'il y en a
            if ($InfosForum['cat_droite'] - $InfosForum['cat_gauche'] != 1) {
                $ListerUneCategorie = ForumDAO::ListerCategoriesForum($InfosForum);
                $LuForum = array();
                $nbIndex = 0;
                foreach ($ListerUneCategorie as $cat) {
                    // Si le forum est vide, l'image lu/non-lu sera une ampoule blanche.
                    if ($cat['cat_last_element'] == 0) {
                        $LuForum[$cat['cat_id']] = array(
                            'image' => 'lightbulb_off',
                            'title' => 'Pas de nouvelles réponses, jamais participé'
                        );
                    } else {
                        $LuForum[$cat['cat_id']] = ForumDAO::LuNonluCategorie(array(
                            'lunonlu_utilisateur_id' => $cat['lunonlu_utilisateur_id'],
                            'lunonlu_sujet_id' => $cat['lunonlu_sujet_id'],
                            'lunonlu_message_id' => $cat['lunonlu_message_id'],
                            'lunonlu_participe' => $cat['lunonlu_participe'],
                            'sujet_dernier_message' => $cat['message_id'],
                            'date_dernier_message' => $cat['message_timestamp'],
                            'derniere_lecture_globale' => $derniere_lecture,
                        ));
                    }

                    if (!empty($_GET['archives'])) {
                        // Forum parent
                        $tempParent = CategoryDAO::ListerParents($cat);
                        if (count($tempParent) > 2) {
                            $tempParent = array_pop($tempParent);
                            $ListerUneCategorie[$nbIndex]['parent'] = $tempParent;
                        }
                    }

                    $nbIndex++;
                }
            } else {
                $ListerUneCategorie = null;
                $LuForum = null;
            }

            // Forum parent
            $parent = CategoryDAO::ListerParents($InfosForum);
            if (count($parent) > 2) // Racine + Les Forums
                $parent = array_pop($parent);
            else
                $parent = null;

            // Inclusion de la vue
            $msgFil = '';
            if (!empty($_GET['trash']) && empty($_GET['archives'])) {
                $msgFil = 'Liste des sujets dans la corbeille';
            } else if (empty($_GET['trash']) && !empty($_GET['archives'])) {
                $msgFil = 'Liste des forums archivés';
            } else {
                $msgFil = 'Liste des sujets';
            }
            fil_ariane($_GET['id'], $msgFil);
            $this->get('zco_core.resource_manager')->requireResources(array(
                '@ZcoCoreBundle/Resources/public/css/zcode.css',
                '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
            ));
            $this->get('zco_core.resource_manager')->requireResource('@ZcoCoreBundle/Resources/public/js/messages.js');

            return render_to_response('ZcoForumBundle::forum.html.php', array(
                'InfosForum' => $InfosForum,
                'CompterSujets' => $CompterSujets,
                'flags' => $flags,
                'Lu' => $Lu,
                'tableau_pages' => $tableau_pages,
                'ListerSujets' => $ListerSujets,
                'Pages' => $Pages,
                'SautRapide' => $SautRapide,
                'action_etendue_a_plusieurs_messages_actif' => $action_etendue_a_plusieurs_messages_actif,
                'ListerUneCategorie' => isset($ListerUneCategorie) ? $ListerUneCategorie : null,
                'LuForum' => $LuForum,
                'Parent' => $parent,
            ));
        }
    }
}
