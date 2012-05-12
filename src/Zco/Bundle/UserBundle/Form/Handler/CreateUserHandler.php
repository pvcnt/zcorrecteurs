<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Zco\Bundle\UserBundle\Form\Handler;

use Zco\Bundle\UserBundle\Event\RegisterEvent;
use Zco\Bundle\UserBundle\Event\FilterRegisterEvent;
use Zco\Bundle\UserBundle\UserEvents;
use Zco\Bundle\CaptchaBundle\Captcha\Captcha;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Gère la soumission du formulaire d'inscription.
 *
 * @author 	DJ Fox <djfox@zcorrecteurs.fr>
 *          Savageman <savageman@zcorrecteurs.fr>
 *          vincent1870 <vincent@zcorrecteurs.fr>
 */
class CreateUserHandler
{
	protected $form;
	protected $request;
	protected $eventDispatcher;
	
	/**
	 * Constructeur.
	 *
	 * @param Form $form
	 * @param Request $request
	 * @param EventDispatcherInterface $eventDispatcher
	 */
	public function __construct(Form $form, Request $request, EventDispatcherInterface $eventDispatcher)
	{
		$this->form 			= $form;
		$this->request 			= $request;
		$this->eventDispatcher 	= $eventDispatcher;
	}
	
	/**
	 * Procède à la soumission du formulaire.
	 *
	 * @param  Utilisateur $user L'entité liée au formulaire
	 * @return boolean Le formulaire a-t-il été traité correctement ?
	 */
	public function process(\Utilisateur $user = null)
	{
		if ($user === null)
		{
			$user = new \Utilisateur;
		}
		$this->form->setData($user);
		
		if ($this->request->getMethod() === 'POST')
		{
			$this->form->bindRequest($this->request);
			if ($this->form->isValid())
			{
				$this->onSuccess($user);
				
				return true;
			}
		}

		return false;
	}

	/**
	 * Action à effectuer lorsque le formulaire est valide.
	 *
	 * @param Utilisateur $user L'entité liée au formulaire
	 */
	protected function onSuccess(\Utilisateur $user)
	{
		$event = new FilterRegisterEvent($user);
		$this->eventDispatcher->dispatch(UserEvents::PRE_REGISTER, $event);
		if ($event->isAborted())
		{
			$this->form->addError(new FormError($event->getErrorMessage() ?: 'Erreur lors de l\'inscription.'));
		}
		elseif (!Captcha::verifier($this->request->request->get('captcha')))
		{
			$this->form->addError(new FormError('Erreur lors de la vérification de l\'anti-spam.'));
		}
		else
		{
			\Doctrine_Core::getTable('Utilisateur')->insert($user);
			
			$message = render_to_string('ZcoUserBundle:Mail:registration.html.php', array(
				'pseudo' => $user->getUsername(),
				'id'	 => $user->getId(),
				'hash'   => $user->getRegistrationHash(),
			));
			send_mail($user->getEmail(), $user->getUsername(), 
				'[zCorrecteurs.fr] Confirmation de votre inscription', $message);
			
			$event = new RegisterEvent($user);
			$this->eventDispatcher->dispatch(UserEvents::POST_REGISTER, $event);
		}
	}
}