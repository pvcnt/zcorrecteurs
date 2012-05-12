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
class HistoriqueGroupeTable extends Doctrine_Table
{
	public function Lister()
	{
		return Doctrine_Query::create()
			->select('h.date, u1.utilisateur_id, u1.utilisateur_pseudo, '.
				'u2.utilisateur_id, u2.utilisateur_pseudo, '.
				'g1.groupe_nom, g1.groupe_class, g2.groupe_nom, g2.groupe_class')
			->from('HistoriqueGroupe h')
			->leftJoin('h.Utilisateur u1')
			->leftJoin('h.Admin u2')
			->leftJoin('h.AncienGroupe g1')
			->leftJoin('h.NouveauGroupe g2')
			->orderBy('h.date DESC')
			->execute();
	}
}