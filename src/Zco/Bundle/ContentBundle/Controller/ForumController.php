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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\ContentBundle\Domain\CategoryDAO;
use Zco\Bundle\ContentBundle\Domain\ForumDAO;

/**
 * @author Original DJ Fox <marthe59@yahoo.fr>
 */
final class ForumController extends Controller
{
    public function indexAction()
    {
        //Redirection si demandé
        if (!empty($_POST['saut_forum'])) {
            return new RedirectResponse('/forum/' . htmlspecialchars($_POST['saut_forum']));
        }

        $ListerCategories = ForumDAO::ListerCategoriesForum([]);

        //Appel de la fonction lu / non-lu
        $Lu = array();
        if ($ListerCategories) {
            $nbIndex = 0;
            foreach ($ListerCategories as $cat) {
                //Si le forum est vide, l'image lu/non-lu sera une ampoule blanche.
                if ($cat['cat_last_element'] == 0) {
                    $Lu[$cat['cat_id']] = array(
                        'image' => 'lightbulb_off',
                        'title' => 'Pas de nouvelles réponses, jamais participé'
                    );
                } else {
                    $Lu[$cat['cat_id']] = ForumDAO::LuNonluCategorie(array(
                        'lunonlu_utilisateur_id' => $cat['lunonlu_utilisateur_id'],
                        'lunonlu_sujet_id' => $cat['lunonlu_sujet_id'],
                        'lunonlu_message_id' => $cat['lunonlu_message_id'],
                        'lunonlu_participe' => $cat['lunonlu_participe'],
                        'sujet_dernier_message' => $cat['message_id'],
                        'date_dernier_message' => $cat['message_timestamp'],
                    ));
                }

                if (!empty($_GET['archives'])) {
                    // Forum parent
                    $parent = CategoryDAO::ListerParents($cat);
                    if (count($parent) > 2) {
                        $parent = array_pop($parent);
                        $ListerCategories[$nbIndex]['parent'] = $parent;
                    }
                }

                $nbIndex++;
            }
        }

        if (!empty($_GET['trash'])) {
            fil_ariane('Accueil de la corbeille');
        } elseif (!empty($_GET['archives'])) {
            fil_ariane('Accueil des archives');
        } else {
            fil_ariane('Accueil des forums');
        }
        $this->get('zco_core.resource_manager')->requireResources([
            '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
            '@ZcoContentBundle/Resources/public/css/forum.css',
        ]);
        $response = $this->render('ZcoContentBundle:Forum:index.html.php', [
            'ListerCategories' => $ListerCategories,
            'Lu' => $Lu,
        ]);
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('cache-Control', 'no-cache');

        return $response;
    }

    public function showForumAction($id, $slug, Request $request)
    {
        $InfosForum = CategoryDAO::InfosCategorie($id);
        if ((!$InfosForum || !verifier('voir_sujets', $id))) {
            throw new NotFoundHttpException();
        } elseif (!empty($_GET['trash']) && !verifier('corbeille_sujets', $id)) {
            throw new NotFoundHttpException();
        }

        // Si on est dans une catégorie de redirection
        if (!empty($InfosForum['cat_redirection'])) {
            return new RedirectResponse($InfosForum['cat_redirection'], 301);
        }

        // Si le forum est archivé on redirige l'utilisateur
        if ($InfosForum['cat_archive'] && !verifier('voir_archives')) {
            return redirect('Le forum n\'est plus accessible.', $this->generateUrl('zco_forum_index'), MSG_ERROR);
        }

        $nbSujetsParPage = 30;
        $CompterSujets = ForumDAO::CompterSujets($id);
        $NombreDePages = ceil($CompterSujets / $nbSujetsParPage);
        //TODO zCorrecteurs::VerifierFormatageUrl($InfosForum['cat_nom'], true, false, $NombreDePages);
        $page = (int)$request->get('p', $NombreDePages);

        if ($page > $NombreDePages)
            throw new NotFoundHttpException();

        if ($page < $NombreDePages) {
            \Page::$titre .= ' - Page ' . $page;
            \Page::$description .= ' - Page ' . $page;
        }

        // On récupère la liste des numéros des pages.
        $tableau_pages = liste_pages($page, $NombreDePages, $this->generateUrl('zco_forum_show', ['id' => $id, 'slug' => rewrite($InfosForum['cat_nom'])]) . '?p=%s', true);
        $debut = ($NombreDePages - $page) * $nbSujetsParPage;

        // On récupère les sujets du forum depuis la fonction du modèle.
        $ListerSujets = ForumDAO::ListerSujets($debut, $nbSujetsParPage, $id);

        $Lu = array();
        $Pages = array();

        if ($ListerSujets) {
            foreach ($ListerSujets as $clef => $valeur) {
                $Lu[$clef] = ForumDAO::LuNonluForum([
                    'lunonlu_utilisateur_id' => $valeur['lunonlu_utilisateur_id'],
                    'lunonlu_sujet_id' => $valeur['lunonlu_sujet_id'],
                    'lunonlu_message_id' => $valeur['lunonlu_message_id'],
                    'lunonlu_participe' => $valeur['lunonlu_participe'],
                    'sujet_dernier_message' => $valeur['message_id'],
                    'date_dernier_message' => $valeur['message_timestamp'],
                ]);

                // Liste des pages
                $nbMessagesParPage = 20;
                $NombreDePagesSujet = ceil(($valeur['sujet_reponses'] + 1) / $nbMessagesParPage);
                $Pages[$clef] = liste_pages(-1, $NombreDePagesSujet, $this->generateUrl('zco_topic_show', ['id' => $valeur['sujet_id'], 'slug' => rewrite($valeur['sujet_titre'])]) . '?p=%s');
            }
        }

        $SautRapide = ForumDAO::RecupererSautRapide($id);

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
        if (!empty($_GET['trash']) && empty($_GET['archives'])) {
            $msgFil = 'Liste des sujets dans la corbeille';
        } else if (empty($_GET['trash']) && !empty($_GET['archives'])) {
            $msgFil = 'Liste des forums archivés';
        } else {
            $msgFil = 'Liste des sujets';
        }
        fil_ariane($id, $msgFil);
        $this->get('zco_core.resource_manager')->requireResources([
            '@ZcoCoreBundle/Resources/public/css/zcode.css',
            '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
            '@ZcoContentBundle/Resources/public/css/forum.css',
            '@ZcoCoreBundle/Resources/public/js/messages.js'
        ]);

        return $this->render('ZcoContentBundle:Forum:forum.html.php', [
            'InfosForum' => $InfosForum,
            'CompterSujets' => $CompterSujets,
            'Lu' => $Lu,
            'tableau_pages' => $tableau_pages,
            'ListerSujets' => $ListerSujets,
            'Pages' => $Pages,
            'SautRapide' => $SautRapide,
            'ListerUneCategorie' => $ListerUneCategorie ?? null,
            'LuForum' => $LuForum,
            'Parent' => $parent,
        ]);
    }
}