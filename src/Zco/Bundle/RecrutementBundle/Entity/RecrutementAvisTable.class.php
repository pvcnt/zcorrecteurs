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