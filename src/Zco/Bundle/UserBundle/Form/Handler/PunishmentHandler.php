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

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Zco\Bundle\CoreBundle\Cache\CacheInterface;

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
	 * @param CacheInterface $cache
	 */
	public function __construct(Form $form, Request $request, CacheInterface $cache)
	{
		$this->form    = $form;
		$this->request = $request;
		$this->cache   = $cache;
	}
	
	/**
	 * Procède à la soumission du formulaire.
	 *
	 * @param  UserPunishment $punishment L'entité liée au formulaire
	 * @return boolean Le formulaire a-t-il été traité correctement ?
	 */
	public function process(\UserPunishment $punishment = null)
	{
		if ($punishment === null)
		{
			$punishment = new \UserPunishment;
		}
		$this->form->setData($punishment);
		
		if ($this->request->getMethod() === 'POST')
		{
			$this->form->bindRequest($this->request);
			if ($this->form->isValid())
			{
				$this->onSuccess($punishment);
				
				return true;
			}
		}

		return false;
	}

	/**
	 * Action à effectuer lorsque le formulaire est valide.
	 *
	 * @param UserPunishment $punishment L'entité liée au formulaire
	 */
	protected function onSuccess(\UserPunishment $punishment)
	{
		$punishment->save();
		$punishment->getUser()->applyPunishment($punishment);
		$this->cache->set('dernier_refresh_droits', time(), 0);
		
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