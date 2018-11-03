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

use Zco\Bundle\Doctrine1Bundle\Model\NamedDoctrineTableInterface;

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
	
	/**
	 * Retourne la liste des dossiers intelligents (qui sont des dossiers virtuels 
	 * permettant de regrouper des fichiers de façon dynamique par rapport à 
	 * leur méta-données).
	 *
	 * @return array
	 */
	public static function getSmartFolders()
	{
		return array(
			self::FOLDER_LAST_IMPORT => array(
				'id' => self::FOLDER_LAST_IMPORT,
				'name' => 'Dernier import',
				'icon' => 'inbox',
				'hidden' => true,
			),
			self::FOLDER_ALL => array(
				'id' => self::FOLDER_ALL,
				'name' => 'Tous les fichiers',
				'icon' => 'home',
				'hidden' => false,
			),
			self::FOLDER_PICTURES => array(
				'id' => self::FOLDER_PICTURES,
				'name' => 'Images',
				'icon' => 'picture',
				'hidden' => false,
			),
			self::FOLDER_AUDIO => array(
				'id' => self::FOLDER_AUDIO,
				'name' => 'Fichiers audio',
				'icon' => 'music',
				'hidden' => false,
			),
			self::FOLDER_DOCUMENTS => array(
				'id' => self::FOLDER_DOCUMENTS,
				'name' => 'Documents',
				'icon' => 'file',
				'hidden' => false,
			),
			self::FOLDER_THIS_WEEK => array(
				'id' => self::FOLDER_THIS_WEEK,
				'name' => 'Cette semaine',
				'icon' => 'time',
				'hidden' => false,
			),
			self::FOLDER_THIS_MONTH => array(
				'id' => self::FOLDER_THIS_MONTH,
				'name' => 'Ce mois',
				'icon' => 'time',
				'hidden' => false,
			),
		);
	}
	
	/**
	 * Retourne la liste des dossiers de contenu (qui sont des dossiers virtuels 
	 * permettant de regrouper des fichiers de façon dynamique par rapport aux 
	 * contenus auxquels ils sont liés).
	 *
	 * @param  integer|null $userId L'utilisateur dont on veut récupérer les 
	 *                              dossiers (null pour tous)
	 * @return array
	 */
	public function getContentFolders($userId = null)
	{
		$query = \Doctrine_Query::create()
			->select('u.entity_class')
			->distinct()
			->from('FileUsage u');
		
		if ($userId)
		{
			$query
				->leftJoin('u.File f')
				->where('f.user_id = ?', $userId);
		}
		$rows = $query->execute(array(), \Doctrine_Core::HYDRATE_ARRAY);
		
		$folders = array();
		foreach ($rows as $row)
		{
			$table = \Doctrine_Core::getTable($row['entity_class']);
			$folders[$row['entity_class']] = array(
				'id'     => $row['entity_class'],
				'name'   => ($table instanceof NamedDoctrineTableInterface) ? $table->getName() : $row['entity_class'],
				'hidden' => false,
				'icon'   => 'tags',
			);
		}
		
		return $folders;
	}
	
	/**
	 * Retourne l'espace disque utilisé par un utilisateur. On ne prend en compte 
	 * que les fichiers originaux et pas les miniatures.
	 *
	 * @param  integer $userId L'utilisateur dont on veut avoir l'espace occupé
	 * @return integer
	 */
	public function getSpaceUsage($userId)
	{
		return $this
			->getSpaceUsageQuery($userId)
			->execute(array(), \Doctrine_Core::HYDRATE_SINGLE_SCALAR);
	}
	
	/**
	 * Retourne la requête permettant de connaître l'espace disque utilisé par un 
	 * utilisateur. On ne prend en compte que les fichiers originaux et pas les 
	 * miniatures.
	 *
	 * @param  integer $userId L'utilisateur dont on veut avoir l'espace occupé
	 * @return Doctrine_Query
	 */
	public function getSpaceUsageQuery($userId)
	{
		return $this->createQuery()
			->select('SUM(size)')
			->where('user_id = ?', $userId)
			->andWhere('quota_affected = ?', true);
	}
	
	/**
	 * Retourne un fichier à partir de son identifiant.
	 *
	 * @param  integer $id L'identifiant du fichier à récupérer
	 * @return File
	 */
	public function getById($id)
	{
		return $this->getByIdQuery($id)->fetchOne();
	}

	/**
	 * Retourne la requête permettant de récupérer un fichier à partir de son 
	 * identifiant.
	 *
	 * @param  integer $id L'identifiant du fichier à récupérer
	 * @return Doctrine_Query
	 */
	public function getByIdQuery($id)
	{
		return $this->createQuery('f')
			->select('f.*, t.*, l.*')
			->leftJoin('f.Thumbnail t')
			->leftJoin('f.License l')
			->where('f.id = ?', $id)
			->limit(1);
	}
	
	/**
	 * Récupère la liste des fichiers appartenant à un certain dossier et effectue 
	 * dans la foulée une recherche sur le nom du fichier.
	 *
	 * @param  integer $folder Le dossier dans lequel chercher
	 * @param  integer $userId L'utilisateur pour lequel on veut récupérer les fichiers
	 * @param  string $search Le masque de recherche sur le nom du fichier
	 * @param  integer|null $limit Nombre maximum de fichiers à renvoyer (null pour aucune limite)
	 * @return Doctrine_Collection
	 */
	public function getByFolderAndSearch($folder, $userId, $search, $entities = null, $offset = null, $limit = null)
	{
		return $this->getByFolderAndSearchQuery($folder, $userId, $search, $entities, $offset, $limit)->execute();
	}

	/**
	 * Retourne la requête permettant de récupérer la liste des fichiers appartenant 
	 * à un certain dossier et effectue dans la foulée une recherche sur le nom du 
	 * fichier.
	 *
	 * @param  integer $folder Le dossier dans lequel chercher
	 * @param  integer $userId L'utilisateur pour lequel on veut récupérer les fichiers
	 * @param  string $search Le masque de recherche sur le nom du fichier
	 * @param  integer|null $limit Nombre maximum de fichiers à renvoyer (null pour aucune limite)
	 * @return Doctrine_Query
	 */
	public function getByFolderAndSearchQuery($folder, $userId, $search, $entities = null, $offset = null, $limit = null)
	{
		return $this
			->getByFolderQuery($folder, $userId, $entities, $offset, $limit)
			->andWhere('name LIKE ? ESCAPE \'#\'', '%'.str_replace(
				'*',
				'%', 
				str_replace(array('%', '_'), array('#%', '_%'), $search)
			).'%');
	}
	
	/**
	 * Récuère la liste des fichiers appartenant à un certain dossier.
	 *
	 * @param  integer $folder Le dossier dans lequel chercher
	 * @param  integer $userId L'utilisateur pour lequel on veut récupérer les fichiers
	 * @param  integer|null $limit Nombre maximum de fichiers à renvoyer (null pour aucune limite)
	 * @return Doctrine_Collection
	 */
	public function getByFolder($folder, $userId, $entities = null, $offset = null, $limit = null)
	{
		return $this->getByFolderQuery($folder, $userId, $entities, $offset, $limit)->execute();
	}

	/**
	 * Retourne la requête permettant de récupérer la liste des fichiers appartenant 
	 * à un certain dossier.
	 *
	 * @param  integer $folder Le dossier dans lequel chercher
	 * @param  integer $userId L'utilisateur pour lequel on veut récupérer les fichiers
	 * @param  integer|null $limit Nombre maximum de fichiers à renvoyer (null pour aucune limite)
	 * @return Doctrine_Query
	 */
	public function getByFolderQuery($id, $userId, $entities = null, $offset = null, $limit = null)
	{
		$query = $this->createQuery('f')
			->select('f.*, t.*')
			->leftJoin('f.Thumbnail t')
			->where('f.user_id = ?', $userId);
		
		if ($entities)
		{
			$query->innerJoin('f.Usage u WITH u.entity_class = ?', $entities);
		}
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
				->andWhere('date >= ?', $targetDate)
				->orderBy('edition_date DESC');
		}
		elseif ($id === self::FOLDER_THIS_MONTH)
		{
			$targetDate = date('Y-m-d H:i:s', strtotime('-1 month'));
			$query
				->andWhere('date >= ?', $targetDate)
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
		if ($offset)
		{
			$query->offset($offset);
		}
		
		return $query;
	}
}