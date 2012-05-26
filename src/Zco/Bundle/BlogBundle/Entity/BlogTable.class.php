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

use Zco\Bundle\FileBundle\Model\GenericEntityTableInterface;

/**
 */
class BlogTable extends Doctrine_Table implements GenericEntityTableInterface
{
    public function getEntities(array $pks)
    {
        return \Doctrine_Query::create()
            ->select('v.*, b.*')
            ->from('Blog b INDEXBY b.id')
            ->whereIn('b.id', $pks)
            ->leftJoin('b.CurrentVersion v')
            ->execute();
    }

	public function query(array $options)
	{
		return $this->getQuery($options)->execute();
	}

	public function getQuery(array $options)
	{
		$query = $this->createQuery('b')
			->select('b.*, v.*, a.*, u.id, u.pseudo, c.nom, c.id')
			->leftJoin('b.CurrentVersion v')
			->leftJoin('b.Authors a')
			->leftJoin('b.Category c')
			->leftJoin('a.User u');
		
		if (
			array_key_exists('status', $options)
			&& (is_numeric($options['status']) || is_array($options['status']))
		)
		{
			if (is_array($options['status']))
			{
				array_map('intval', $options['status']);
				$query->andWhereIn('status', $options['status']);
			}
			else
			{
				$query->andWhere('status = ?', $options['status']);
			}
		}
		if (array_key_exists('user_id', $options) && is_numeric($options['user_id']))
		{
			/*$where[] = "blog_id IN(" .
					"SELECT auteur_id_billet " .
					"FROM zcov2_blog_auteurs " .
					"WHERE auteur_id_utilisateur = :id_utilisateur" . 
					((array_key_exists('lecteurs', $params) && $params['lecteurs'] == false) ? " AND auteur_statut > 1" : "").")";
			$bind['id_utilisateur'] = $params['id_utilisateur'];*/
		}
		if (array_key_exists('category_id', $options) && is_numeric($options['category_id']))
		{
			$query->andWhere('category_id = ?', $options['category_id']);
		}
		if (array_key_exists('readers', $options))
		{
			//if($params['lecteurs'] == false)
			//	$where_auteurs[] = 'auteur_statut > 1';
		}
		if (array_key_exists('scheduled', $options))
		{
			if ($options['scheduled'] === false)
			{
				$query->andWhere('publication_date <= NOW()');
			}
		}
		if (array_key_exists('id', $options))
		{
			if (is_array($options['id']))
			{
				$query->andWhereIn('id', $options['id']);
			}
			elseif (is_numeric($options['id']))
			{
				$query->andWhere('id = ?', $options['id']);
			}
			else
			{
				throw new \InvalidArgumentException(sprintf(
					'Unvalid type for "id" option: got %s, expected array or integer.',
					gettype($options['id'])
				));
			}
		}
		
		if (isset($options['#order_by']))
		{
			if ($options['#order_by'][0] === '-')
			{
				$query->orderBy(substr($options['#order_by'], 1).' DESC');
			}
			else
			{
				$query->orderBy($options['#order_by']);
			}
		}
		
		return $query;
	}
}