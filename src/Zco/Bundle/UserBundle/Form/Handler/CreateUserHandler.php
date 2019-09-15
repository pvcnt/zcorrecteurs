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

namespace Zco\Bundle\UserBundle\Form\Handler;

use Zco\Bundle\ContentBundle\Captcha\Captcha;
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
        $this->form->handleRequest($this->request);
        if ($this->form->isSubmitted() && $this->form->isValid())
        {
            return $this->onSuccess($user);
        }

		return false;
	}

	/**
	 * Action à effectuer lorsque le formulaire est valide.
	 *
	 * @param  Utilisateur $user L'entité liée au formulaire
	 * @return boolean L'utilisateur a-t-il été réellement créé ?
	 */
	protected function onSuccess(\Utilisateur $user)
	{
        if (!Captcha::verifier($this->request->request->get('captcha')))
		{
			$this->form->addError(new FormError('Erreur lors de la vérification de l\'anti-spam.'));
			
			return false;
		}
		
		\Doctrine_Core::getTable('Utilisateur')->insert($user);
		
		$message = render_to_string('ZcoUserBundle:Mail:registration.html.php', array(
			'pseudo' => $user->getUsername(),
			'id'	 => $user->getId(),
			'hash'   => $user->getRegistrationHash(),
		));
		send_mail($user->getEmail(), $user->getUsername(), '[zCorrecteurs.fr] Confirmation de votre inscription', $message);
		
		return true;
	}
}