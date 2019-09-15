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

namespace Zco\Bundle\UserBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Zco\Bundle\UserBundle\User\UserSession;
use Zco\Bundle\UserBundle\Exception\LoginException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

/**
 * Gère la soumission du formulaire de connexion.
 *
 * 	@author Savageman <savageman@zcorrecteurs.fr>
 *          Barbatos
 *          vincent1870 <vincent@zcorrecteurs.fr>
 */
class FormLoginHandler
{
	protected $form;
	protected $request;
	protected $user;
	
	/**
	 * Constructeur.
	 *
	 * @param FormInterface $form
	 * @param Request $request
	 * @param UserSession $user
	 */
	public function __construct(FormInterface $form, Request $request, UserSession $user)
	{
		$this->form 	= $form;
		$this->request 	= $request;
		$this->user 	= $user;
	}
	
	/**
	 * Procède à la soumission du formulaire.
	 *
	 * @return boolean Le formulaire a-t-il été traité correctement ?
	 */
	public function process()
	{
        $this->form->handleRequest($this->request);
        if ($this->form->isSubmitted() && $this->form->isValid())
        {
            return $this->onSuccess();
        }

		return false;
	}

	/**
	 * Action à effectuer lorsque le formulaire est valide.
	 *
	 * @return boolean Le formulaire a-t-il été traité correctement ?
	 */
	protected function onSuccess()
	{
		$data = $this->form->getData();
		try
		{
			$remember = isset($data['remember']) ? (bool) $data['remember'] : true;
			$userEntity = $this->user->attemptFormLogin($data);
			$this->user->login($this->request, $userEntity, $remember);
		}
		catch (LoginException $e)
		{
			$this->form->addError(new FormError($e->getMessage() ?: 'Mauvais couple pseudonyme/mot de passe.'));
			
			return false;
		}
		
		return true;
	}
}