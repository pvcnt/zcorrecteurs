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

namespace Zco\Bundle\UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Zco\Bundle\GroupesBundle\Domain\GroupDAO;
use Zco\Bundle\UserBundle\Form\Type\NewUsernameType;

/**
 */
class DefaultController extends Controller
{
    /**
     * Affiche la liste de tous les membres du site. Permet de filtrer ces
     * membres suivant divers critères (groupe, pseudo, etc.).
     *
     * @author vincent1870 <vincent@zcorrecteurs.fr>
     * @author Ziame <ziame@zcorrecteurs.fr>
     * @param  Request $request
     * @param  integer $page La page à afficher
     * @return Response
     */
    public function indexAction(Request $request, $page = 1)
    {
        $query = array();

        //Filtre par pseudo.
        $pseudo = '';
        $type = 1;
        if ($request->query->has('pseudo') && $request->query->get('pseudo') !== '') {
            $query['pseudo'] = $request->query->get('pseudo');
            $query['#pseudo_like'] = $request->query->has('type') ? (int)$request->query->get('type') : 1;
            $pseudo = $query['pseudo'];
            $type = $query['#pseudo_like'];
        }

        //Tri des résultats.
        $order = 'pseudo';
        $orderBy = 'asc';
        if ($request->query->has('tri')
            && in_array($request->query->get('tri'), array('id', 'pseudo', 'date_inscription', 'date_derniere_visite'))
            || ($request->query->get('tri') === 'forum_messages' && verifier('voir_nb_messages'))
        ) {
            $query['#order_by'] = $request->query->get('tri');
            $order = $query['#order_by'];
            if ($request->query->has('ordre') && strtolower($request->query->get('ordre')) === 'desc') {
                $query['#order_by'] = '-' . $query['#order_by'];
                $orderBy = 'desc';
            }
        } else {
            $query['#order_by'] = 'pseudo';
        }

        //Filtre par groupe.
        $group = null;
        $secondaryGroup = array();
        if ($request->query->has('groupe') && $request->query->get('groupe') !== '') {
            $query['group'] = (int)$request->query->get('groupe');
            $group = $query['group'];
        }
        if ($request->query->has('secondaire')) {
            $query['secondary_group'] = array_map('intval', (array)$request->query->get('secondaire'));
            $secondaryGroup = $query['secondary_group'];
        }

        //Pagination.
        $paginator = $this->get('knp_paginator');
        $users = $paginator->paginate(\Doctrine_Core::getTable('Utilisateur')->getQuery($query), $page, 30);
        $users->setUsedRoute('zco_user_indexWithPage');

        fil_ariane(null);
        \Page::$titre = 'Liste des membres';
        \Page::$description = 'Liste complète de tous les membres inscrits sur le site';
        if ($page > 1) {
            \Page::$titre .= ' - Page ' . $page;
            \Page::$description .= ' - Page ' . $page;
        }

        return $this->render('ZcoUserBundle::index.html.php', array(
            'users' => $users,
            'groups' => \Doctrine_Core::getTable('Groupe')->getApplicable(),
            'secondaryGroups' => \Doctrine_Core::getTable('Groupe')->getBySecondary(),

            'pseudo' => $pseudo,
            'type' => $type,
            'group' => $group,
            'secondaryGroup' => $secondaryGroup,
            'order' => $order,
            'orderBy' => $orderBy,
        ));
    }

    /**
     * Affiche le profil d'un membre.
     *
     * @author vincent1870 <vincent@zcorrecteurs.fr>
     * @param  Request $request
     * @param int $id
     * @param string $slug
     * @return Response
     */
    public function profileAction(Request $request, $id, $slug)
    {
        $user = \Doctrine_Core::getTable('Utilisateur')->getByIdFull($id);
        if (!$user) {
            throw new NotFoundHttpException('Cet utilisateur n\'existe pas.');
        }

        if ($slug !== rewrite($user->getUsername())) {
            // Redirect for SEO if slug is wrong.
            return new RedirectResponse($this->generateUrl('zco_user_profile', ['id' => $id, 'slug' => rewrite($user->getUsername())]), 301);
        }

        $vars = array('user' => $user);

        $firstChar = remove_accents($user->getUsername());
        $firstChar = strtolower($firstChar[0]);
        $art = in_array($firstChar, array('a', 'e', 'i', 'o', 'u', 'y')) ? "'" : 'e ';
        $vars['art'] = $art;

        if (verifier('membres_voir_ch_pseudos')) {
            $vars['newPseudo'] = \Doctrine_Core::getTable('UserNewUsername')->getByUserId($user->getId());
        }
        if (verifier('groupes_changer_membre') || $user->isTeam()) {
            $vars['ListerGroupes'] = GroupDAO::ListerChangementGroupeMembre($user->getId());
            if ($user->isTeam() && count($vars['ListerGroupes'])) {
                for ($i = count($vars['ListerGroupes']) - 1; $i >= 0; --$i) {
                    if (!$vars['ListerGroupes'][$i]['ancien_groupe_secondaire'] && !$vars['ListerGroupes'][$i]['nouveau_groupe_secondaire']) {
                        $vars['lastGroupChange'] = $vars['ListerGroupes'][$i]['chg_date'];
                        break;
                    }
                }
            }
            if ($user->isTeam() && empty($vars['lastGroupChange'])) {
                $vars['lastGroupChange'] = $user->getRegistrationDate();
            }
        }
        $vars['canSendEmail'] = verifier('rechercher_mail') || $user->isEmailDisplayed();
        $vars['canSeeInfos'] = verifier('membres_voir_ch_pseudos') || verifier('groupes_changer_membre');
        $vars['canAdmin'] = verifier('groupes_changer_membre') || verifier('options_editer_profils');
        $vars['own'] = $_SESSION['id'] == $user->getId();

        fil_ariane(['Profil d' . $art . htmlspecialchars($user->getUsername())]);
        \Page::$description = 'Pour en savoir plus sur la personnalité d' . $art . htmlspecialchars($user->getUsername()) . ' et son activité sur le site';

        return $this->render('ZcoUserBundle::profile.html.php', $vars);
    }

    /**
     * Demande un changement de pseudo.
     *
     * @param Request $request
     * @param integer|null $id
     * @return Response
     */
    public function newPseudoAction(Request $request, $id = null)
    {
        if ($id === null) {
            $id = $_SESSION['id'];
        }
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException();
        }
        if (!($user = \Doctrine_Core::getTable('Utilisateur')->getById($id))) {
            throw new NotFoundHttpException();
        }
        if (($id != $_SESSION['id']) && !verifier('membres_editer_pseudos')) {
            throw new AccessDeniedHttpException();
        }
        if ($id == $_SESSION['id'] && \Doctrine_Core::getTable('UserNewUsername')->hasWaitingQuery($user->getId())) {
            return redirect(
                'Vous avez déjà une demande changement de pseudonyme en attente.',
                $this->generateUrl('zco_options_index')
            );
        }

        $newUsername = new \UserNewUsername();
        $newUsername->setUser($user);
        $form = $this->createForm(NewUsernameType::class, $newUsername);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($newUsername->isAutoValidated()) {
                $newUsername->setAdmin($this->get('zco_user.user')->getEntity());
                $newUsername->save();
                $user->getUsername($newUsername->getNewUsername());
                $user->save();

                return redirect('Le pseudonyme a bien été changé.',
                    $this->generateUrl('zco_user_profile', ['id' => $user->getId(), 'slug' => rewrite($user->getUsername())])
                );
            }
            $newUsername->save();

            return redirect(
                'Votre demande de changement de pseudonyme a été enregistrée.',
                $this->generateUrl('zco_options_index')
            );
        }

        \Page::$titre = 'Demander un changement de pseudo';
        fil_ariane('Demander un changement de pseudo');

        return $this->render('ZcoUserBundle::newPseudo.html.php', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Affiche la liste des sauvegardes de zCode.
     *
     * @param Request $request HTTP request.
     * @param integer|null $textarea Identifiant HTML d'un élément où récupérer la sauvegarde.
     * @return Response
     */
    public function zformBackupsAction(Request $request, $textarea = null)
    {
        if (!verifier('connecte')) {
            throw new AccessDeniedHttpException('Vous devez être connecté pour accéder à cette page.');
        }
        \Page::$titre = 'Sauvegardes automatiques de zCode';
        fil_ariane('Voir mes textes sauvegardés');

        return $this->render('ZcoUserBundle::zformBackups.html.php', array(
            'backups' => $_SESSION['zform_backup'] ?? [],
            'xhr' => $request->query->get('xhr', false),
            'textarea' => $textarea,
        ));
    }
}