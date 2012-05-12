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
class DicteeTable extends Doctrine_Table
{
	public function getTags(Dictee $Dictee)
	{
		$dbh = Doctrine_Manager::connection()->getDbh();
		$q = $dbh->prepare('SELECT t.id, t.nom, t.couleur, t.description, '
			.'t.couleur <> \'\' AND t.couleur IS NOT NULL AS coul '
			.'FROM zcov2_dictees_tags dt '
			.'LEFT JOIN zcov2_tags t ON t.id = dt.tag_id '
			.'WHERE dt.dictee_id = ? '
			.'ORDER BY coul DESC, t.nom ASC');
		$q->execute(array($Dictee->id));

		$o = array();
		foreach ($q->fetchAll() as $d)
		{
			$out = new StdClass();
			$out->Tag = new StdClass();
			foreach ($d as $k => $v)
				$out->Tag->$k = $v;

			$o[] = $out;
		}
		return $o;

		/*return Doctrine_Query::create()
			->select('dt.tag_id, t.nom, t.couleur, t.description)
			->from('DicteeTag dt')
			->leftJoin('dt.Tag t')
			->where('dt.dictee_id = ?', $Dictee->id)
			->orderBy('coul DESC, t.nom ASC')
			->execute();
		*/
	}
	
	/**
	 * Liste simplement les dictées avec leur id et leur titre
	 * pour le sitemap.
	 *
	 * @return array
	 */
	public function getAllId()
	{
	    return $this->createQuery()
	        ->select('id, titre')
	        ->where('etat = ?', DICTEE_VALIDEE)
	        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}
}
