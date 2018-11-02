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

use Zco\Bundle\OptionsBundle\Form\Model\EditPassword;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Gère la soumission du formulaire de modification du mot de passe.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EditPasswordHandler
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
	 * @param  EditPassword $editPassword L'entité liée au formulaire
	 * @param  boolean $own Modifie-t-on l'entité de l'utilisateur courant ?
	 * @return boolean Le formulaire a-t-il été traité correctement ?
	 */
	public function process(EditPassword $editPassword, $own)
	{
		$this->form->setData($editPassword);
        $this->form->handleRequest($this->request);
        if ($this->form->isSubmitted() && $this->form->isValid())
        {
            $this->onSuccess($editPassword, $own);

            return true;
        }

		return false;
	}

	/**
	 * Action à effectuer lorsque le formulaire est valide.
	 *
	 * @param EditPassword $editPassword L'entité liée au formulaire
	 * @param boolean $own Modifie-t-on l'entité de l'utilisateur courant ?
	 */
	protected function onSuccess(EditPassword $editPassword, $own)
	{
		$user = $editPassword->user;

		$user->setRawPassword($editPassword->new);
		$user->save();
	}
}