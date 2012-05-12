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

use Zco\Bundle\UserBundle\User\User;
use Zco\Bundle\UserBundle\Exception\LoginException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Form;
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
	 * @param Form $form
	 * @param Request $request
	 * @param User $user
	 */
	public function __construct(Form $form, Request $request, User $user)
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
		if ($this->request->getMethod() === 'POST')
		{
			$this->form->bindRequest($this->request);
			if ($this->form->isValid())
			{
				return $this->onSuccess();
			}
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
			$userEntity = $this->user->attemptFormLogin($data, $this->request);
			$this->user->login($userEntity, $data['remember']);
		}
		catch (LoginException $e)
		{
			$this->form->addError(new FormError($e->getMessage() ?: 'Mauvais couple pseudonyme/mot de passe.'));
			
			return false;
		}
		
		return true;
	}
}