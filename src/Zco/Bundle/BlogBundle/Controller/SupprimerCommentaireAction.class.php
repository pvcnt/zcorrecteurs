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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contrôleur gérant la suppression d'un commentaire sur un billet du blog.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class SupprimerCommentaireAction extends BlogActions
{
    public function execute()
    {
        zCorrecteurs::VerifierFormatageUrl(null, true);

        //Si on a bien demandé à supprimer un commentaire
        if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
            //On récupère des infos sur le commentaire
            $InfosCommentaire = InfosCommentaire($_GET['id']);
            $Auteurs = InfosBillet($InfosCommentaire['commentaire_id_billet']);
            $InfosBillet = $Auteurs[0];
            $createur = false;
            foreach ($Auteurs as $a) {
                if ($a['utilisateur_id'] == $_SESSION['id'] && $a['auteur_statut'] == 3) {
                    $createur = true;
                }
            }

            Page::$titre = htmlspecialchars($InfosCommentaire['version_titre']) . ' - Supprimer un commentaire';

            //Si on a bien le droit de supprimer le commentaire
            if (verifier('blog_supprimer_commentaires') || ($InfosCommentaire['utilisateur_id'] == $_SESSION['id'] && verifier('blog_supprimer_ses_commentaire')) || ($createur == true && $nfosBillet['blog_etat'] != BLOG_VALIDE)) {
                //Si on veut le supprimer
                if (isset($_POST['confirmer'])) {
                    SupprimerCommentaire($_GET['id']);
                    return redirect(
                        'Le commentaire a bien été supprimé.',
                        'billet-' . $InfosCommentaire['blog_id'] . '-' . rewrite($InfosCommentaire['version_titre']) . '.html');
                } //Si on annule
                elseif (isset($_POST['annuler']))
                    return new RedirectResponse('billet-' . $InfosCommentaire['blog_id'] . '-' . rewrite($InfosCommentaire['version_titre']) . '.html');

                //Inclusion de la vue
                fil_ariane($InfosBillet['cat_id'], array(
                    htmlspecialchars($InfosBillet['version_titre']) => 'billet-' . $InfosCommentaire['blog_id'] . '-' . rewrite($InfosBillet['version_titre']) . '.html',
                    'Supprimer un commentaire'
                ));
                return render_to_response(array(
                    'InfosBillet' => $InfosBillet,
                    'InfosCommentaire' => $InfosCommentaire,
                ));
            } else
                throw new AccessDeniedHttpException();
        } else
            throw new NotFoundHttpException();
    }
}
