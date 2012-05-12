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

namespace Zco\Bundle\CoreBundle\Cache;

/**
 * Implémentation d'un cache stockant toutes les données sur le 
 * système de fichiers.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class FileCache extends AbstractCache
{
	protected $path;
	
	/**
	 * Constructeur.
	 *
	 * @param string $path Chemin physique vers le cache
	 * @param integer $defaultLifetime Durée de vie par défaut des caches
	 */
	public function __construct($path, $defaultLifetime)
	{
		$this->path = rtrim($path, '/');
		
		if (!is_dir($this->path))
		{
			if (false === @mkdir($this->path, 0777, true))
			{
				throw new \RuntimeException(sprintf('Failed to create the cache directory "%s".', $this->path));
			}
		}
		if (!is_writable($this->path))
		{
			throw new \RuntimeException(sprintf('Cannot write into cache directory "%s".', $this->path));
		}
		
		parent::__construct($defaultLifetime);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function read($id)
	{
		return unserialize(file_get_contents($this->buildPath($id)));
	}

	/**
	 * {@inheritdoc}
	 */
	protected function write($id, $data, $lifetime)
	{
		$path = $this->buildPath($id);
		$directory = dirname($path);
		
		if (!is_dir($directory))
		{
			if (false === @mkdir($directory, 0777, true))
			{
				throw new \RuntimeException(sprintf('Failed to create the cache directory "%s".', $directory));
			}
		}
		if (!is_writable($directory))
		{
			throw new \RuntimeException(sprintf('Cannot write into cache directory "%s".', $directory));
		}
		
		file_put_contents($path, serialize($data));
		@chmod($path, 0777);
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function remove($id)
	{
		if (is_file($path = $this->buildPath($id)))
		{
			@unlink($path);
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function readIndex()
	{
		if (is_file($indexPath = $this->path.'/'.self::INDEX_ID))
		{
			$this->index = unserialize(file_get_contents($indexPath));
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function writeIndex()
	{
		file_put_contents($this->path.'/'.self::INDEX_ID, serialize($this->index));
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function exists($id)
	{
		return parent::exists($id) && is_file($this->buildPath($id));
	}
	
	/**
	 * Construit le chemin physique menant vers un fichier cache. Les 
	 * fichiers cache sont répartis en sous-dossiers sur deux niveaux afin 
	 * d'éviter d'engorger le système de fichiers avec trop de fichiers dans 
	 * le même dossier. Ces dossiers sont construits à partir du début d'un hash 
	 * de l'identifiant. Le hash permet également de sécuriser l'accès aux 
	 * ressources en cas d'identifiant malveillant (../, /home, etc.).
	 * 
	 * @param  string $id L'identifiant du cache
	 * @return string Le chemin physique vers le cache
	 */
	protected function buildPath($id)
	{
		$hash = md5($id);
		
		return $this->path.'/'.$hash[0].'/'.substr($hash, 0, 2).'/'.$hash;
	}
}
