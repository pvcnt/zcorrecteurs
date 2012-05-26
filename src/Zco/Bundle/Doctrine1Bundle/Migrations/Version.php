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
 *
 * Le code de ce fichier a été fortement inspiré par celui de Jonathan H. Wage 
 * <jonwage@gmail.com> développé pour Doctrine 2 et publié sous licence LGPL.
 */

namespace Zco\Bundle\Doctrine1Bundle\Migrations;

use Zco\Bundle\Doctrine1Bundle\Migrations\Configuration\Configuration;
use Zco\Bundle\Doctrine1Bundle\Migrations\Exception\SkipMigrationException;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class which wraps a migration version and allows execution of the
 * individual migration version up or down method.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Version
{
	const STATE_NONE = 0;
	const STATE_PRE  = 1;
	const STATE_EXEC = 2;
	const STATE_POST = 3;

	private $configuration;
	private $version;
	private $migration;
	private $connection;
	private $class;
	private $sql = array();
	private $params = array();
	private $types = array();
	private $time;
	private $state = self::STATE_NONE;

	public function __construct(Configuration $configuration, $version, $class)
	{
		$this->configuration = $configuration;
		$this->class         = $class;
		$this->connection    = \Doctrine_Manager::connection();
		$this->migration     = new $class($this);
		$this->version       = $this->migration->getName() ?: $version;
	}
	
	/**
	 * Retourne le nom de la version au format YYYYMMJJHHMMSS.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->version;
	}

	/**
	 * Retourne le nom de la version au format YYYYMMJJHHMMSS.
	 *
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * Retourne l'instance de la configuration des migrations.
	 *
	 * @return Configuration
	 */
	public function getConfiguration()
	{
		return $this->configuration;
	}

	/**
	 * Check if this version has been migrated or not.
	 *
	 * @param  bool $bool
	 * @return mixed
	 */
	public function isMigrated()
	{
		return $this->configuration->hasVersionMigrated($this);
	}

	public function markMigrated()
	{
		$this->configuration->createMigrationTable();
		$this->connection->getDbh()->exec('INSERT INTO '.$this->configuration->getMigrationsTableName().' VALUES(\''.$this->version.'\')');
	}

	public function markNotMigrated()
	{
		$this->configuration->createMigrationTable();
		$this->connection->getDbh()->exec('DELETE FROM '.$this->configuration->getMigrationsTableName().' WHERE version = \''.$this->version.'\'');
	}

	/**
	 * Add some SQL queries to this versions migration
	 *
	 * @param  mixed $sql
	 * @param  array $params
	 * @param  array $types
	 * @return void
	 */
	public function addSql($sql, array $params = array(), array $types = array())
	{
		if (is_array($sql))
		{
			foreach ($sql as $key => $query)
			{
				$this->sql[] = $query;
				if (isset($params[$key]))
				{
					$this->params[count($this->sql) - 1] = $params[$key];
					$this->types[count($this->sql) - 1]  = isset($types[$key]) ? $types[$key] : array();
				}
			}
		}
		else
		{
			$this->sql[] = $sql;
			if ($params) 
			{
				$this->params[count($this->sql) - 1] = $params;
				$this->types[count($this->sql) - 1]  = $types ?: array();
			}
		}
	}

	/**
	 * @return AbstractMigration
	 */
	public function getMigration()
	{
		return $this->migration;
	}

	/**
	 * Execute this migration version up or down and and return the SQL.
	 *
	 * @param  string $direction   The direction to execute the migration.
	 * @param  string $dryRun	  Whether to not actually execute the migration SQL and just do a dry run.
	 * @return array $sql
	 * @throws Exception when migration fails
	 */
	public function execute($direction, OutputInterface $output, $dryRun = false)
	{
		$this->sql = array();

		$this->connection->beginTransaction();

		try
		{
			$start = microtime(true);

			$this->state = self::STATE_PRE;
			$this->migration->{'pre' . ucfirst($direction)}($output);

			if ($direction === 'up')
			{
				$output->writeln(sprintf('  <info>++</info> migrating <comment>%s</comment>', $this->version));
			}
			else
			{
				$output->writeln(sprintf('  <info>--</info> reverting <comment>%s</comment>', $this->version));
			}

			$this->state = self::STATE_EXEC;
			$this->migration->$direction($output);

			if ($dryRun === false)
			{
				if ($this->sql)
				{
					$dbh = $this->connection->getDbh();
					
					foreach ($this->sql as $key => $query)
					{
						if (!isset($this->params[$key]))
						{
							$output->writeln('	 <comment>-></comment> ' . $query);
							$dbh->exec($query);
						}
						else
						{
							$output->writeln(sprintf('	<comment>-</comment> %s (with parameters)', $query));
							$stmt = $dbh->prepare($query);
							foreach ($this->params[$key] as $k => $v)
							{
								$stmt->bindValue($k, $v, isset($this->types[$key][$k]) ? $this->types[$key][$k] : \PDO::PARAM_STR);
							}
							$stmt->execute();
							$stmt->fetchAll();
						}
					}
				}
				else
				{
					$output->writeln(sprintf('<error>Migration %s was executed but did not result in any SQL statements.</error>', $this->version));
				}

				if ($direction === 'up')
				{
					$this->markMigrated();
				}
				else
				{
					$this->markNotMigrated();
				}

			}
			else
			{
				foreach ($this->sql as $query)
				{
					$output->write('	 <comment>-></comment> ' . $query);
				}
			}

			$this->state = self::STATE_POST;
			$this->migration->{'post' . ucfirst($direction)}($output);

			$end = microtime(true);
			$this->time = round($end - $start, 2);
			if ($direction === 'up')
			{
				$output->writeln(sprintf("  <info>++</info> migrated (%ss)", $this->time));
			}
			else
			{
				$output->writeln(sprintf("  <info>--</info> reverted (%ss)", $this->time));
			}

			$this->connection->commit();

			return $this->sql;
		}
		catch (SkipMigrationException $e)
		{
			$this->connection->rollback();

			if ($dryRun == false)
			{
				// now mark it as migrated
				if ($direction === 'up')
				{
					$this->markMigrated();
				}
				else
				{
					$this->markNotMigrated();
				}
			}

			$output->writeln(sprintf("  <info>SS</info> skipped (%s)",  $e->getMessage()));
		}
		catch (\Exception $e)
		{
			$output->writeln(sprintf(
				'<error>Migration %s failed during %s.</error>',
				$this->version, $this->getExecutionState()
			));

			$this->connection->rollback();
			$this->state = self::STATE_NONE;
			
			throw $e;
		}
		$this->state = self::STATE_NONE;
	}

	/**
	 * Retourne une version lisible de l'état courant de l'exécution de la 
	 * migration de cette version.
	 * 
	 * @return string
	 */
	public function getExecutionState()
	{
		switch ($this->state)
		{
			case self::STATE_PRE:
				return 'pre-checks';
			case self::STATE_POST:
				return 'post-checks';
			case self::STATE_EXEC:
				return 'execution';
			default:
				return 'no state';
		}
	}

	/**
	 * Returns le temps que cette version a mis pour exécuter sa migration.
	 *
	 * @return integer
	 */
	public function getTime()
	{
		return $this->time;
	}
}