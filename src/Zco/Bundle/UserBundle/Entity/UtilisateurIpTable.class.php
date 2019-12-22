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

/**
 * Requêtes sur la table des adresses IP des utilisateurs inscrits.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UtilisateurIpTable extends Doctrine_Table
{
	/**
	 * Récupère les entrées correspondant à une IP donnée. L'adresse fournie 
	 * peut contenir une ou plusieurs étoiles qui permettent de définir une 
	 * plage d'adresses.
	 *
	 * @param  string $ip Adresse IP (format textuel)
	 * @param  integer|null $hydrationMode Le mode d'hydratation
	 * @return \Doctrine_Collection
	 */
	public function findByIP($ip, $hydrationMode = null)
	{
		$query = Doctrine_Query::create()
			->select('u.utilisateur_id, u.utilisateur_pseudo, '.
				'u.utilisateur_valide, u.utilisateur_forum_messages, u.utilisateur_pourcentage, '.
				'g.groupe_id, g.groupe_nom, g.class, u.utilisateur_date_inscription, '.
				'u.utilisateur_ip, i.ip_date_debut, i.ip_date_last')
			->from('UtilisateurIp i')
			->leftJoin('i.Utilisateur u')
			->leftJoin('u.Groupe g')
			->orderBy('u.utilisateur_pseudo');

		//Si on demande un masque d'ip
		if (strpos($ip, '*') !== false)
		{
			$ipMin = ip2long(str_replace('*', '0', $ip));
			$ipMax = ip2long(str_replace('*', '255', $ip));
			$query->where('i.ip_ip BETWEEN ? AND ?', array($ipMin, $ipMax));
		}
		else
		{
			$query->where('i.ip_ip = ?', ip2long($ip));
		}

		return $query->execute(array(), $hydrationMode);
	}

	/**
	 * Supprime les adresses IP de l'historique datant de plus d'un an.
	 *
	 * @return integer Le nombre d'adresses supprimées.
	 */
	public function purge()
	{
		return $this->createQuery()
			->delete()
			->where('date_last <= NOW() - INTERVAL 1 YEAR')
			->execute();
	}
}