<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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
 *
 * Le code de ce fichier a été fortement inspiré par celui de Jonathan H. Wage 
 * <jonwage@gmail.com> développé pour Doctrine 2 et publié sous licence LGPL.
 */

namespace Zco\Bundle\Doctrine1Bundle\Migrations\Configuration;

use Zco\Bundle\Doctrine1Bundle\Migrations\Version;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Configuration
{
	private $directory;
	private $container;
	private $migrations = array();
	private $migrationTableCreated = false;
	
	public function __construct($directory, ContainerInterface $container)
	{
		$this->directory = realpath($directory);
		$this->container = $container;
	}
	
	public function getContainer()
	{
		return $this->container;
	}
	
	public function getVersionPath($version)
	{
		return $this->hasVersion($version) ? $this->migrations[$version] : null;
	}
	
	public function registerMigrations()
	{
		$iterator = new \DirectoryIterator($this->directory);
		foreach ($iterator as $file)
		{
			if ($file->isFile())
			{
				include($file->getRealPath());
				$class = substr($file->getFilename(), 0, -4);
				$version = preg_replace('/[^0-9]/', '', $class);
				$this->migrations[$version] = new Version($this, $version, $class);
			}
		}
		
		ksort($this->migrations);
	}
	
	public function getMigrationsDirectory()
	{
		return $this->directory;
	}
	
	public function getMigrationsTableName()
	{
		return 'zcov2_schema_version';
	}
	
	public function getMigrations()
	{
		return $this->migrations;
	}
	
	public function getNumberOfAvailableMigrations()
	{
		return count($this->migrations);
	}
	
	public function getLatestVersion()
	{
		$versions = array_keys($this->migrations);
		$latest = end($versions);
		
		return ($latest !== false) ? $latest : 0;
	}
	
	/**
	 * Returns an array of available migration version numbers.
	 *
	 * @return array $availableVersions
	 */
	public function getAvailableVersions()
	{
		$availableVersions = array();
		foreach ($this->migrations as $migration)
		{
			$availableVersions[] = $migration->getVersion();
		}
		
		return $availableVersions;
	}
	
	public function getCurrentVersion()
	{
		$this->createMigrationTable();
		
		$dbh  = \Doctrine_Manager::connection()->getDbh();
		$stmt = $dbh->prepare(
			'SELECT version '.
			'FROM '.$this->getMigrationsTableName().' '.
			'ORDER BY version DESC '.
			'LIMIT 1'
		);
		$stmt->execute();
		$row = $stmt->fetchColumn();
		
		return ($row === false) ? 0 : $row;
	}
	
	public function getMigratedVersions()
	{
		$this->createMigrationTable();
		
		$dbh  = \Doctrine_Manager::connection()->getDbh();
		$stmt = $dbh->prepare(
			'SELECT version '.
			'FROM '.$this->getMigrationsTableName()
		);
		$stmt->execute();
		
		$versions = array();
		$rows	 = $stmt->fetchAll();
		foreach ($rows as $row)
		{
			$versions[] = current($row);
		}
		
		return $versions;
	}
	
	public function getNumberOfExecutedMigrations()
	{
		$this->createMigrationTable();
		
		$dbh  = \Doctrine_Manager::connection()->getDbh();
		$stmt = $dbh->prepare(
			'SELECT COUNT(*) '.
			'FROM '.$this->getMigrationsTableName()
		);
		$stmt->execute();
		$count = $stmt->fetchColumn();
		
		return ($count === false) ? 0 : (int) $count;
	}
	
	public function hasVersion($version)
	{
		return isset($this->migrations[$version]);
	}
	
	/**
 	 * Returns the Version instance for a given version in the format YYYYMMDDHHMMSS.
	 *
	 * @param string $version   The version string in the format YYYYMMDDHHMMSS.
	 * @return Version $version
	 * @throws \InvalidArgumentException Si la version spécifiée n'existe pas
	 */
	public function getVersion($version)
	{
		if (!isset($this->migrations[$version]))
		{
			throw new \InvalidArgumentException(sprintf('Could not find migration version %s', $version));
		}
		
		return $this->migrations[$version];
	}
	
	public function hasVersionMigrated($version)
	{
		$this->createMigrationTable();
		
		$dbh  = \Doctrine_Manager::connection()->getDbh();
		$stmt = $dbh->prepare(
			'SELECT COUNT(*) '.
			'FROM '.$this->getMigrationsTableName().' '.
			'WHERE version = :version'
		);
		$stmt->execute(array(':version' => $version));
		
		return $stmt->fetchColumn() > 0;
	}
	
	public function formatVersion($version)
	{
		return sprintf('%s-%s-%s %s:%s:%s',
			substr($version, 0, 4),
			substr($version, 4, 2),
			substr($version, 6, 2),
			substr($version, 8, 2),
			substr($version, 10, 2),
			substr($version, 12, 2)
		);
	}
	
	public function createMigrationTable()
	{
		if ($this->migrationTableCreated)
		{
			return;
		}
		
		$dbh  = \Doctrine_Manager::connection()->getDbh();
		$stmt = $dbh->prepare(
			'CREATE TABLE IF NOT EXISTS '.
			$this->getMigrationsTableName().' ('.
				'version VARCHAR(255) NOT NULL'.
			')');
		$stmt->execute();
		
		$this->migrationTableCreated = true;
	}
	
	/**
	 * Returns the array of migrations to executed based on the given direction
	 * and target version number.
	 *
	 * @param string $direction	The direction we are migrating.
	 * @param string $to		   The version to migrate to.
	 * @return array $migrations   The array of migrations we can execute.
	 */
	public function getMigrationsToExecute($direction, $to)
	{
		if ($direction === 'down')
		{
			if (count($this->migrations))
			{
				$allVersions = array_reverse(array_keys($this->migrations));
				$classes = array_reverse(array_values($this->migrations));
				$allVersions = array_combine($allVersions, $classes);
			}
			else
			{
				$allVersions = array();
			}
		}
		else
		{
			$allVersions = $this->migrations;
		}
		$versions = array();
		$migrated = $this->getMigratedVersions();
		foreach ($allVersions as $version)
		{
			if ($this->shouldExecuteMigration($direction, $version, $to, $migrated))
			{
				$versions[$version->getVersion()] = $version;
			}
		}
		
		return $versions;
	}

	/**
	 * Check if we should execute a migration for a given direction and target
	 * migration version.
	 *
	 * @param  string $direction   The direction we are migrating.
	 * @param  Version $version	The Version instance to check.
	 * @param  string $to		  The version we are migrating to.
	 * @param  array $migrated	 Migrated versions array.
	 * @return void
	 */
	private function shouldExecuteMigration($direction, Version $version, $to, $migrated)
	{
		if ($direction === 'down')
		{
			if ( ! in_array($version->getVersion(), $migrated))
			{
				return false;
			}
			
			return $version->getVersion() > $to;
		}
		elseif ($direction === 'up')
		{
			if (in_array($version->getVersion(), $migrated))
			{
				return false;
			}
			
			return $version->getVersion() <= $to;
		}
	}
}