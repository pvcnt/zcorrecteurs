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
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class FileTable extends Doctrine_Table
{
	const FOLDER_ALL		 = 1;
	const FOLDER_PICTURES	 = 2;
	const FOLDER_DOCUMENTS   = 3;
	const FOLDER_AUDIO	  	 = 4;
	const FOLDER_THIS_WEEK   = 5;
	const FOLDER_THIS_MONTH  = 6;
	const FOLDER_LAST_IMPORT = 7;
	
	public static function getFolders()
	{
		return array(
			array(
				'id' => self::FOLDER_LAST_IMPORT,
				'name' => 'Dernier import',
				'icon' => 'inbox',
				'hidden' => true,
			),
			array(
				'id' => self::FOLDER_ALL,
				'name' => 'Tous les fichiers',
				'icon' => 'home',
				'hidden' => false,
			),
			array(
				'id' => self::FOLDER_PICTURES,
				'name' => 'Images',
				'icon' => 'picture',
				'hidden' => false,
			),
			array(
				'id' => self::FOLDER_AUDIO,
				'name' => 'Fichiers audio',
				'icon' => 'music',
				'hidden' => false,
			),
			array(
				'id' => self::FOLDER_DOCUMENTS,
				'name' => 'Documents',
				'icon' => 'file',
				'hidden' => false,
			),
			array(
				'id' => self::FOLDER_THIS_WEEK,
				'name' => 'Cette semaine',
				'icon' => 'time',
				'hidden' => false,
			),
			array(
				'id' => self::FOLDER_THIS_MONTH,
				'name' => 'Ce mois',
				'icon' => 'time',
				'hidden' => false,
			),
		);
	}
	
	public function getSpaceUsage($userId)
	{
		return $this
			->getSpaceUsageQuery($userId)
			->execute(array(), \Doctrine_Core::HYDRATE_SINGLE_SCALAR);
	}
	
	public function getSpaceUsageQuery($userId)
	{
		return $this->createQuery()
			->select('SUM(size)')
			->where('user_id = ?', $userId)
			->andWhere('quota_affected = ?', true);
	}
	
	public function getById($id)
	{
		return $this->getByIdQuery($id)->fetchOne();
	}

	public function getByIdQuery($id)
	{
		return $this->createQuery('f')
			->select('f.*, t.*, l.*')
			->leftJoin('f.Thumbnail t')
			->leftJoin('f.License l')
			->where('f.id = ?', $id)
			->limit(1);
	}
	
	public function getByFolderAndSearch($folder, $userId, $search, $limit = null)
	{
		return $this->getByFolderAndSearchQuery($folder, $userId, $search, $limit)->execute();
	}

	public function getByFolderAndSearchQuery($folder, $userId, $search, $limit = null)
	{
		return $this
			->getByFolderQuery((int) $folder, $userId, $limit)
			->andWhere('name LIKE ? ESCAPE \'#\'', '%'.str_replace(
				'*',
				'%', 
				str_replace(array('%', '_'), array('#%', '_%'), $search)
			).'%');
	}
	
	public function getByFolder($folder, $userId, $limit = null)
	{
		return $this->getByFolderQuery($folder, $userId, $limit)->execute();
	}

	public function getByFolderQuery($id, $userId, $limit = null)
	{
		$query = $this->createQuery('f')
			->select('f.*, t.*')
			->leftJoin('f.Thumbnail t')
			->where('f.user_id = ?', $userId);
		
		if ($id === self::FOLDER_PICTURES)
		{
			$query
				->andWhere('major_mime = ?', 'image')
				->orderBy('date DESC');
		}
		elseif ($id === self::FOLDER_AUDIO)
		{
			$query
				->andWhere('major_mime = ?', 'audio')
				->orderBy('date DESC');
		}
		elseif ($id === self::FOLDER_DOCUMENTS)
		{
			$query
				->andWhere('major_mime <> ?', 'image')
				->andWhere('major_mime <> ?', 'audio')
				->orderBy('date DESC');
		}
		elseif ($id === self::FOLDER_THIS_WEEK)
		{
			$targetDate = date('Y-m-d H:i:s', strtotime('-1 week'));
			$query
				->andWhere('date >= ? OR edition_date >= ?', array($targetDate, $targetDate))
				->orderBy('edition_date DESC');
		}
		elseif ($id === self::FOLDER_THIS_MONTH)
		{
			$targetDate = date('Y-m-d H:i:s', strtotime('-1 month'));
			$query
				->andWhere('date >= ? OR edition_date >= ?', array($targetDate, $targetDate))
				->orderBy('date DESC');
		}
		elseif ($id === self::FOLDER_THIS_MONTH)
		{
			$targetDate = date('Y-m-d H:i:s', strtotime('-1 month'));
			$query
				->andWhere('date >= ? OR edition_date >= ?', array($targetDate, $targetDate))
				->orderBy('date DESC');
		}
		elseif ($id === self::FOLDER_LAST_IMPORT)
		{
			$query
				->andWhereIn('id', !empty($_SESSION['fichiers']['last_import']) ? $_SESSION['fichiers']['last_import'] : array())
				->orderBy('name');
		}
		else
		{
			$query->orderBy('date DESC');
		}
		
		if ($limit)
		{
			$query->limit($limit);
		}
		
		return $query;
	}
}