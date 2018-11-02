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

use Doctrine\Common\Cache\Cache;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Gère la soumission du formulaire de sanction d'un utilisateur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class PunishmentHandler
{
	protected $form;
	protected $request;
	protected $cache;
	
	/**
	 * Constructeur.
	 *
	 * @param Form $form
	 * @param Request $request
	 * @param Cache $cache
	 */
	public function __construct(Form $form, Request $request, Cache $cache)
	{
		$this->form    = $form;
		$this->request = $request;
		$this->cache   = $cache;
	}
	
	/**
	 * Procède à la soumission du formulaire.
	 *
	 * @param \UserPunishment $punishment L'entité liée au formulaire
	 * @return boolean Le formulaire a-t-il été traité correctement ?
	 */
	public function process(\UserPunishment $punishment = null)
	{
		if ($punishment === null)
		{
			$punishment = new \UserPunishment;
		}
		$this->form->setData($punishment);
        $this->form->handleRequest($this->request);
        if ($this->form->isSubmitted() && $this->form->isValid())
        {
            $this->onSuccess($punishment);

            return true;
        }

		return false;
	}

	/**
	 * Action à effectuer lorsque le formulaire est valide.
	 *
	 * @param \UserPunishment $punishment L'entité liée au formulaire
	 */
	protected function onSuccess(\UserPunishment $punishment)
	{
		$punishment->save();
		$punishment->getUser()->applyPunishment($punishment);
		$this->cache->save('dernier_refresh_droits', time(), 0);
		
		$message = render_to_string('ZcoUserBundle:Mail:punishment.html.php', array(
			'pseudo'      => $punishment->getUser()->getUsername(),
			'adminPseudo' => $punishment->getAdmin()->getUsername(),
			'adminId'     => $punishment->getAdminId(),
			'reason'      => $punishment->getReason(),
		));
		send_mail($punishment->getUser()->getEmail(), $punishment->getUser()->getUsername(), 
			'[zCorrecteurs.fr] Vous avez été sanctionné', $message);
	}
}