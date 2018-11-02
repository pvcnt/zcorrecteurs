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

namespace Zco\Bundle\OptionsBundle\Form\Handler;

use Zco\Bundle\OptionsBundle\Form\Model\EditEmail;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Gère la soumission du formulaire de modification de l'adresse courriel.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EditEmailHandler
{
	protected $form;
	protected $request;
	
	/**
	 * Constructeur.
	 *
	 * @param Form $form
	 * @param Request $request
	 */
	public function __construct(Form $form, Request $request)
	{
		$this->form 			= $form;
		$this->request 			= $request;
	}
	
	/**
	 * Procède à la soumission du formulaire.
	 *
	 * @param  EditEmail $editEmail L'entité liée au formulaire
	 * @param  boolean $own Modifie-t-on l'entité de l'utilisateur courant ?
	 * @return boolean Le formulaire a-t-il été traité correctement ?
	 */
	public function process(EditEmail $editEmail, $own)
	{
		$this->form->setData($editEmail);
        $this->form->handleRequest($this->request);
        if ($this->form->isSubmitted() && $this->form->isValid())
        {
            $this->onSuccess($editEmail, $own);

            return true;
        }

		return false;
	}

	/**
	 * Action à effectuer lorsque le formulaire est valide.
	 *
	 * @param EditEmail $editEmail L'entité liée au formulaire
	 * @param boolean $own Modifie-t-on l'entité de l'utilisateur courant ?
	 */
	protected function onSuccess(EditEmail $editEmail, $own)
	{
		$user = $editEmail->user;

		if ($own)
		{
			$hash = sha1(uniqid(rand(), true));

			$user->setNewEmail($editEmail->new);
			$user->setValidationHash($hash);
			$user->save();

			//Envoi du mail.
			$message = render_to_string('ZcoOptionsBundle:Mail:email.html.php', array(
				'pseudo'   => $user->getUsername(),
				'newEmail' => $editEmail->new,
				'oldEmail' => $user->getEmail(),
				'hash'     => $hash,
			));

			send_mail($editEmail->new, $user->getUsername(), '[zCorrecteurs.fr] Changement d\'adresse mail', $message);
		}
		
		$user->setEmail($editEmail->new);
		$user->save();
	}
}