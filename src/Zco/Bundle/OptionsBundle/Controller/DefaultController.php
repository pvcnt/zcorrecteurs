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

namespace Zco\Bundle\OptionsBundle\Controller;

use Zco\Bundle\OptionsBundle\Form\Type\EditProfileType;
use Zco\Bundle\OptionsBundle\Form\Type\EditPreferencesType;
use Zco\Bundle\OptionsBundle\Form\Type\EditPasswordType;
use Zco\Bundle\OptionsBundle\Form\Type\EditEmailType;
use Zco\Bundle\OptionsBundle\Form\Model\EditPassword;
use Zco\Bundle\OptionsBundle\Form\Model\EditEmail;
use Zco\Bundle\OptionsBundle\Form\Handler\EditAvatarHandler;
use Zco\Bundle\OptionsBundle\Form\Handler\EditEmailHandler;
use Zco\Bundle\OptionsBundle\Form\Handler\EditPasswordHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contrôleur gérant les options membre.
 *
 * @author vincent1870, DJ Fox, Vanger, Barbatos
 */
class DefaultController extends Controller
{
	/**
	 * Modifie l'avatar en l'envoyant depuis l'ordinateur ou en le liant
	 * depuis une adresse web.
	 *
	 * @author DJ Fox <djfox@zcorrecteurs.fr>
	 * @author vincent1870 <vincent@zcorrecteurs.fr>
	 *
	 * @param Request $request
	 * @param integer $id
	 */
	public function avatarAction(Request $request, $id = null)
	{
		$user    = $this->getEditableUser($id);
		$own     = $user->getId() == $_SESSION['id'];
		$handler = new EditAvatarHandler($request, $this->get('imagine'));

		if (($retval = $handler->process($user)) !== false)
		{
			if (EditAvatarHandler::INTERNAL_ERROR === $retval)
			{
				return redirect(
					'Une erreur interne est survenue, veuillez réessayer.', 
					$this->generateUrl('zco_options_avatar', array('id' => $own ? null : $user->getId())), 
					MSG_ERROR
				);
			}
			elseif (EditAvatarHandler::WRONG_FORMAT === $retval)
			{
				return redirect(
					'Le format de votre fichier est invalide.', 
					$this->generateUrl('zco_options_avatar', array('id' => $own ? null : $user->getId())), 
					MSG_ERROR
				);
			}
			elseif (EditAvatarHandler::AVATAR_DELETED === $retval)
			{
				if ($own)
				{
					return redirect(
						'Votre avatar a bien été supprimé.', 
						$this->generateUrl('zco_options_avatar')
					);
				}

				return redirect(
					'L\'avatar a bien été supprimé.', 
					$this->generateUrl('zco_user_profile', array(
						'id' => $user->getId(), 
						'slug' => rewrite($user->getUsername()),
					))
				);
			}

			if ($own)
			{
				return redirect(
					'Votre avatar a bien été modifié.', 
					$this->generateUrl('zco_options_avatar')
				);
			}

			return redirect(
				'L\'avatar a bien été modifié.', 
				$this->generateUrl('zco_user_profile', array(
					'id' => $user->getId(), 
					'slug' => rewrite($user->getUsername()),
				))
			);
		}

		//Paramétrage de la vue
		fil_ariane('Modifier l\'avatar');
		\Page::$titre = 'Modifier l\'avatar';
		
		return render_to_response('ZcoOptionsBundle:Default:avatar.html.php', array(
			'user' => $user,
			'own'  => $own,
		));
	}

	/**
	 * Modifie l'adresse courriel.
	 *
	 * @author vincent1870 <vincent@zcorrecteurs.fr>
	 *
	 * @param Request $request
	 * @param integer $id
	 * @param string $hash Hash de validation d'une nouvelle adresse
	 */
	public function emailAction(Request $request, $id = null, $hash = null)
	{
		//Si on veut activer une nouvelle adresse.
		if ($hash && \Doctrine_Core::getTable('Utilisateur')->confirmEmail($hash))
		{
			return redirect('La nouvelle adresse courriel a bien été validée. Vous recevrez désormais tous les messages du site à cette adresse.', '/');
		}

		$user      = $this->getEditableUser($id);
		$own       = $user->getId() == $_SESSION['id'];
		$editEmail = new EditEmail($user);
		$form      = $this->createForm(new EditEmailType(), $editEmail, array('own' => $own));
		$handler   = new EditEmailHandler($form, $request);

		if ($handler->process($editEmail, $own))
		{
			if ($own)
			{
				return redirect(
					'La nouvelle adresse mail est pour l\'instant inactive.<br />'
					.'N\'oubliez pas d\'aller la valider en cliquant sur le lien '
					.'dans le courriel qui vous a été envoyé à cette dernière !', 
					$this->generateUrl('zco_options_email')
				);
			}
			
			return redirect(
				'Le mot de passe a bien été modifié.', 
				$this->generateUrl('zco_user_profile', array(
					'id' => $user->getId(), 
					'slug' => rewrite($user->getUsername()),
				))
			);
		}

		//Paramétrage de la vue.
		fil_ariane('Modifier l\'adresse courriel');
		\Page::$titre = 'Modifier l\'adresse courriel';
		
		return render_to_response('ZcoOptionsBundle:Default:email.html.php', array(
			'form' => $form->createView(),
			'user' => $user,
			'own' => $own,
		));
	}

	/**
	 * Modifie le mot de passe.
	 *
	 * @author DJ Fox <djfox@zcorrecteurs.fr>
	 * @author vincent1870 <vincent@zcorrecteurs.fr>
	 *
	 * @param Request $request
	 * @param integer $id
	 */
	public function passwordAction(Request $request, $id = null)
	{
		$user         = $this->getEditableUser($id);
		$own          = $user->getId() == $_SESSION['id'];
		$editPassword = new EditPassword($user);
		$form         = $this->createForm(new EditPasswordType(), $editPassword);
		$handler      = new EditPasswordHandler($form, $request);

		if ($handler->process($editPassword, $own))
		{
			if ($own)
			{
				return redirect('Votre mot de passe a bien été modifié.', $this->generateUrl('zco_options_password'));
			}
				
			return redirect(
				'Le mot de passe a bien été modifié.', 
				$this->generateUrl('zco_user_profile', array(
					'id' => $user->getId(), 
					'slug' => rewrite($user->getUsername()),
				))
			);
		}

		//Paramétrage de la vue.
		fil_ariane('Modifier le mot de passe');
		\Page::$titre = 'Modifier le mot de passe';
		
		return render_to_response('ZcoOptionsBundle:Default:password.html.php', array(
			'form' => $form->createView(),
			'user' => $user,
			'own' => $own,
		));
	}

	/**
	 * Modifie le profil (signature, biographie, adresses de contact, etc.).
	 *
	 * @author DJ Fox <djfox@zcorrecteurs.fr>
	 * @author Barbatos
	 * @author vincent1870 <vincent@zcorrecteurs.fr>
	 *
	 * @param Request $request
	 * @param integer $id
	 */
	public function profileAction(Request $request, $id)
	{
		$user = $this->getEditableUser($id);
		$own  = $user->getId() == $_SESSION['id'];
		$form = $this->createForm(new EditProfileType(), $user);

		if ('POST' === $request->getMethod())
		{
			$form->bindRequest($request);
			if ($form->isValid())
			{
				$user->save();
				if ($own)
				{
					return redirect('Votre profil a bien été modifié.', $this->generateUrl('zco_options_profile'));
				}
				
				return redirect(
					'Le profil a bien été modifié.', 
					$this->generateUrl('zco_user_profile', array(
						'id' => $user->getId(), 
						'slug' => rewrite($user->getUsername()),
					))
				);
			}
		}

		//Paramétrage de la vue.
		fil_ariane('Modifier le profil');
		\Page::$titre = 'Modifier le profil';
		
		return render_to_response('ZcoOptionsBundle:Default:profile.html.php', array(
			'form' => $form->createView(),
			'user' => $user,
			'own' => $own,
		));
	}

	/**
	 * Permet de modifier les préférences d'un membre ou celles par défaut.
	 *
	 * @author vincent1870 <vincent@zcorrecteurs.fr>
	 *
	 * @param Request $request
	 * @param integer $id
	 */
	public function preferencesAction(Request $request, $id = null)
	{
		if ('0' !== $id)
		{
			$user        = $this->getEditableUser($id);
			$own         = $user->getId() == $_SESSION['id'];
			$preferences = $user->getPreferences();
			$default     = false;
		}
		else
		{
			if (!verifier('options_editer_defaut'))
			{
				throw new AccessDeniedHttpException();
			}

			$preferences = \Doctrine_Core::getTable('UserPreference')->getDefault();
			$own = false;
			$default = true;
			$user = null;
		}

		$form = $this->createForm(new EditPreferencesType(), $preferences);

		if ('POST' === $request->getMethod())
		{
			$form->bindRequest($request);
			if ($form->isValid())
			{
				$preferences->save();
				if ($own)
				{
					$preferences->apply();
					return redirect('Vos préférences ont bien été modifiés.', $this->generateUrl('zco_options_preferences'));
				}
				elseif ($default)
				{
					return redirect('Les préférences par défaut ont bien été modifiés.', $this->generateUrl('zco_options_preferences', array('id' => '0')));
				}

				return redirect(
					'Les préférences ont bien été modifiés.', 
					$this->generateUrl('zco_user_profile', array(
						'id' => $user->getId(), 
						'slug' => rewrite($user->getUsername()),
					))
				);
			}
		}

		//Paramétrage de la vue
		\Page::$titre = 'Modifier les préférences';
		fil_ariane('Modifier les préférences'.($default ? ' par défaut' : ''));

		return render_to_response('ZcoOptionsBundle:Default:preferences.html.php', array(
			'form' => $form->createView(),
			'user' => $user,
			'own' => $own,
			'default' => $default,
		));
	}

    /**
     * Récupère l'utilisateur que l'on va modifier et procède au passage aux 
     * vérifications des autorisations.
     *
     * @param  integer $id L'id de l'utilisateur
     * @return \Utilisateur
     * @throws AccessDeniedHttpException Si on n'a pas le droit de procéder à l'action
     */
    protected function getEditableUser($id)
    {
    	if (!verifier('connecte'))
		{
			throw new AccessDeniedHttpException('Vous devez être connecté pour accéder à cette page.');
		}

    	if (!$id)
		{
			$id = $_SESSION['id'];
		}

		$user = \Doctrine_Core::getTable('Utilisateur')->getById($id);
		if (!$user)
		{
			throw new NotFoundHttpException('Cet utilisateur n\'existe pas.');
		}

		//Si on n'a pas le droit de modifier ce profil.
		if ($user->getId() != $_SESSION['id'] && !verifier('options_editer_profils'))
		{
			throw new AccessDeniedHttpException();
		}

		return $user;
    }
}

