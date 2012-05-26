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

use Zco\Bundle\CoreBundle\Paginator\Paginator;
use Zco\Bundle\FileBundle\Model\GenericEntityTableInterface;

/**
 */
class ForumMessageTable extends Doctrine_Table implements GenericEntityTableInterface
{
	public function messagesUtilisateur($uid, $cats)
	{
		$groupes = isset($_SESSION['groupes_secondaires'])
		? $_SESSION['groupes_secondaires']
		: array();
		array_unshift($groupes, $_SESSION['groupe']);
		$groupes = implode(',', $groupes);

		$query = Doctrine_Query::create()
			->select('m.date, m.texte, s.titre, s.resolu, s.ferme, s.corbeille')
			->from('ForumMessage m')
			->leftJoin('m.Sujet s')
			->leftJoin('s.Categorie c')
			->leftJoin('c.GroupeDroit g WITH g.gd_id_groupe IN (' . $groupes . ')')
			->leftJoin('g.Droit d')
			->orderBy('m.date DESC')
			->where('m.message_auteur = ? AND ((d.droit_nom = "corbeille_sujets" AND s.corbeille = IF(g.gd_valeur>0,1,0)) OR (s.corbeille = 0))', $uid)
			->distinct(true);

		$cats = array_map('intval', $cats);
		$conds = implode(' OR s.forum_id = ', $cats);
		$query->addWhere('(s.forum_id = '.$conds.')');
		$query->groupBy('m.id');
		
		return new Paginator($query, 20, 0);
	}
	
	public function getEntities(array $pks)
    {
        return \Doctrine_Query::create()
            ->select('m.*, s.*')
            ->from('ForumMessage m INDEXBY m.id')
            ->leftJoin('m.Sujet s')
            ->whereIn('m.id', $pks)
            ->execute();
    }
}
