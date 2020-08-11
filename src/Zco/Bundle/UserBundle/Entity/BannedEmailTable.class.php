<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2020 Corrigraphie
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

/**
 * Requêtes sur la table des adresses courriel interdites.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class BannedEmailTable extends Doctrine_Table
{
	/**
	 * Renvoie la liste des plages de courriels bannies.
	 *
	 * @return \Doctrine_Collection
	 */
	public function getAll()
	{
		return $this->createQuery('m')
			->select('m.mail, m.id, m.raison, u.utilisateur_id, u.utilisateur_pseudo')
			->leftJoin('m.User u')
			->orderBy('m.mail')
			->execute();
	}

	/**
	 * Vérifie si une adresse courriel est bannie.
	 *
	 * @param  string $email L'adresse courriel à vérifier
	 * @return boolean
	 */
	public function isBanned($email)
	{
		$domain = substr($email, strpos($email, '@') + 1);
		$bans = $this->createQuery()
			->select('mail')
			->execute(array(), \Doctrine_Core::HYDRATE_ARRAY);

		foreach ($bans as $ban)
		{
			if (preg_match('`^'.str_replace(array('.', '*'), array('\.', '.+'), $ban['mail']).'$`', $domain))
			{
				return true;
			}
		}
		
		return false;
	}
}