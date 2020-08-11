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
 * Categorie
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class Categorie extends BaseCategorie
{
	public function __toString()
	{
		return $this['nom'];
	}
	
	/**
	 * Retourne la liste des sujets d'aide visibles de la catégorie.
	 * @return Doctrine_Collection
	 */
	public function Aide()
	{
		static $aide = array();

		if (!isset($aide[$this['id']]))
		{
			$aide[$this['id']] = Doctrine_Query::create()
				->select('a.*')
				->from('Aide a')
				->where('a.categorie_id = ?', $this['id'])
				->andWhere('a.racine = 1')
				->orderBy('a.ordre')
				->execute();
		}
		return $aide[$this['id']];
	}

	public function postDelete($event)
	{
		Doctrine_Query::create()
			->update('Categorie')
			->set('cat_gauche', 'cat_gauche - 2')
			->where('cat_gauche >= ?', $this['gauche'])
			->execute();

		Doctrine_Query::create()
			->update('Categorie')
			->set('cat_droite', 'cat_droite - 2')
			->where('cat_gauche >= ?', $this['gauche'])
			->execute();

		Doctrine_Query::create()
			->delete('GroupeDroit')
			->where('gd_id_categorie = ?', $this['id'])
			->execute();

		Container::getService('zco_core.cache')->Delete('categories');
	}

	public function majNbElements($nb = 1)
	{
		$this['nb_elements'] += $nb;
		$this->save();
	}

	public function listerEnfants($include = false)
	{
		$query = Doctrine_Query::create()
			->select('*')
			->from('Categorie')
			->orderBy('cat_gauche');

		if ($include)
		{
			$query->where('cat_gauche >= ?', $this['gauche']);
			$query->andWhere('cat_droite <= ?', $this['droite']);
		}
		else
		{
			$query->where('cat_gauche > ?', $this['gauche']);
			$query->andWhere('cat_droite < ?', $this['droite']);
		}

		return $query->execute();
	}

	public function listerParents($include = false)
	{
		$query = Doctrine_Query::create()
			->select('*')
			->from('Categorie')
			->orderBy('cat_gauche');

		if ($include)
		{
			$query->where('cat_gauche <= ?', $this['gauche']);
			$query->andWhere('cat_droite >= ?', $this['droite']);
		}
		else
		{
			$query->where('cat_gauche < ?', $this['gauche']);
			$query->andWhere('cat_droite > ?', $this['droite']);
		}

		return $query->execute();
	}

	public function fiches($valide = true)
	{
		$query = Doctrine_Query::create()
			->select('f.*')
			->from('Fiche f')
			->innerJoin('f.Categories c WITH c.cat_gauche >= ? AND c.cat_droite <= ?', array($this['gauche'], $this['droite']));
		if ($valide === true)
		{
			$query->where('f.valide = ?', $valide);
		}
		elseif ($valide === false)
		{
			$query->where('f.valide = ?', $valide);
		}
		//echo $query->getSqlQuery();
		return $query->execute();
	}
}