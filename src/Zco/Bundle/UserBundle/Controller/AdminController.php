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

namespace Zco\Bundle\UserBundle\Controller;

use Zco\Bundle\UserBundle\Form\Type\BannedEmailType;
use Zco\Bundle\UserBundle\Form\Type\WarningType;
use Zco\Bundle\UserBundle\Form\Type\PunishmentType;
use Zco\Bundle\UserBundle\Form\Type\AnswerNewUsernameType;
use Zco\Bundle\UserBundle\Form\Handler\WarningHandler;
use Zco\Bundle\UserBundle\Form\Handler\PunishmentHandler;
use Zco\Bundle\UserBundle\Form\Handler\AnswerNewUsernameHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant les actions liées aux membres, leur connexion, 
 * leur déconnexion.
 *
 * @author Savageman <savageman@zcorrecteurs.fr>
 *         Ziame <ziame@zcorrecteurs.fr>
 *         Barbatos
 *         vincent1870 <vincent@zcorrecteurs.fr>
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
		if (!verifier('gerer_comptes_valides'))
		{
			throw new AccessDeniedHttpException;
		}
		if (!($user = \Doctrine_Core::getTable('Utilisateur')->getById($id)))
		{
			throw new NotFoundHttpException('Cet utilisateur n\'existe pas.');
		}
		
		$user->setAccountValid((boolean) $status);
		$user->save();
		
		return redirect('Le compte a bien été '.($status ? '' : 'dé').'validé.', 
			$this->generateUrl('zco_user_profile', array('id' => $id, 'slug' => rewrite($user->getUsername()))));
	}
	
	/**
	 * Affiche la liste des blocages de connexion suite à des tentatives ratées 
	 * trop nombreuses.
	 *
	 * @param  integer $page
	 * @return Response
	 */
	public function blocagesAction($page = 1)
	{
		if (!verifier('lister_blocages'))
		{
			throw new AccessDeniedHttpException;
		}
		
		$query = \Doctrine_Core::getTable('Tentative')->getByBlockedQuery();
		
		$paginator = $this->get('knp_paginator');
		$blocages = $paginator->paginate($query, $page, 20);
		$blocages->setUsedRoute('zco_user_blocages');
		
		//Paramétrage de la vue.
		\Page::$titre = 'Tentatives de connexion ratées';
		
		return render_to_response('ZcoUserBundle:Admin:blocages.html.php', compact('blocages'));
	}

	/**
	 * Modifie le niveau d'avertissement d'un membre.
	 *
	 * @param  Request $request
	 * @param  integer $id
	 * @return Response
	 */
	public function warnAction(Request $request, $id = null)
	{
		if (!verifier('membres_avertir'))
		{
			throw new AccessDeniedHttpException;
		}
		
		$warning = new \UserWarning;
		$warning->setPercentage(0);
		$warning->setAdminId($_SESSION['id']);
		if ($id)
		{
			if (!($user = \Doctrine_Core::getTable('Utilisateur')->getById($id)))
			{
				throw new NotFoundHttpException('Cet utilisateur n\'existe pas.');
			}
			$warning->setUser($user);
		}
		else
		{
			$user = null;
		}
		
		$form = $this->get('form.factory')->create(new WarningType);
		$handler = new WarningHandler($form, $request);
		if ($handler->process($warning))
		{
			return redirect('Le niveau d\'avertissement du membre a bien été mis à jour.', 
				$this->generateUrl('zco_user_profile', array('id' => $warning->getUserId(), 'slug' => rewrite($warning->getUser()->getUsername()))));
		}

		\Page::$titre = 'Niveau d\'avertissement';
		
		return render_to_response('ZcoUserBundle:Admin:warn.html.php', array(
			'user' => $user,
			'form' => $form->createView(),
		));
	}

	/**
	 * Affiche la liste des comptes non encore validés.
	 *
	 * @return Response
	 */
	public function unvalidAccountsAction()
	{
		if (!verifier('gerer_comptes_valides'))
		{
			throw new AccessDeniedHttpException;
		}
		
		\Page::$titre = 'Comptes en cours de validation';
		
		return render_to_response('ZcoUserBundle:Admin:unvalidAccounts.html.php', array(
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
		if (!verifier('rechercher_mail'))
		{
			throw new AccessDeniedHttpException;
		}
		
		if ($request->query->has('email'))
		{
			$email = $request->query->get('email');
			$users = \Doctrine_Core::getTable('Utilisateur')->getByEmail($email);
		}
		else
		{
			$users = null;
			$email = null;
		}
		
		\Page::$titre = 'Rechercher une adresse mail';
		
		return render_to_response('ZcoUserBundle:Admin:searchEmail.html.php', array(
			'users' => $users,
			'email' => $email,
		));
	}

	/**
	 * Bannit une nouvelle plage d'adresses mails.
	 *
	 * @param  Request $request
	 * @return Response
	 */
	public function newBannedEmailAction(Request $request)
	{
		if (!verifier('bannir_mails'))
		{
			throw new AccessDeniedHttpException;
		}
		
		$email = new \BannedEmail;
		$email->setUserId($_SESSION['id']);
		$form = $this->get('form.factory')->create(new BannedEmailType(), $email);
		
		if ($request->getMethod() === 'POST')
		{
			$form->submit($request);
			if ($form->isValid())
			{
				$email->save();
				
				return redirect('La plage spécifiée a bien été bannie.', 
					$this->get('router')->generate('zco_user_admin_bannedEmails'));
			}
		}

		\Page::$titre = 'Bannir une plage d\'adresses courriel';
		fil_ariane(array(
			'Adresses courriel bannies' => $this->get('router')->generate('zco_user_admin_bannedEmails'),
			'Nouvelle plage',
		));
		
		return render_to_response('ZcoUserBundle:Admin:newBannedEmail.html.php', 
			array('form' => $form->createView())
		);
	}

	/**
	 * Liste les plages d'adresses mails bannies et permet d'en débannir.
	 *
	 * @param  integer|null $id Identifiant d'une place d'adresses à débannir.
	 * @return Response
	 */
	public function bannedEmailsAction($id = null)
	{
		if (!verifier('bannir_mails'))
		{
			throw new AccessDeniedHttpException;
		}
		
		if ($id && $email = \Doctrine_Core::getTable('BannedEmail')->find($id))
		{
			$email->delete();
			
			return redirect('La plage spécifiée a bien été débannie.', 
				$this->get('router')->generate('zco_user_admin_bannedEmails'));
		}
		
		\Page::$titre = 'Adresses courriel bannies';
		
		return render_to_response('ZcoUserBundle:Admin:bannedEmails.html.php', array(
			'emails' => \Doctrine_Core::getTable('BannedEmail')->getAll(),
		));
	}

	/**
	 * Permet de sanctionner un membre.
	 * 
	 * @param  Request $request
	 * @param  integer|null $id
	 * @return Response
	 */
	public function punishAction(Request $request, $id = null)
	{
		if (!verifier('sanctionner'))
		{
			throw new AccessDeniedHttpException;
		}
		
		$punishment = new \UserPunishment;
		$punishment->setAdminId($_SESSION['id']);
		if ($id)
		{
			if (!($user = \Doctrine_Core::getTable('Utilisateur')->getById($id)))
			{
				throw new NotFoundHttpException('Cet utilisateur n\'existe pas.');
			}
			$punishment->setUser($user);
		}
		else
		{
			$user = null;
		}
		
		$form = $this->get('form.factory')->create(new PunishmentType);
		$handler = new PunishmentHandler($form, $request, $this->get('zco_core.cache'));
		if ($handler->process($punishment))
		{
			return redirect('Le membre a bien été sanctionné.', 
				$this->generateUrl('zco_user_profile', array('id' => $punishment->getUserId(), 'slug' => rewrite($punishment->getUser()->getUsername()))));
		}
		
		\Page::$titre = 'Sanctionner un membre';

		return render_to_response('ZcoUserBundle:Admin:punish.html.php', array(
			'user' => $user,
			'form' => $form->createView(),
		));
	}
	
	/**
	 * Annule une sanction en cours.
	 *
	 * @param  integer $id Identifiant de la sanction concernée
	 * @return Response
	 */
	public function cancelPunishmentAction($id)
	{
		if (!verifier('sanctionner'))
		{
			throw new AccessDeniedHttpException;
		}
		if (!($punishment = \Doctrine_Core::getTable('UserPunishment')->getById($id)))
		{
			throw new NotFoundHttpException('Cette sanction n\'existe pas.');
		}
		
		$punishment->complete();
		$punishment->getUser()->unapplyPunishment($punishment);
		
		return redirect('La sanction a bien pris fin.', 
			$this->generateUrl('zco_user_profile', array('id' => $punishment->getUserId(), 'slug' => rewrite($punishment->getUser()->getUsername()))));
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
		if (!verifier('suppr_comptes'))
		{
			throw new AccessDeniedHttpException;
		}
		if (!($user = \Doctrine_Core::getTable('Utilisateur')->getById($id)))
		{
			throw new NotFoundHttpException('Cet utilisateur n\'existe pas.');
		}
		
		if ($request->getMethod() === 'POST' && $request->request->has('confirm'))
		{
			$user->delete();
			
			return redirect('Le compte de l\'utilisateur a bien été supprimé.', 
				$this->generateUrl('zco_user_index'));
		}
		
		\Page::$titre = 'Supprimer un compte';
		
		return render_to_response('ZcoUserBundle:Admin:deleteAccount.html.php', 
			array('user' => $user));
	}
	
	/**
	 * Affiche la liste des membres avec un changement de pseudo en attente
	 */
	public function newPseudoQueriesAction()
	{
		if (!verifier('membres_valider_ch_pseudos'))
		{
			throw new AccessDeniedHttpException;
		}
		
		\Page::$titre = 'Changements de pseudonymes';
		
		return render_to_response('ZcoUserBundle:Admin:newPseudoQueries.html.php', array(
			'queries' => \Doctrine_Core::getTable('UserNewUsername')->getWaitingQueries()));
	}

	/**
	 * Répond à un changement de pseudo.
	 */
	public function newPseudoAnswerAction(Request $request, $id)
	{
		if (!verifier('membres_valider_ch_pseudos'))
		{
			throw new AccessDeniedHttpException;
		}
		if (!($query = \Doctrine_Core::getTable('UserNewUsername')->getById($id)))
		{
			throw new NotFoundHttpException('Cette demande n\'existe pas.');
		}
		
		$form = $this->get('form.factory')->create(new AnswerNewUsernameType);
		$handler = new AnswerNewUsernameHandler($form, $request);
		if ($handler->process($query))
		{
			return redirect('La réponse a bien été transmise au membre.', 
				$this->generateUrl('zco_user_profile', array('id' => $query->getUserId(), 'slug' => rewrite($query->getUser()->getUsername()))));
		}
		
		\Page::$titre = 'Répondre à une demande de changement de pseudonyme';
		
		return render_to_response('ZcoUserBundle:Admin:newPseudoAnswer.html.php', array(
			'query' => $query, 
			'form' => $form->createView(),
		));
	}
}
