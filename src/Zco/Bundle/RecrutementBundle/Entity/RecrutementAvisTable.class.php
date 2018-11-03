<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
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
class RecrutementAvisTable extends Doctrine_Table
{
	public function listerCandidature($id)
	{
		$avis = array();
		$resultat = $this->createQuery()
			->select('COUNT(*) AS votes, type')
			->where('candidature_id = ?', $id)
			->groupBy('type')
			->execute();
		
		foreach($resultat as $donnees)
		{
			$avis[$donnees['type']] = $donnees['votes'];
		}
		
		return $avis;
	}
	
	public function recupererUtilisateurCandidature($utilisateurId, $candidatureId)
	{
		$resultat = $this->createQuery()
			->select('*')
			->where('candidature_id = ?', $candidatureId)
			->andWhere('utilisateur_id = ?', $utilisateurId)
			->fetchOne();
	}
}