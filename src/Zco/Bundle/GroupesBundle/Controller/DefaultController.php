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

namespace Zco\Bundle\GroupesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\CategoriesBundle\Domain\CategoryDAO;
use Zco\Bundle\GroupesBundle\Domain\CredentialsDAO;
use Zco\Bundle\GroupesBundle\Domain\GroupDAO;

/**
 * Contrôleur gérant les actions sur les groupes et les droits.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DefaultController extends Controller
{
    /**
     * Affiche la liste des groupes.
     */
    public function indexAction()
    {
        if (!verifier('groupes_gerer')) {
            throw new AccessDeniedHttpException();
        }
        \Page::$titre = 'Gestion des groupes';

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

        if ($request->isMethod('POST')) {
            GroupDAO::AjouterGroupe($request->request->all());

            return redirect('Le groupe a bien été ajouté.', $this->generateUrl('zco_groups_index'));
        }

        $ListerGroupes = array_filter(GroupDAO::ListerGroupes(), function ($group) {
            return $group['groupe_code'] != \Groupe::ANONYMOUS;
        });
        \Page::$titre = 'Ajouter un groupe';

        return render_to_response('ZcoGroupesBundle::new.html.php', [
            'ListerGroupes' => $ListerGroupes,
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

        if ($request->isMethod('POST')) {
            GroupDAO::EditerGroupe($id, $request->request->all());

            return redirect('Le groupe a bien été modifié.', $this->generateUrl('zco_groups_index'));
        }

        \Page::$titre = 'Modifier un groupe';

        return render_to_response('ZcoGroupesBundle::edit.html.php', [
            'InfosGroupe' => $InfosGroupe,
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

        \Page::$titre = 'Supprimer un groupe';

        return render_to_response('ZcoGroupesBundle::delete.html.php', [
            'InfosGroupe' => $InfosGroupe,
        ]);
    }

    /**
     * Vérifier la liste des droits attribués à un groupe.
     */
    public function checkCredentialsAction($id)
    {
        if (!verifier('groupes_changer_droits')) {
            throw new AccessDeniedHttpException();
        }

        $InfosGroupe = $this->getGroupOrThrow($id);
        $Droits = CredentialsDAO::VerifierDroitsGroupe($id);

        \Page::$titre = 'Vérification des droits d\'un groupe';

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
        \Page::$titre = 'Changer un membre de groupe';

        include_once(__DIR__ . '/../../UserBundle/modeles/utilisateurs.php');
        if (null !== $id) {
            $InfosUtilisateur = InfosUtilisateur($id);
            if (!$InfosUtilisateur) {
                throw new NotFoundHttpException();
            }
        } elseif ($request->request->has('pseudo')) {
            $InfosUtilisateur = InfosUtilisateur($request->request->get('pseudo'));
            if (!$InfosUtilisateur) {
                $_SESSION['erreur'][] = 'Ce membre n\'existe pas.';
                unset($InfosUtilisateur);
            }
        }

        if (isset($InfosUtilisateur)) {
            if (isset($_POST['groupe'])) {
                $this->getGroupOrThrow($_POST['groupe']);
                GroupDAO::ChangerGroupeUtilisateur($InfosUtilisateur['utilisateur_id'], $_POST['groupe']);
                $this->get('cache')->save('dernier_refresh_droits', time(), 0);

                return redirect(
                    'Le membre a bien été changé de groupe.',
                    $this->generateUrl('zco_groups_assign', ['id' => $InfosUtilisateur['utilisateur_id']])
                );
            }
            if (isset($_POST['changement_groupes_secondaires'])) {
                GroupDAO::ModifierGroupesSecondairesUtilisateur(
                    $_GET['id'],
                    isset($_POST['groupes_secondaires']) ? $_POST['groupes_secondaires'] : array()
                );
                $this->get('cache')->save('dernier_refresh_droits', time(), 0);

                return redirect(
                    'Le membre a bien été changé de groupe.',
                    $this->generateUrl('zco_groups_assign', ['id' => $InfosUtilisateur['utilisateur_id']])
                );
            }

            $ListerGroupes = array_filter(GroupDAO::ListerGroupes(), function ($group) {
                return $group['groupe_code'] != \Groupe::ANONYMOUS;
            });
            $GroupesSecondaires = GroupDAO::ListerGroupesSecondairesUtilisateur($InfosUtilisateur['utilisateur_id']);
            $ListerGroupesSecondaires = GroupDAO::ListerGroupesSecondaires();
            $temp = array();
            foreach ($GroupesSecondaires as $groupe) {
                $temp[] = $groupe['groupe_id'];
            }
            $GroupesSecondaires = $temp;
        } else {
            $ListerGroupes = null;
            $InfosUtilisateur = null;
            $GroupesSecondaires = null;
        }

        $pseudo = isset($InfosUtilisateur) ? $InfosUtilisateur['utilisateur_pseudo'] : '';

        return render_to_response('ZcoGroupesBundle::assign.html.php', [
            'ListerGroupes' => $ListerGroupes,
            'ListerGroupesSecondaires' => $ListerGroupesSecondaires ?? null,
            'pseudo' => $pseudo,
            'InfosUtilisateur' => $InfosUtilisateur,
            'GroupesSecondaires' => $GroupesSecondaires,
        ]);
    }

    /**
     * Modifie la liste des droits attribués à un groupe.
     */
    public function changeCredentialsAction($id, Request $request)
    {
        if (!verifier('groupes_changer_droits')) {
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

            $ValeurDroit = CredentialsDAO::RecupererValeurDroit($_GET['id2'], $InfosGroupe['groupe_id']);
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

        //Si on veut modifier
        if (isset($_POST['modifier']) && !empty($InfosDroit) && !empty($InfosGroupe)) {
            //En cas de droit simple (sans sélection de catégorie)
            if (!$InfosDroit['droit_choix_binaire'] && !$InfosDroit['droit_choix_categorie']) {
                CredentialsDAO::EditerDroitGroupe($InfosGroupe['groupe_id'], $InfosDroit['droit_id_categorie'], $_GET['id2'], (int)$_POST['valeur']);
            } elseif (!$InfosDroit['droit_choix_categorie']) {
                CredentialsDAO::EditerDroitGroupe($InfosGroupe['groupe_id'], $InfosDroit['droit_id_categorie'], $_GET['id2'], isset($_POST['valeur']) ? 1 : 0);
            } else {
                //Sinon droit appliquable par catégorie
                if (empty($_POST['cat'])) {
                    $_POST['cat'] = array();
                }

                foreach ($ListerEnfants as $e) {
                    if (in_array($e['cat_id'], $_POST['cat'])) {
                        //Si on doit ajouter le droit
                        $valeur = $InfosDroit['droit_choix_binaire'] ? 1 : (int)$_POST['valeur'];
                        CredentialsDAO::EditerDroitGroupe($InfosGroupe['groupe_id'], $e['cat_id'], $_GET['id2'], $valeur);
                    } else {
                        //Sinon on le retire.
                        CredentialsDAO::EditerDroitGroupe($InfosGroupe['groupe_id'], $e['cat_id'], $_GET['id2'], 0);
                    }
                }
            }

            // Suppression des caches.
            $this->get('cache')->delete('droits_groupe_' . $InfosGroupe['groupe_id']);

            return redirect(
                'Le droit de ce groupe a bien été mis à jour.',
                'droits-' . $_GET['id'] . '-' . $_GET['id2'] . '.html'
            );
        }

        \Page::$titre = 'Changement des droits d\'un groupe';
        $this->get('zco_core.resource_manager')->requireResource(
            '@ZcoCoreBundle/Resources/public/css/zcode.css'
        );

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
}
