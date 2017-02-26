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

namespace Zco\Bundle\UserBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Gère la soumission du formulaire d'avertissement d'un utilisateur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class WarningHandler
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
		$this->form = $form;
		$this->request = $request;
	}
	
	/**
	 * Procède à la soumission du formulaire.
	 *
	 * @param  UserWarning $warning L'entité liée au formulaire
	 * @return boolean Le formulaire a-t-il été traité correctement ?
	 */
	public function process(\UserWarning $warning = null)
	{
		if ($warning === null)
		{
			$warning = new \UserWarning;
		}
		$this->form->setData($warning);
		
		if ($this->request->getMethod() === 'POST')
		{
			$this->form->submit($this->request);
			if ($this->form->isValid())
			{
				$this->onSuccess($warning);
				
				return true;
			}
		}

		return false;
	}

	/**
	 * Action à effectuer lorsque le formulaire est valide.
	 *
	 * @param UserWarning $warning L'entité liée au formulaire
	 */
	protected function onSuccess(\UserWarning $warning)
	{
		$warning->save();
		if ($warning->getPercentage() > 0)
		{
			$warning->getUser()->incrementPercentage($warning->getPercentage());
		}
		elseif ($warning->getPercentage() < 0)
		{
			$warning->getUser()->decrementPercentage(abs($warning->getPercentage()));
		}
		
		if ($warning->hasReason())
		{
			if ($warning->getPercentage() > 0)
			{
				$subtitle = '+'.$warning->getPercentage().'%';
				$title    = 'Votre niveau d\'avertissement a été augmenté';
				$action   = 'a été augmenté de '.$warning->getPercentage().'%';
			}
			elseif ($warning->getPercentage() < 0)
			{
				$subtitle = '-'.abs($warning->getPercentage()).'%';
				$title    = 'Votre niveau d\'avertissement a été diminué !';
				$action   = 'a été diminué de '.abs($warning->getPercentage()).'%';
			}
			else
			{
				$soustitre = '';
				$titre = 'Vous avez reçu un avertissement';
				$etat = 'reste cependant inchangé';
			}

			$message = render_to_string('ZcoUserBundle:Mp:warn.html.php', array(
				'pseudo' =>	$warning->getAdmin()->getUsername(),
				'id'     =>	$warning->getAdminId(),
				'action' =>	$action,
				'reason' =>	$warning->getReason(),
			));
			AjouterMPAuto($title, $subtitle, $warning->getUserId(), $message);
		}
	}
}