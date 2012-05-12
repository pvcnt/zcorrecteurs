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

/**
 */
class LivredorTable extends Doctrine_Table
{
	public function VerifierDernierPost($ip)
	{
		if(verifier('livredor_epargne_anti_up'))
		{
			return true;
		}
		elseif(!verifier('livredor_ecrire'))
		{
			return false;
		}
		elseif (verifier('connecte'))
		{
			$retour = Doctrine_Query::create()
				->select('UNIX_TIMESTAMP(date)')
				->from('Livredor')
				->where('utilisateur_id = ?', $_SESSION['id'])
				->orderBy('date DESC')
				->limit(1)
				->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
			return empty($retour) || $retour <= (time() - 7*24*60*60);
		}
		else
		{
		    $retour = Doctrine_Query::create()
				->select('UNIX_TIMESTAMP(date)')
				->from('Livredor')
				->where('ip = ?', ip2long($ip))
				->orderBy('date DESC')
				->limit(1)
				->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
			return empty($retour) || $retour <= (time() - 7*24*60*60);
		}
	}

	public function NoteMoyenne()
	{
		return Doctrine_Query::create()
			->select('AVG(note)')
			->from('Livredor')
			->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
	}

	public function Messages()
	{
		return Doctrine_Query::create()
			->select('m.id, m.date, m.message, m.note, m.ip, u.utilisateur_id, u.utilisateur_pseudo, u.utilisateur_avatar')
			->from('Livredor m')
			->leftJoin('m.Utilisateur u')
			->orderBy('m.date DESC');
	}
}
