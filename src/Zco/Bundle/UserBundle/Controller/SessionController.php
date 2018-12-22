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

namespace Zco\Bundle\UserBundle\Controller;

use Zco\Bundle\UserBundle\Form\Type\FormLoginType;
use Zco\Bundle\UserBundle\Form\Type\CreateUserType;
use Zco\Bundle\UserBundle\Form\Handler\CreateUserHandler;
use Zco\Bundle\UserBundle\Form\Handler\FormLoginHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Contrôleur gérant les actions liées aux comptes membres et aux sessions.
 *
 * @author Savageman <savageman@zcorrecteurs.fr>
 *         DJ Fox <djfox@zcorrecteurs.fr>
 *         Barbatos <barbatos@f1m.fr>
 *         vincent1870 <vincent@zcorrecteurs.fr>
 */
class SessionController extends Controller
{
	/**
	 * Affiche le formulaire permettant à un utilisateur de se connecter au 
	 * site et gère son traitement.
	 *
	 * @author Savageman <savageman@zcorrecteurs.fr>
	 *         Barbatos <barbatos@f1m.fr>
	 *         vincent1870 <vincent@zcorrecteurs.fr>
	 */
	public function loginAction(Request $request)
	{
		if ($this->get('zco_user.user')->isAuthenticated())
		{
			throw new AccessDeniedHttpException('Vous êtes déjà connecté.');
		}
		
		$form    = $this->createForm(FormLoginType::class);
		$handler = new FormLoginHandler($form, $request, $this->get('zco_user.user'));
		
		if ($handler->process())
		{
			//Redirection vers l'adresse de provenance, l'accueil si on venait 
			//du formulaire de connexion.
			if (!isset($_SERVER['HTTP_REFERER']))
			{
				$ref = '/';
			}
			else
			{
				$ref = strpos($_SERVER['HTTP_REFERER'], $this->generateUrl('zco_user_session_login')) !== false
					? '/' : $_SERVER['HTTP_REFERER'];
			}
			
			return redirect('Vous êtes maintenant connecté à votre compte.', $ref);
		}
		
		//Paramétrage de la vue.
		\Page::$titre = 'Se connecter sur zCorrecteurs.fr';
		fil_ariane('Se connecter');
		
		return $this->render('ZcoUserBundle:Session:login.html.php', array(
			'form' => $form->createView(),
		));
	}

	/**
	 * Déconnecte un utilisateur.
	 *
	 * @author vincent1870 <vincent@zcorrecteurs.fr>
	 * @param  Request $request
	 */
	public function logoutAction(Request $request)
	{
		if ($this->get('zco_user.user')->isAuthenticated()
			&& $request->query->has('token') && !empty($_SESSION['token']) 
			&& $request->query->get('token') == $_SESSION['token'])
		{
			$this->get('zco_user.user')->logout();
			
			return redirect('Vous êtes maintenant déconnecté de votre compte. À bientôt !', '/');
		}
		
		throw new AccessDeniedHttpException('Vous n\'êtes pas connecté.');
	}

	/**
	 * Gère la régénération de mots de passe.
	 *
	 * @author Savageman <savageman@zcorrecteurs.fr>
	 *         vincent1870 <vincent@zcorrecteurs.fr>
	 */
	public function newPasswordAction(Request $request)
	{
		// Validation du nouveau pass
		if ($request->query->has('hash'))
		{
			if (\Doctrine_Core::getTable('Utilisateur')->confirmNewPassword($request->query->get('hash')))
			{
				return redirect('Le mot de passe que vous avez reçu par email a été '.
					'activé, vous pouvez désormais vous identifier sur le site.', 
					$this->generateUrl('zco_user_session_login'));
			}
			
			return redirect('Erreur lors de l’activation du nouveau mot de passe', 
				'/', MSG_ERROR);
		}

		// On a demandé un nouveau mot de passe.
		if ($request->getMethod() === 'POST' && $request->request->has('email'))
		{
			$infos = \Doctrine_Core::getTable('Utilisateur')->generateNewPassword($request->request->get('email'));
			if (false !== $infos)
			{
				$message = $this->renderView('ZcoUserBundle:Mail:newPassword.html.php', array(
					'mdp'  => $infos[0],
					'hash' => $infos[1],
				));
				send_mail($_POST['email'], $_POST['email'], '[zCorrecteurs.fr] Nouveau mot de passe', $message);
				
				return redirect('Un courriel vous a été envoyé à l\'adresse indiquée.', 
					$this->generateUrl('zco_user_session_login'));
			}
			
			return redirect('L’adresse courriel est inconnue (ou bien vous n’avez pas encore validé votre compte)…', 
				$this->generateUrl('zco_user_session_newPassword'), MSG_ERROR);
		}
		
		\Page::$titre = 'Nouveau mot de passe';
		\Page::$description = 'Si vous avez perdu votre mot de passe, nous pouvons vous en renvoyer un nouveau';
		
		return $this->render('ZcoUserBundle:Session:newPassword.html.php');
	}
	
	/**
	 * Confirmation de l'inscription au site.
	 *
	 * @author Savageman <savageman@zcorrecteurs.fr>
	 *         vincent1870 <vincent@zcorrecteurs.fr>
	 * @param integer $id L'identifiant du compte à valider
	 * @param string $hash Le code de confirmation
	 */
	public function confirmAccountAction($id, $hash)
	{
		if (\Doctrine_Core::getTable('Utilisateur')->confirmAccount($id, $hash))
		{
			return redirect('Votre compte a été activé avec succès. Vous pouvez '
				.'dès maintenant profiter du site !', '/');
		}
		
		return redirect('Soit votre compte est déjà actif, soit le code de '
			.'confirmation donné est incorrect.', '/', MSG_ERROR);
	}
	
	/**
	 * Affiche le formulaire d'inscription et traite les demandes.
	 *
	 * @author DJ Fox <djfox@zcorrecteurs.fr>
	 *         Savageman <savageman@zcorrecteurs.fr>
	 *         vincent1870 <vincent@zcorrecteurs.fr>
	 */
	public function registerAction(Request $request)
	{
		if ($this->get('zco_user.user')->isAuthenticated())
		{
			throw new AccessDeniedHttpException('Vous êtes déjà inscrit et connecté.');
		}
		
		$user    = new \Utilisateur;
		$form    = $this->createForm(CreateUserType::class);
		$handler = new CreateUserHandler($form, $request, $this->get('event_dispatcher'));
		
		if ($handler->process($user))
		{
			return redirect('Vous êtes maintenant inscrit. Votre compte est pour l\'instant '
				.'inactif.<br />N\'oubliez pas d\'aller valider votre inscription en cliquant '
				.'sur le lien dans le courriel qui vous a été envoyé !', '/');
		}
		
		//Paramétrage de la vue.
		\Page::$titre = 'S\'inscrire sur zCorrecteurs.fr';
		fil_ariane('Créer un nouveau compte');
		
		return $this->render('ZcoUserBundle:Session:register.html.php', array(
			'form' => $form->createView(),
		));
	}
}
