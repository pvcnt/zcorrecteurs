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

namespace Zco\Bundle\GroupesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\ContentBundle\Domain\CategoryDAO;
use Zco\Bundle\GroupesBundle\Domain\CredentialsDAO;
use Zco\Bundle\GroupesBundle\Domain\GroupDAO;
use Zco\Bundle\GroupesBundle\Form\GroupType;
use Zco\Bundle\UserBundle\Domain\UserDAO;

/**
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
final class DefaultController extends Controller
{
    /**
     * Affiche la liste des groupes.
     */
    public function indexAction()
    {
        if (!verifier('groupes_gerer')) {
            throw new AccessDeniedHttpException();
        }
        \Page::$titre = 'Groupes';

        return render_to_response('ZcoGroupesBundle::index.html.php', [
            'ListerGroupes' => GroupDAO::ListerGroupes(),
            'ListerGroupesSecondaires' => GroupDAO::ListerGroupesSecondaires(),
        ]);
    }

    /**
     * Ajoute un nouveau groupe.
     */
    public function newAction(Request $request)
    {
        if (!verifier('groupes_gerer')) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(GroupType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            GroupDAO::AjouterGroupe($form->getData());

            return redirect('Le groupe a bien été ajouté.', $this->generateUrl('zco_groups_index'));
        }

        \Page::$titre = 'Créer un groupe';
        fil_ariane([
            'Groupes' => $this->generateUrl('zco_groups_index'),
            'Créer un groupe',
        ]);

        return render_to_response('ZcoGroupesBundle::new.html.php', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Modifie un groupe.
     */
    public function editAction($id, Request $request)
    {
        if (!verifier('groupes_gerer')) {
            throw new AccessDeniedHttpException();
        }

        $InfosGroupe = $this->getGroupOrThrow($id);
        $form = $this->createForm(GroupType::class, [
            'nom' => $InfosGroupe['groupe_nom'],
            'logo' => $InfosGroupe['groupe_logo'],
            'logo_feminin' => $InfosGroupe['groupe_logo_feminin'],
            'class' => $InfosGroupe['groupe_class'],
            'sanction' => (bool)$InfosGroupe['groupe_sanction'],
            'team' => (bool)$InfosGroupe['groupe_team'],
            'secondaire' => (bool)$InfosGroupe['groupe_secondaire'],
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            GroupDAO::EditerGroupe($id, $form->getData());

            return redirect('Le groupe a bien été modifié.', $this->generateUrl('zco_groups_index'));
        }

        \Page::$titre = htmlspecialchars($InfosGroupe['groupe_nom']) . '- Modifier le groupe';
        fil_ariane([
            'Groupes' => $this->generateUrl('zco_groups_index'),
            htmlspecialchars($InfosGroupe['groupe_nom']),
        ]);

        return render_to_response('ZcoGroupesBundle::edit.html.php', [
            'InfosGroupe' => $InfosGroupe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime un groupe.
     */
    public function deleteAction($id, Request $request)
    {
        if (!verifier('groupes_gerer')) {
            throw new AccessDeniedHttpException();
        }

        $InfosGroupe = $this->getGroupOrThrow($id);

        if ($request->isMethod('POST')) {
            GroupDAO::SupprimerGroupe($id);
            $this->get('cache')->save('dernier_refresh_droits', time(), 0);

            return redirect('Le groupe a bien été supprimé.', $this->generateUrl('zco_groups_index'));
        }

        \Page::$titre = htmlspecialchars($InfosGroupe['groupe_nom']) . ' - Supprimer le groupe';
        fil_ariane([
            'Groupes' => $this->generateUrl('zco_groups_index'),
            htmlspecialchars($InfosGroupe['groupe_nom']) => $this->generateUrl('zco_groups_edit', ['id' => $InfosGroupe['groupe_id']]),
            'Supprimer',
        ]);

        return render_to_response('ZcoGroupesBundle::delete.html.php', [
            'InfosGroupe' => $InfosGroupe,
        ]);
    }

    /**
     * Vérifier la liste des droits attribués à un groupe.
     */
    public function checkCredentialsAction($id)
    {
        if (!verifier('groupes_gerer')) {
            throw new AccessDeniedHttpException();
        }

        $InfosGroupe = $this->getGroupOrThrow($id);
        $Droits = CredentialsDAO::VerifierDroitsGroupe($id);

        \Page::$titre = htmlspecialchars($InfosGroupe['groupe_nom']) . ' - Vérifier les droits';
        fil_ariane([
            'Groupes' => $this->generateUrl('zco_groups_index'),
            htmlspecialchars($InfosGroupe['groupe_nom']) => $this->generateUrl('zco_groups_edit', ['id' => $InfosGroupe['groupe_id']]),
            'Vérifier les droits',
        ]);

        return render_to_response('ZcoGroupesBundle::checkCredentials.html.php', [
            'InfosGroupe' => $InfosGroupe,
            'Droits' => $Droits,
        ]);
    }

    /**
     * Change un membre de groupe.
     */
    public function assignAction($id, Request $request)
    {
        if (!verifier('groupes_changer_membre')) {
            throw new AccessDeniedHttpException();
        }

        $InfosUtilisateur = $this->getUserOrThrow($id);
        if ($request->isMethod('POST')) {
            $this->getGroupOrThrow($_POST['groupe']);
            $noop = true;
            if ($_POST['groupe'] != $InfosUtilisateur['utilisateur_id_groupe']) {
                GroupDAO::ChangerGroupeUtilisateur($InfosUtilisateur['utilisateur_id'], $_POST['groupe']);
                $noop = false;
            }
            if (isset($_POST['groupes_secondaires'])) {
                GroupDAO::ModifierGroupesSecondairesUtilisateur(
                    $InfosUtilisateur['utilisateur_id'],
                    $_POST['groupes_secondaires']
                );
                $noop = false;
            }
            if ($noop) {
                return redirect(
                    'Le groupe de ce membre n\'a pas été changé.',
                    $this->generateUrl('zco_groups_assign', ['id' => $InfosUtilisateur['utilisateur_id']])
                );
            }

            $this->get('cache')->save('dernier_refresh_droits', time(), 0);

            return redirect(
                'Le membre a bien été changé de groupe.',
                $this->generateUrl('zco_groups_assign', ['id' => $InfosUtilisateur['utilisateur_id']])
            );
        }

        \Page::$titre = 'Changer de groupe - ' . htmlspecialchars($InfosUtilisateur['utilisateur_pseudo']);
        fil_ariane([
            'Membres' => $this->generateUrl('zco_user_index'),
            htmlspecialchars($InfosUtilisateur['utilisateur_pseudo']) => $this->generateUrl('zco_user_profile', ['id' => $InfosUtilisateur['utilisateur_id'], 'slug' => rewrite($InfosUtilisateur['utilisateur_pseudo'])]),
            'Modifier les droits',
        ]);

        $ListerGroupes = array_filter(GroupDAO::ListerGroupes(), function ($group) {
            return $group['groupe_code'] != \Groupe::ANONYMOUS;
        });
        $GroupesSecondaires = GroupDAO::ListerGroupesSecondairesUtilisateur($InfosUtilisateur['utilisateur_id']);
        $GroupesSecondaires = array_column($GroupesSecondaires, 'groupe_id');
        $ListerGroupesSecondaires = GroupDAO::ListerGroupesSecondaires();

        return render_to_response('ZcoGroupesBundle::assign.html.php', [
            'ListerGroupes' => $ListerGroupes,
            'ListerGroupesSecondaires' => $ListerGroupesSecondaires,
            'InfosUtilisateur' => $InfosUtilisateur,
            'GroupesSecondaires' => $GroupesSecondaires,
        ]);
    }

    /**
     * Modifie la liste des droits attribués à un groupe.
     */
    public function changeCredentialsAction($id, Request $request)
    {
        if (!verifier('groupes_gerer')) {
            throw new AccessDeniedHttpException();
        }

        $InfosGroupe = $this->getGroupOrThrow($id);
        $ListerGroupes = array_merge(GroupDAO::ListerGroupes(), GroupDAO::ListerGroupesSecondaires());
        $ListerDroits = CredentialsDAO::ListerDroits();

        // Infos sur le droit si besoin.
        $InfosDroit = null;
        if ($request->query->has('credential')) {
            $InfosDroit = CredentialsDAO::InfosDroit($request->query->get('credential'));
            if (!$InfosDroit) {
                throw new NotFoundHttpException();
            }
        }

        // Listage des catégories nécessaires et récupération de la valeur du droit si besoin.
        $ValeurNumerique = null;
        if (!empty($InfosDroit) && !empty($InfosGroupe)) {
            $ListerEnfants = null;
            if ($InfosDroit['droit_choix_categorie'] == 1) {
                $ListerEnfants = CategoryDAO::ListerEnfants($InfosDroit, true);
            }

            $ValeurDroit = CredentialsDAO::RecupererValeurDroit($InfosDroit['droit_id'], $InfosGroupe['groupe_id']);
            if (!$InfosDroit['droit_choix_categorie'] && !empty($ValeurDroit) && $InfosDroit['droit_choix_binaire']) {
                $ValeurDroit = $ValeurDroit[0];
            } elseif (!$InfosDroit['droit_choix_categorie'] && !empty($ValeurDroit) && !$InfosDroit['droit_choix_binaire']) {
                $ValeurNumerique = $ValeurDroit[0]['gd_valeur'];
            } elseif ($InfosDroit['droit_choix_categorie'] && !$InfosDroit['droit_choix_binaire']) {
                foreach ($ValeurDroit as $d) {
                    if ($d['gd_valeur'] != 0)
                        $ValeurNumerique = $d['gd_valeur'];
                }
            } else {
                $ValeurNumerique = '';
            }
        } else {
            $ValeurDroit = null;
            $ListerEnfants = null;
            $ValeurNumerique = null;
        }

        // Si on veut modifier
        if ($request->isMethod('POST') && !empty($InfosDroit) && !empty($InfosGroupe)) {
            // En cas de droit simple (sans sélection de catégorie)
            if (!$InfosDroit['droit_choix_categorie']) {
                $value = $InfosDroit['droit_choix_binaire']
                    ? $request->request->has('valeur') ? 1 : 0
                    : (int)$request->request->get('valeur');
                CredentialsDAO::EditerDroitGroupe(
                    $InfosGroupe['groupe_id'],
                    $InfosDroit['droit_id_categorie'],
                    $InfosDroit['droit_id'],
                    $value
                );
            } else {
                // Sinon droit appliquable par catégorie.
                $categories = $request->request->get('cat', []);
                foreach ($ListerEnfants as $e) {
                    if (in_array($e['cat_id'], $categories)) {
                        // Si on doit ajouter le droit.
                        $value = $InfosDroit['droit_choix_binaire'] ? 1 : (int)$request->request->get('valeur');
                        CredentialsDAO::EditerDroitGroupe(
                            $InfosGroupe['groupe_id'],
                            $e['cat_id'],
                            $InfosDroit['droit_id'],
                            $value
                        );
                    } else {
                        // Sinon on le retire.
                        CredentialsDAO::EditerDroitGroupe(
                            $InfosGroupe['groupe_id'],
                            $e['cat_id'],
                            $InfosDroit['droit_id'],
                            0
                        );
                    }
                }
            }

            // Suppression des caches.
            $this->get('cache')->delete('droits_groupe_' . $InfosGroupe['groupe_id']);

            return redirect(
                'Le droit de ce groupe a bien été mis à jour.',
                $this->generateUrl('zco_groups_changeCredentials', ['id' => $InfosGroupe['groupe_id'], 'credential' => $InfosDroit['droit_id']])
            );
        }

        \Page::$titre = htmlspecialchars($InfosGroupe['groupe_nom']) . ' - Modifier les droits';
        fil_ariane([
            'Groupes' => $this->generateUrl('zco_groups_index'),
            htmlspecialchars($InfosGroupe['groupe_nom']) => $this->generateUrl('zco_groups_edit', ['id' => $InfosGroupe['groupe_id']]),
            'Modifier les droits',
        ]);

        return render_to_response('ZcoGroupesBundle::changeCredentials.html.php', [
            'InfosGroupe' => $InfosGroupe,
            'ListerGroupes' => $ListerGroupes,
            'ListerDroits' => $ListerDroits,
            'InfosDroit' => $InfosDroit,
            'ListerEnfants' => $ListerEnfants,
            'ValeurDroit' => $ValeurDroit,
            'ValeurNumerique' => $ValeurNumerique,
        ]);
    }

    private function getGroupOrThrow($id)
    {
        $group = GroupDAO::InfosGroupe($id);
        if (!$group) {
            throw new NotFoundHttpException();
        }

        return $group;
    }

    private function getUserOrThrow($id)
    {
        $user = UserDAO::InfosUtilisateur($id);
        if (!$user) {
            throw new NotFoundHttpException();
        }

        return $user;
    }
}
