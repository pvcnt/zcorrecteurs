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

namespace Zco\Bundle\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\BlogBundle\Domain\BlogDAO;
use Zco\Bundle\BlogBundle\Domain\CommentDAO;
use Zco\Bundle\ContentBundle\Domain\CategoryDAO;

/**
 * Contrôleur gérant l'accueil du blog.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DefaultController extends Controller
{
    const PER_PAGE = 15;

    public function indexAction(Request $request)
    {
        $page = (int)$request->get('page', 1);
        $NombreDeBillet = BlogDAO::CompterListerBilletsEnLigne();
        $NombreDePage = ceil($NombreDeBillet / self::PER_PAGE);
        if ($page > 1) {
            \Page::$titre .= ' - Page ' . $page;
        }

        $params = [
            'lecteurs' => false,
            'etat' => BLOG_VALIDE,
            'futur' => false,
        ];
        $categoryId = $request->get('filtre', null);
        if (null !== $categoryId) {
            $category = CategoryDAO::InfosCategorie($categoryId);
            if (!$category) {
                throw new NotFoundHttpException();
            }
            $params['id_categorie'] = (int) $categoryId;
        }
        [$ListerBillets, $BilletsAuteurs] = BlogDAO::ListerBillets($params, $page, self::PER_PAGE);
        $Categories = CategoryDAO::ListerEnfants(CategoryDAO::InfosCategorie($categoryId ?: CategoryDAO::GetIDCategorieCourante()));
        $ListePage = liste_pages($page, $NombreDePage, $this->generateUrl('zco_blog_index') . '?page=%s');

        fil_ariane('Liste des derniers billets');

        return $this->render('ZcoBlogBundle::index.html.php', [
            'Categories' => $Categories,
            'NombreDeBillet' => $NombreDeBillet,
            'ListerBillets' => $ListerBillets,
            'BilletsAuteurs' => $BilletsAuteurs,
            'ListePage' => $ListePage,
        ]);
    }

    /**
     * Ajout d'un nouveau billet.
     *
     * @return Response
     */
    public function newAction()
    {
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
        \Page::$titre .= ' - Ajouter un billet';

        //Si on a posté un nouveau billet
        if (isset($_POST['submit'])) {
            if (!empty($_POST['titre']) && !empty($_POST['texte']) && !empty($_POST['intro'])) {
                BlogDAO::AjouterBillet();

                return redirect('Le billet a bien été ajouté.', $this->generateUrl('zco_blog_mine'));
            }
            return redirect('Vous devez remplir tous les champs nécessaires !', '', MSG_ERROR);
        }
        fil_ariane(['Mes billets' => $this->generateUrl('zco_blog_mine'), 'Ajouter un billet']);

        return $this->render('ZcoBlogBundle::new.html.php', [
            'Categories' => CategoryDAO::ListerEnfants(CategoryDAO::GetIDCategorieCourante())
        ]);
    }

    public function mineAction(Request $request)
    {
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
        \Page::$titre = 'Mes billets';

        $params = ['id_utilisateur' => $_SESSION['id']];
        $status = $request->get('etat', null);
        if (null !== $status) {
            $params['etat'] = (int)$status;
        }
        list($ListerBillets, $BilletsAuteurs) = BlogDAO::ListerBillets($params);

        return $this->render('ZcoBlogBundle::mine.html.php', [
            'ListerBillets' => $ListerBillets,
            'BilletsAuteurs' => $BilletsAuteurs,
            'AuteursClass' => [3 => 'gras', 2 => 'normal', 1 => 'italique'],
            'Etats' => [
                BLOG_BROUILLON => 'Brouillon',
                BLOG_PREPARATION => 'En cours de préparation',
                BLOG_PROPOSE => 'Proposé',
                BLOG_REFUSE => 'Refusé',
                BLOG_VALIDE => 'Validé'
            ],
        ]);
    }

    /**
     * Suppression d'un commentaire.
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function deleteCommentAction($id, Request $request)
    {
        $InfosCommentaire = CommentDAO::InfosCommentaire($id);
        if (!$InfosCommentaire) {
            throw new NotFoundHttpException();
        }
        $Auteurs = BlogDAO::InfosBillet($InfosCommentaire['commentaire_id_billet']);
        if (!$Auteurs) {
            throw new NotFoundHttpException();
        }
        $InfosBillet = $Auteurs[0];
        $createur = false;
        foreach ($Auteurs as $a) {
            if ($a['utilisateur_id'] == $_SESSION['id'] && $a['auteur_statut'] == 3) {
                $createur = true;
            }
        }

        $authorized = verifier('blog_editer_commentaires')
            || ($InfosCommentaire['utilisateur_id'] == $_SESSION['id'] && verifier('blog_supprimer_ses_commentaire'))
            || ($createur && $InfosBillet['blog_etat'] != BLOG_VALIDE);
        if (!$authorized) {
            throw new AccessDeniedHttpException();
        }

        if ($request->isMethod('POST')) {
            CommentDAO::SupprimerCommentaire($id);

            return redirect(
                'Le commentaire a bien été supprimé.',
                'billet-' . $InfosCommentaire['blog_id'] . '-' . rewrite($InfosCommentaire['version_titre']) . '.html');
        }

        \Page::$titre = htmlspecialchars($InfosCommentaire['version_titre']) . ' - Supprimer un commentaire';
        fil_ariane($InfosBillet['cat_id'], [
            htmlspecialchars($InfosBillet['version_titre']) => 'billet-' . $InfosCommentaire['blog_id'] . '-' . rewrite($InfosBillet['version_titre']) . '.html',
            'Supprimer un commentaire'
        ]);

        return $this->render('ZcoBlogBundle::deleteComment.html.php', [
            'InfosBillet' => $InfosBillet,
            'InfosCommentaire' => $InfosCommentaire,
        ]);
    }

    public function editCommentAction($id)
    {
        $InfosCommentaire = CommentDAO::InfosCommentaire($id);
        if (!$InfosCommentaire) {
            throw new NotFoundHttpException();
        }

        $authorized = (
                ($InfosCommentaire['utilisateur_id'] == $_SESSION['id'] && verifier('blog_editer_ses_commentaires', $InfosCommentaire['blog_id_categorie']))
                || verifier('blog_editer_commentaires', $InfosCommentaire['blog_id_categorie']))
            && ($InfosCommentaire['blog_commentaires'] == COMMENTAIRES_OK || verifier('blog_poster_commentaires_fermes', $InfosCommentaire['blog_id_categorie']));
        if (!$authorized) {
            throw new AccessDeniedHttpException();
        }

        if (!empty($_POST['submit'])) {
            // On a envoyé le nouveau commentaire.
            CommentDAO::EditerCommentaire($id, $_SESSION['id'], $_POST['texte']);

            return redirect(
                'Le commentaire a bien été édité.',
                'billet-' . $InfosCommentaire['blog_id'] . '-' . $id . '-' . rewrite($InfosCommentaire['version_titre']) . '.html#commentaires'
            );
        }

        \Page::$titre = htmlspecialchars($InfosCommentaire['version_titre']) . ' - Modifier un commentaire';
        fil_ariane($InfosCommentaire['blog_id_categorie'], [
            htmlspecialchars($InfosCommentaire['version_titre']) => 'billet-' . $InfosCommentaire['blog_id'] . '-' . rewrite($InfosCommentaire['version_titre']) . '.html',
            'Modifier un commentaire'
        ]);

        return $this->render('ZcoBlogBundle::editComment.html.php', [
            'InfosCommentaire' => $InfosCommentaire,
        ]);
    }
}
