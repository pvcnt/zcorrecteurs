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
use Zco\Bundle\UserBundle\Domain\UserDAO;

/**
 * Contrôleur gérant le blog.
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
            $params['id_categorie'] = (int)$categoryId;
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

    public function showAction($id, $slug, Request $request)
    {
        $Auteurs = BlogDAO::InfosBillet($id);
        if (!$Auteurs) {
            throw new NotFoundHttpException();
        }
        $InfosBillet = $Auteurs[0];

        $credentials = new BlogCredentials($Auteurs, $InfosBillet);
        if (!$credentials->canView()) {
            throw new AccessDeniedHttpException();
        }

        //TODO zCorrecteurs::VerifierFormatageUrl($InfosBillet['version_titre'], true, true, 1);

        //Si le billet est un article virtuel.
        if (!is_null($InfosBillet['blog_url_redirection']) && !empty($InfosBillet['blog_url_redirection'])) {
            if ($InfosBillet['blog_etat'] == BLOG_VALIDE) {
                BlogDAO::BlogIncrementerVues($id);
            }
            return new RedirectResponse(htmlspecialchars($InfosBillet['blog_url_redirection']), 301);
        }

        $url = $this->generateUrl('zco_blog_show', ['id' => $id, 'slug' => rewrite($InfosBillet['version_titre'])]);

        //Si on veut voir un commentaire en particulier
        $commentId = $request->get('c');
        if ($commentId) {
            $page = CommentDAO::TrouverPageCommentaire($commentId, $id);
            if (!$page !== false) {
                throw new NotFoundHttpException();
            }
            $url = ($page > 1) ? $url . '?page=' . $page : $url;
            return new RedirectResponse($url . '#m' . $commentId, 301);
        }

        $page = (int)$request->get('page', 1);
        if ($page > 1) {
            \Page::$titre .= ' - Page ' . $page;
            \Page::$description .= ' - Page ' . $page;
        }

        $ListerCommentaires = CommentDAO::ListerCommentairesBillet($id, $page);
        $CompterCommentaires = CommentDAO::CompterCommentairesBillet($id);
        $NombrePages = ceil($CompterCommentaires / self::PER_PAGE);
        $ListePages = liste_pages($page, $NombrePages, $url . '?page=%s#commentaires');

        //On marque les commentaires comme lus s'il y en a
        if (!empty($ListerCommentaires) && verifier('connecte'))
            CommentDAO::MarquerCommentairesLus($InfosBillet, $ListerCommentaires);

        $InfosBillet['blog_etat'] == BLOG_VALIDE && BlogDAO::BlogIncrementerVues($id);

        fil_ariane($InfosBillet['cat_id'], [
            htmlspecialchars($InfosBillet['version_titre']) => $url,
            'Lecture du billet',
        ]);
        \Page::$titre = htmlspecialchars($InfosBillet['version_titre']);
        \Page::$description = htmlspecialchars(strip_tags($InfosBillet['version_intro']));
        $this->get('zco_core.resource_manager')->requireResources(array(
            '@ZcoContentBundle/Resources/public/css/forum.css',
            '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
        ));

        return $this->render('ZcoBlogBundle::show.html.php', [
            'InfosBillet' => $InfosBillet,
            'Auteurs' => $Auteurs,
            'CompterCommentaires' => $CompterCommentaires,
            'ListerCommentaires' => $ListerCommentaires,
            'ListePages' => $ListePages,
            'credentials' => $credentials,
        ]);
    }

    public function manageAction($id, $slug, Request $request)
    {
        $Auteurs = BlogDAO::InfosBillet($id);
        if (!$Auteurs) {
            throw new NotFoundHttpException();
        }
        $InfosBillet = $Auteurs[0];

        $credentials = new BlogCredentials($Auteurs, $InfosBillet);
        if (!$credentials->canView()) {
            throw new NotFoundHttpException();
        }

        $authorized = $credentials->isAllowed() || $credentials->canEdit() || $InfosBillet['blog_etat'] != BLOG_VALIDE;
        if (!$authorized) {
            throw new AccessDeniedHttpException();
        }

        $url = $this->generateUrl('zco_blog_manage', ['id' => $InfosBillet['blog_id']]);

        //--- Si on veut ajouteur un auteur ---
        if (isset($_POST['ajouter_auteur']) && ($credentials->isOwner() || verifier('blog_toujours_createur'))) {
            $InfosUtilisateur = UserDAO::InfosUtilisateur($_POST['pseudo']);
            if (!empty($InfosUtilisateur)) {
                BlogDAO::AjouterAuteur($id, $InfosUtilisateur['utilisateur_id'], $_POST['statut']);
                return redirect('L\'auteur a bien été ajouté.', $url);
            } else {
                return redirect('Ce membre n\'existe pas.', $url, MSG_ERROR);
            }
        }

        //--- Si on veut changer de logo ---
        if (!empty($_POST['image']) && $credentials->canEdit()) {
            $urlimage = BlogDAO::AjouterBilletImage($id, $_POST['image']);
            if ($urlimage[0] === false) {
                if ($urlimage[1] == 0)
                    return redirect('Erreur : pas de fichier à uploader ?', '', MSG_ERROR); // pas de fichier à uploader
                elseif ($urlimage[1] == 1)
                    return redirect('Erreur serveur : transfert bloqué à cause de l\'extension.', '', MSG_ERROR); // extension inconnue
                elseif ($urlimage[1] == 2)
                    return redirect('Erreur serveur : impossible d\'enregistrer l\'image.', '', MSG_ERROR); // imagepng fail
                else
                    exit('unknown code');
            }

            BlogDAO::EditerBillet($id, array('image' => $urlimage[1]));
            return redirect('Le logo de ce billet a bien été changé.', $url);
        }

        //--- Si on veut changer la date de publication ---
        if (isset($_POST['changer_date']) && verifier('blog_valider')) {
            BlogDAO::EditerBillet($id, array(
                'date_publication' => $_POST['date_pub']
            ));
            return redirect('La date de publication de ce billet a bien été changée.', $url);
        }

        fil_ariane($InfosBillet['cat_id'], [
            htmlspecialchars($InfosBillet['version_titre']) => $this->generateUrl('zco_blog_show', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre'])]),
            'Modification du billet'
        ]);
        \Page::$titre = htmlspecialchars($InfosBillet['version_titre']);
        $Etats = [
            BLOG_BROUILLON => 'Brouillon',
            BLOG_PREPARATION => 'En cours de préparation',
            BLOG_PROPOSE => 'Proposé',
            BLOG_REFUSE => 'Refusé',
            BLOG_VALIDE => 'Validé',
        ];

        return $this->render('ZcoBlogBundle::adminBillet.html.php', [
            'Auteurs' => $Auteurs,
            'InfosBillet' => $InfosBillet,
            'Etats' => $Etats,
            'credentials' => $credentials,
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
        \Page::$titre = 'Ajouter un billet';

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
        $status = $request->get('etat', '');
        if ($status) {
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
                BLOG_VALIDE => 'Validé',
            ],
            'status' => $status,
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
        $credentials = new BlogCredentials($Auteurs, $InfosBillet);
        $authorized = verifier('blog_editer_commentaires')
            || ($InfosCommentaire['utilisateur_id'] == $_SESSION['id'] && verifier('blog_supprimer_ses_commentaire'))
            || ($credentials->isOwner() && $InfosBillet['blog_etat'] != BLOG_VALIDE);
        if (!$authorized) {
            throw new AccessDeniedHttpException();
        }

        if ($request->isMethod('POST')) {
            CommentDAO::SupprimerCommentaire($id);

            return redirect(
                'Le commentaire a bien été supprimé.',
                $this->generateUrl('zco_blog_show', ['id' => $InfosCommentaire['blog_id'], 'slug' => rewrite($InfosCommentaire['version_titre'])]));
        }

        \Page::$titre = htmlspecialchars($InfosCommentaire['version_titre']) . ' - Supprimer un commentaire';
        fil_ariane($InfosBillet['cat_id'], [
            htmlspecialchars($InfosBillet['version_titre']) => $this->generateUrl('zco_blog_show', ['id' => $id, 'slug' => rewrite($InfosBillet['version_titre'])]),
            'Supprimer un commentaire'
        ]);

        return $this->render('ZcoBlogBundle::deleteComment.html.php', [
            'InfosBillet' => $InfosBillet,
            'InfosCommentaire' => $InfosCommentaire,
        ]);
    }

    public function newComment($id, Request $request)
    {
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
        $Auteurs = BlogDAO::InfosBillet($id);
        if (!$Auteurs) {
            throw new NotFoundHttpException();
        }
        $InfosBillet = $Auteurs[0];
        $credentials = new BlogCredentials($Auteurs, $InfosBillet);
        if (!$credentials->canView()) {
            throw new AccessDeniedHttpException();
        }

        if ($request->isMethod('POST')) {
            $id = CommentDAO::AjouterCommentaire($id, $_SESSION['id'], $_POST['texte']);

            return redirect(
                'Le commentaire a bien été ajouté.',
                $this->generateUrl('zco_blog_show', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre'])])
            );
        }

        //Si on veut citer un message.
        $commentId = $request->get('c');
        if ($commentId) {
            $InfosCommentaire = CommentDAO::InfosCommentaire($commentId);
            $texte_zform = '<citation nom="' . htmlspecialchars($InfosCommentaire['utilisateur_pseudo']) . '">' .
                $InfosCommentaire['commentaire_texte'] . '' .
                '</citation>';
        } else {
            $texte_zform = '';
        }

        fil_ariane($InfosBillet['cat_id'], array(
            htmlspecialchars($InfosBillet['version_titre']) => $this->generateUrl('zco_blog_show', ['id' => $InfosBillet['blog_id'], 'slug' => rewrite($InfosBillet['version_titre'])]),
            'Ajouter un commentaire'
        ));
        $this->get('zco_core.resource_manager')->requireResources([
            '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
            '@ZcoContentBundle/Resources/public/css/forum.css',
        ]);
        \Page::$titre = htmlspecialchars($InfosBillet['version_titre']) . ' - Ajouter un commentaire';

        return $this->render('ZcoBlogBundle::ajouterCommentaire.html.php', [
            'InfosBillet' => $InfosBillet,
            'ListerCommentaires' => CommentDAO::ListerCommentairesBillet($id, -1),
            'texte_zform' => $texte_zform,
        ]);

    }

    public function editCommentAction($id, Request $request)
    {
        $InfosCommentaire = CommentDAO::InfosCommentaire($id);
        if (!$InfosCommentaire) {
            throw new NotFoundHttpException();
        }

        $authorized = (
            ($InfosCommentaire['utilisateur_id'] == $_SESSION['id'] && verifier('blog_editer_ses_commentaires', $InfosCommentaire['blog_id_categorie']))
            || verifier('blog_editer_commentaires', $InfosCommentaire['blog_id_categorie']));
        if (!$authorized) {
            throw new AccessDeniedHttpException();
        }

        if ($request->isMethod('POST')) {
            // On a envoyé le nouveau commentaire.
            CommentDAO::EditerCommentaire($id, $_SESSION['id'], $_POST['texte']);

            return redirect(
                'Le commentaire a bien été édité.',
                $this->generateUrl('zco_blog_show', ['id' => $InfosCommentaire['blog_id'], 'slug' => rewrite($InfosCommentaire['version_titre'])]) . '#commentaires'
            );
        }

        \Page::$titre = htmlspecialchars($InfosCommentaire['version_titre']) . ' - Modifier un commentaire';
        fil_ariane($InfosCommentaire['blog_id_categorie'], [
            htmlspecialchars($InfosCommentaire['version_titre']) => $this->generateUrl('zco_blog_show', ['id' => $InfosCommentaire['blog_id'], 'slug' => rewrite($InfosCommentaire['version_titre'])]),
            'Modifier un commentaire'
        ]);

        return $this->render('ZcoBlogBundle::editComment.html.php', [
            'InfosCommentaire' => $InfosCommentaire,
        ]);
    }

    public function editAuthorAction($id, $authorId, Request $request)
    {
        $Auteurs = BlogDAO::InfosBillet($id);
        if (!$Auteurs) {
            throw new NotFoundHttpException();
        }
        $InfosBillet = $Auteurs[0];
        $credentials = new BlogCredentials($Auteurs, $InfosBillet);
        if (!$credentials->isOwner() && !verifier('blog_toujours_createur')) {
            throw new AccessDeniedHttpException;
        }
        $url = $this->generateUrl('zco_blog_manage', ['id' => $InfosBillet['blog_id']]);

        if ($request->isMethod('POST')) {
            $InfosUtilisateur = UserDAO::InfosUtilisateur($_POST['pseudo']);
            if (!$InfosUtilisateur) {
                return redirect('Ce membre n\'existe pas.', $url, MSG_ERROR);
            }
            BlogDAO::EditerAuteur($authorId, $id, $InfosUtilisateur['utilisateur_id'], $_POST['statut']);

            return redirect('L\'auteur a bien été modifié.', $url);
        }

        $InfosUtilisateur = UserDAO::InfosUtilisateur($authorId);
        foreach ($Auteurs as $a) {
            if ($a['utilisateur_id'] == $authorId)
                $InfosUtilisateur['auteur_statut'] = $a['auteur_statut'];
        }

        \Page::$titre = htmlspecialchars($InfosBillet['version_titre']) . ' - Modifier un auteur';
        fil_ariane($InfosBillet['blog_id_categorie'], [
            htmlspecialchars($InfosBillet['version_titre']) => $url,
            'Modifier un auteur'
        ]);

        return $this->render('ZcoBlogBundle::editerAuteur.html.php', [
            'InfosBillet' => $InfosBillet,
            'InfosUtilisateur' => $InfosUtilisateur,
        ]);
    }

    public function deleteAuthorAction($id, $authorId, Request $request)
    {
        $Auteurs = BlogDAO::InfosBillet($id);
        if (!$Auteurs) {
            throw new NotFoundHttpException();
        }
        $InfosBillet = $Auteurs[0];
        $credentials = new BlogCredentials($Auteurs, $InfosBillet);
        if (!$credentials->isOwner() && !verifier('blog_toujours_createur')) {
            throw new AccessDeniedHttpException;
        }
        $url = $this->generateUrl('zco_blog_manage', ['id' => $InfosBillet['blog_id']]);

        if ($request->isMethod('POST')) {
            BlogDAO::SupprimerAuteur($authorId, $id);
            return redirect('L\'auteur a bien été supprimé.', $url);
        }

        $InfosUtilisateur = UserDAO::InfosUtilisateur($authorId);
        \Page::$titre = htmlspecialchars($InfosBillet['version_titre']) . ' - Supprimer un auteur';
        fil_ariane($InfosBillet['blog_id_categorie'], [
            htmlspecialchars($InfosBillet['version_titre']) => $url,
            'Supprimer un auteur'
        ]);

        return $this->render('ZcoBlogBundle::supprimerAuteur.html.php', [
            'InfosBillet' => $InfosBillet,
            'InfosUtilisateur' => $InfosUtilisateur,
        ]);
    }

    public function unpublishAction($id, Request $request)
    {
        $Auteurs = BlogDAO::InfosBillet($id);
        if (!$Auteurs) {
            throw new NotFoundHttpException();
        }
        $InfosBillet = $Auteurs[0];
        $credentials = new BlogCredentials($Auteurs, $InfosBillet);
        if (!$credentials->canUnpublish()) {
            throw new AccessDeniedHttpException;
        }

        if ($request->isMethod('POST')) {
            BlogDAO::EditerBillet($id, array('etat' => BLOG_BROUILLON));

            return redirect('Le billet a bien été dévalidé.', $this->generateUrl('zco_blog_mine'));
        }

        \Page::$titre = htmlspecialchars($InfosBillet['version_titre']) . ' - Dévalider le billet';
        fil_ariane($InfosBillet['cat_id'], [
            htmlspecialchars($InfosBillet['version_titre']) => $this->generateUrl('zco_blog_show', ['id' => $id, 'slug' => rewrite($InfosBillet['version_titre'])]),
            'Dévalider le billet'
        ]);

        return $this->render('ZcoBlogBundle::unpublish.html.php', [
            'InfosBillet' => $InfosBillet,
        ]);
    }

    public function publishAction($id, Request $request)
    {
        $Auteurs = BlogDAO::InfosBillet($id);
        if (!$Auteurs) {
            throw new NotFoundHttpException();
        }
        $InfosBillet = $Auteurs[0];
        $credentials = new BlogCredentials($Auteurs, $InfosBillet);
        if (!$credentials->canPublish()) {
            throw new AccessDeniedHttpException();
        }

        if ($request->isMethod('POST')) {
            BlogDAO::ValiderBillet($id, isset($_POST['conserver_date_pub']));

            return redirect('Le billet a bien été validé.', $this->generateUrl('zco_blog_mine'));
        }

        fil_ariane($InfosBillet['cat_id'], array(
            htmlspecialchars($InfosBillet['version_titre']) => $this->generateUrl('zco_blog_show', ['id' => $id, 'slug' => rewrite($InfosBillet['version_titre'])]),
            'Valider le billet'
        ));
        \Page::$titre = htmlspecialchars($InfosBillet['version_titre']) . ' - Valider le billet';

        return $this->render('ZcoBlogBundle::publish.html.php', ['InfosBillet' => $InfosBillet]);
    }

    public function historyAction($id)
    {
        $Auteurs = BlogDAO::InfosBillet($id);
        if (!$Auteurs) {
            throw new NotFoundHttpException();
        }
        $InfosBillet = $Auteurs[0];
        $credentials = new BlogCredentials($Auteurs, $InfosBillet);
        if (!$credentials->isAllowed()) {
            throw new AccessDeniedHttpException();
        }

        fil_ariane($InfosBillet['cat_id'], [
            htmlspecialchars($InfosBillet['version_titre']) => $this->generateUrl('zco_blog_manage', ['id' => $id]),
            'Voir l\'historique des versions',
        ]);
        \Page::$titre = htmlspecialchars($InfosBillet['version_titre']) . ' - Historique des versions';

        return $this->render('ZcoBlogBundle::versions.html.php', [
            'InfosBillet' => $InfosBillet,
            'ListerVersions' => BlogDAO::ListerVersions($id),
        ]);
    }

    public function editAction($id, Request $request)
    {
        $Auteurs = BlogDAO::InfosBillet($id);
        if (!$Auteurs) {
            throw new NotFoundHttpException();
        }
        $InfosBillet = $Auteurs[0];
        $credentials = new BlogCredentials($Auteurs, $InfosBillet);
        if (!$credentials->canEdit()) {
            throw new AccessDeniedHttpException();
        }

        if ($request->isMethod('POST')) {
            if (empty($_POST['titre']) || empty($_POST['intro']) || empty($_POST['texte']))
                return redirect(
                    'Vous devez remplir tous les champs nécessaires !',
                    $this->generateUrl('zco_blog_edit', ['id' => $id, 'slug' => rewrite($InfosBillet['version_titre'])]),
                    MSG_ERROR
                );

            BlogDAO::EditerBillet($id, [
                'titre' => $_POST['titre'],
                'sous_titre' => $_POST['sous_titre'],
                'intro' => $_POST['intro'],
                'texte' => $_POST['texte'],
                'id_categorie' => $_POST['categorie'],
                'lien_nom' => $_POST['lien_nom'],
                'lien_url' => $_POST['lien_url'],
                'commentaire' => $_POST['commentaire'],
            ]);

            return redirect(
                'Le billet a bien été édité.',
                $this->generateUrl('zco_blog_manage', ['id' => $id])
            );
        }

        fil_ariane($InfosBillet['cat_id'], [
            htmlspecialchars($InfosBillet['version_titre']) => $this->generateUrl('zco_blog_manage', ['id' => $id]),
            'Modifier le billet',
        ]);
        \Page::$titre = htmlspecialchars($InfosBillet['version_titre']) . ' - Modifier le billet';

        return $this->render('ZcoBlogBundle::editer.html.php', [
            'InfosBillet' => $InfosBillet,
            'Categories' => CategoryDAO::ListerEnfants(CategoryDAO::GetIDCategorieCourante()),
        ]);
    }

    public function deleteAction($id, Request $request)
    {
        $Auteurs = BlogDAO::InfosBillet($id);
        if (!$Auteurs) {
            throw new NotFoundHttpException();
        }
        $InfosBillet = $Auteurs[0];
        $credentials = new BlogCredentials($Auteurs, $InfosBillet);
        if (!$credentials->canDelete()) {
            throw new AccessDeniedHttpException();
        }

        if ($request->isMethod('POST')) {
            BlogDAO::SupprimerBillet($id);

            return redirect('Le billet a bien été supprimé.', $this->generateUrl('zco_blog_mine'));
        }

        fil_ariane($InfosBillet['cat_id'], [
            htmlspecialchars($InfosBillet['version_titre']) => $this->generateUrl('zco_blog_manage', ['id' => $id]),
            'Supprimer le billet',
        ]);
        \Page::$titre = htmlspecialchars($InfosBillet['version_titre']) . ' - Supprimer le billet';

        return $this->render('ZcoBlogBundle::supprimer.html.php', [
            'InfosBillet' => $InfosBillet,
        ]);
    }

    public function compareAction($from, $to)
    {
        $infos_new = BlogDAO::InfosVersion($from);
        $infos_old = BlogDAO::InfosVersion($to);

        if (empty($infos_new) || empty($infos_old)) {
            throw new NotFoundHttpException();
        }
        if ($infos_new['version_id_billet'] != $infos_old['version_id_billet']) {
            throw new NotFoundHttpException();
        }

        $Auteurs = BlogDAO::InfosBillet($infos_new['version_id_billet']);
        if (!$Auteurs) {
            throw new NotFoundHttpException();
        }
        $InfosBillet = $Auteurs[0];
        $credentials = new BlogCredentials($Auteurs, $InfosBillet);
        if (!$credentials->canView() && !$credentials->isAllowed()) {
            throw new AccessDeniedHttpException();
        }

        $texte_new = $infos_new['version_texte'];
        $texte_old = $infos_old['version_texte'];
        $intro_new = $infos_new['version_intro'];
        $intro_old = $infos_old['version_intro'];

        $diff_intro = $this->diff($intro_old, $intro_new);
        $diff_texte = $this->diff($texte_old, $texte_new);

        fil_ariane($InfosBillet['cat_id'], [
            htmlspecialchars($InfosBillet['version_titre']) => $this->generateUrl('zco_blog_manage', ['id' => $InfosBillet['blog_id']]),
            'Historique des versions' => $this->generateUrl('zco_blog_history', ['id' => $InfosBillet['blog_id']]),
            'Comparaison',
        ]);
        \Page::$titre = htmlspecialchars($InfosBillet['version_titre']) . ' - Historique des versions';
        $this->get('zco_core.resource_manager')->requireResource(
            '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css'
        );

        return $this->render('ZcoBlogBundle::comparaison.html.php', [
            'infos_old' => $infos_old,
            'infos_new' => $infos_new,
            'diff_intro' => $diff_intro,
            'diff_texte' => $diff_texte,
        ]);
    }

    /**
     * Réalise un diff entre deux chaines de caractères.
     *
     * @param string $old L'ancienne chaine de caractères.
     * @param string $new La nouvelle chaine de caractères.
     * @return string
     */
    private function diff($old, $new)
    {
        include_once(BASEPATH . '/lib/diff/diff.php');
        include_once(BASEPATH . '/lib/diff/htmlformatter.php');

        $old = explode("\n", strip_tags($old));
        $new = explode("\n", strip_tags($new));

        $diff = new \Diff($old, $new);
        $formatter = new \HTMLDiffFormatter();

        return $formatter->format($diff);
    }
}
