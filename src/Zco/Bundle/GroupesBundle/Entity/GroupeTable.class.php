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
class GroupeTable extends Doctrine_Table
{
	public function getApplicable($hydrationMode = null)
	{
		return $this->getApplicableQuery()->execute(array(), $hydrationMode);
	}
	
	public function getBySecondary($hydrationMode = null)
	{
		return $this->getBySecondaryQuery()->execute(array(), $hydrationMode);
	}
	
	public function getApplicableQuery()
	{
		return $this->createQuery('g')
			->select('g.*')
			->where('g.id <> ?', GROUPE_VISITEURS)
			->andWhere('g.secondary = ?', false);
	}
	
	public function getBySecondaryQuery()
	{
		return $this->createQuery('g')
			->select('g.*')
			->where('g.secondary = ?', true);
	}
	
	public function getByPunishmentQuery()
	{
		return $this->createQuery()
			->select('*')
			->where('sanction = ?', true);
	}
	
	public function findAll($hydrationMode = null)
	{
		return $this->createQuery('g')
			->select('g.*, COUNT(*) AS effectifs')
			->leftJoin('g.Utilisateurs')
			->groupBy('g.id')
			->execute(array(), $hydrationMode);
	}
}
