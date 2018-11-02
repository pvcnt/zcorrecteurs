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
 * Gère la soumission du formulaire de réponse à une demande de nouveau 
 * nom d'utilisateur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class AnswerNewUsernameHandler
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
		$this->form    = $form;
		$this->request = $request;
	}
	
	/**
	 * Procède à la soumission du formulaire.
	 *
	 * @param  \UserNewUsername $query L'entité liée au formulaire
	 * @return boolean Le formulaire a-t-il été traité correctement ?
	 */
	public function process(\UserNewUsername $query)
	{
		$this->form->setData($query);
        $this->form->handleRequest($this->request);
        if ($this->form->isSubmitted() && $this->form->isValid())
        {
            $this->onSuccess($query);

            return true;
        }

		return false;
	}

	/**
	 * Action à effectuer lorsque le formulaire est valide.
	 *
	 * @param UserNewUsername $query L'entité liée au formulaire
	 */
	protected function onSuccess(\UserNewUsername $query)
	{
		$query->setOldUsername($query->getUser()->getUsername()); //Au cas où celui-ci ait changé pour une raison ou une autre depuis la demande.
		$query->setAdminId($_SESSION['id']);
		$query->setResponseDate(new \Doctrine_Expression('NOW()'));
		$query->save();
		
		if ($query->getStatus() == CH_PSEUDO_ACCEPTE)
		{
			$oldUsername = $query->getUser()->getUsername();
			$query->getUser()->applyNewUsername($query);
			
			$message = render_to_string('ZcoUserBundle:Mail:newPseudoAccepted.html.php', array(
				'pseudo'         => $oldUsername,
				'newPseudo'      => $query->getNewUsername(),
				'reason'         => $query->getAdminResponse(),
				'adminPseudo'    => $_SESSION['pseudo'],
				'adminId'        => $_SESSION['id'],
			));

			send_mail($query->getUser()->getEmail(), $oldUsername, '[zCorrecteurs.fr] Votre changement de pseudo a été accepté', $message);
		}
		elseif ($query->getStatus() == CH_PSEUDO_REFUSE)
		{
			$message = render_to_string('ZcoUserBundle:Mail:newPseudoRefused.html.php', array(
				'pseudo'         => $query->getUser()->getUsername(),
				'newPseudo'      => $query->getNewUsername(),
				'reason'         => $query->getAdminResponse(),
				'adminPseudo'    => $_SESSION['pseudo'],
				'adminId'        => $_SESSION['id'],
			));

			send_mail($query->getUser()->getEmail(), $query->getUser()->getUsername(), '[zCorrecteurs.fr] Votre changement de pseudo a été refusé', $message);
		}
	}
}