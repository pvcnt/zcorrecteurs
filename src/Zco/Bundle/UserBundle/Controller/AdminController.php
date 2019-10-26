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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\UserBundle\Form\Handler\AnswerNewUsernameHandler;
use Zco\Bundle\UserBundle\Form\Type\AnswerNewUsernameType;

/**
 *
 */
class AdminController extends Controller
{
    /**
     * Valide ou dévalide un compte utilisateur.
     *
     * @param  integer $id Identifiant du compte à (dé)valider
     * @param  boolean $status Valider ou dévalider ?
     * @return Response
     */
    public function validateAccountAction($id, $status)
    {
        if (!verifier('gerer_comptes_valides')) {
            throw new AccessDeniedHttpException;
        }
        if (!($user = \Doctrine_Core::getTable('Utilisateur')->getById($id))) {
            throw new NotFoundHttpException('Cet utilisateur n\'existe pas.');
        }
        $user->setAccountValid((boolean)$status);
        $user->save();

        return redirect('Le compte a bien été ' . ($status ? '' : 'dé') . 'validé.',
            $this->generateUrl('zco_user_profile', array('id' => $id, 'slug' => rewrite($user->getUsername()))));
    }

    /**
     * Affiche la liste des comptes non encore validés.
     *
     * @return Response
     */
    public function invalidAccountsAction()
    {
        if (!verifier('gerer_comptes_valides')) {
            throw new AccessDeniedHttpException;
        }
        \Zco\Page::$titre = 'Comptes en cours de validation';

        return $this->render('ZcoUserBundle:Admin:unvalidAccounts.html.php', array(
            'users' => \Doctrine_Core::getTable('Utilisateur')->getByNonValid(),
        ));
    }

    /**
     * Recherche une adresse mail en trouvant le(s) compte(s) l'utilisant.
     *
     * @param  Request $request
     * @return Response
     */
    public function searchEmailAction(Request $request)
    {
        if (!verifier('rechercher_mail')) {
            throw new AccessDeniedHttpException;
        }
        if ($request->query->has('email')) {
            $email = $request->query->get('email');
            $users = \Doctrine_Core::getTable('Utilisateur')->getByEmail($email);
        } else {
            $users = null;
            $email = null;
        }
        \Zco\Page::$titre = 'Rechercher une adresse mail';

        return $this->render('ZcoUserBundle:Admin:searchEmail.html.php', array(
            'users' => $users,
            'email' => $email,
        ));
    }

    /**
     * Supprime un compte.
     *
     * @param  Request $request
     * @param  integer $id
     * @return Response
     */
    public function deleteAccountAction(Request $request, $id)
    {
        if (!verifier('suppr_comptes')) {
            throw new AccessDeniedHttpException;
        }
        if (!($user = \Doctrine_Core::getTable('Utilisateur')->getById($id))) {
            throw new NotFoundHttpException('Cet utilisateur n\'existe pas.');
        }

        if ($request->getMethod() === 'POST' && $request->request->has('confirm')) {
            $user->delete();

            return redirect('Le compte de l\'utilisateur a bien été supprimé.',
                $this->generateUrl('zco_user_admin'));
        }

        \Zco\Page::$titre = 'Supprimer un compte';

        return $this->render('ZcoUserBundle:Admin:deleteAccount.html.php',
            array('user' => $user));
    }
}
