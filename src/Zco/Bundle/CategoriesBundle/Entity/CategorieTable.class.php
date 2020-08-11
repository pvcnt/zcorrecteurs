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
 */
class CategorieTable extends Doctrine_Table
{
	public function getCategoriesCiblables($disponible = true)
	{
		$requete = $this->createQuery('c')
			->select('c.*')
			->where('c.cat_niveau = 1')
			->orderBy('c.cat_gauche');
		
		if ($disponible)
		{
			$requete->andWhere('c.cat_disponible_ciblage = 1');
		}
			
		return $requete->execute();
	}

	public function findAll($niveau = 0)
	{
		static $retour = null;
		static $retour_avec_verif = null;

		if (!$retour)
		{
			if(!($retour = Container::getService('zco_core.cache')->Get('categories')))
			{
				$rows = Doctrine_Query::create()
					->select('c.*')
					->from('Categorie c')
					->orderBy('cat_gauche ASC')
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				foreach($rows as $row)
					$retour[$row['id']] = $row;

				Container::getService('zco_core.cache')->Set('categories', $retour, 0);
			}
		}

		if ($verif_droits == true && !$retour_avec_verif)
		{
			$retour_avec_verif = $retour;
			foreach($retour_avec_verif as $cle => $valeur)
			{
				if(!verifier('voir', $cle))
					unset($retour_avec_verif[$cle]);
			}
		}
		return $verif_droits ? $retour_avec_verif : $retour;
	}

	public function listerParents($cid, $include = false)
	{
		if ($cat = $this->find($cid))
		{
			return $cat->listerParents($include);
		}
		else
			return false;
	}

	public function listerEnfants($cid, $include = false)
	{
		if ($cat = $this->find($cid))
		{
			return $cat->listerEnfants($include);
		}
		else
			return false;
	}
}