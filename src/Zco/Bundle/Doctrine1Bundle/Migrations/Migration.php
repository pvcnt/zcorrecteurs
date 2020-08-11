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
 *
 * Le code de ce fichier a été fortement inspiré par celui de Jonathan H. Wage 
 * <jonwage@gmail.com> développé pour Doctrine 2 et publié sous licence LGPL.
 */

namespace Zco\Bundle\Doctrine1Bundle\Migrations;

use Zco\Bundle\Doctrine1Bundle\Migrations\Configuration\Configuration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Classe gérant le lancement effectif des migrations vers la dernière version 
 * ou une version indiquée manuellement.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Migration
{
	private $configuration;
	private $output;

	/**
	 * Constructeur.
	 *
	 * @param Configuration $configuration Configuration du composant de migrations
	 * @param OutputInterface $output Pour afficher un retour à l'utilisateur
	 */
	public function __construct(Configuration $configuration, OutputInterface $output)
	{
		$this->configuration = $configuration;
		$this->output		 = $output;
	}
	
	/**
	 * Retourne le composant permettant d'afficher un retour à l'utilisateur.
	 *
	 * @return OutputInterface
	 */
	public function getOutput()
	{
		return $this->output;
	}
	
	/**
	 * Écrit les requêtes SQL nécessaires à l'exécution d'une migration dans un 
	 * fichier à un emplacement donné.
	 *
	 * @param  string $path Le chemin où écrire le fichier de migration
	 * @param  string $to La version vers laquelle on souhaite migrer
	 * @return bool Le fichier a-t-il été correctement écrit ?
	 */
	public function writeSqlFile($path, $to = null)
	{
		$sql = $this->migrate($to, true);

		$from = $this->configuration->getCurrentVersion();
		if ($to === null)
		{
			$to = $this->configuration->getLatestVersion();
		}

		$string  = sprintf("# Doctrine Migration File Generated on %s\n", date('Y-m-d H:m:s'));
		$string .= sprintf("# Migrating from %s to %s\n", $from, $to);

		foreach ($sql as $version => $queries)
		{
			$string .= "\n# Version " . $version . "\n";
			foreach ($queries as $query)
			{
				$string .= $query . ";\n";
			}
		}
		if (is_dir($path))
		{
			$path = realpath($path);
			$path = $path . '/doctrine_migration_' . date('YmdHis') . '.sql';
		}

		$this->output->writeln(sprintf('Writing migration file to "<info>%s</info>"', $path));

		return file_put_contents($path, $string);
	}

	/**
	 * Lance l'exécution d'une migration vers la dernière version ou une version 
	 * indiquée manuellement. L'exécution peut être réelle ou simplement une 
	 * simulation permettant de connaître les requêtes qui doivent être exécutées.
	 *
	 * @param  string $to La version vers laquelle on souhaite migrer
	 * @param  string $dryRun Doit-on effectivement exécuter les requêtes ?
	 * @return array La liste des requêtes SQL effectuées
	 *
	 * @throws \InvalidArgumentException Si la version cible n'est pas valide
	 * @throws \LogicException S'il n'y a aucune migration à effectuer
	 */
	public function migrate($to = null, $dryRun = false)
	{
		if ($to === null)
		{
			$to = $this->configuration->getLatestVersion();
		}

		$from = $this->configuration->getCurrentVersion();
		$from = (string) $from;
		$to = (string) $to;

		$migrations = $this->configuration->getMigrations();
		if (!isset($migrations[$to]) && $to > 0)
		{
			throw new \InvalidArgumentException(sprintf('Could not find migration version %s', $to));
		}

		$direction = $from > $to ? 'down' : 'up';
		$migrationsToExecute = $this->configuration->getMigrationsToExecute($direction, $to);

		if ($from === $to && empty($migrationsToExecute) && $migrations)
		{
			return array();
		}

		if ($dryRun === false)
		{
			$this->output->writeln(sprintf('Migrating <info>%s</info> to <comment>%s</comment> from <comment>%s</comment>', $direction, $to, $from));
		}
		else
		{
			$this->output->writeln(sprintf('Executing dry run of migration <info>%s</info> to <comment>%s</comment> from <comment>%s</comment>', $direction, $to, $from));
		}

		if (empty($migrationsToExecute))
		{
			throw new \LogicException('Could not find any migrations to execute.');
		}

		$sql = array();
		$time = 0;
		foreach ($migrationsToExecute as $version)
		{
			$versionSql = $version->execute($direction, $this->output, $dryRun);
			$sql[$version->getVersion()] = $versionSql;
			$time += $version->getTime();
		}

		$this->output->writeln("  <comment>------------------------</comment>");
		$this->output->writeln(sprintf("  <info>++</info> finished in %s", $time));
		$this->output->writeln(sprintf("  <info>++</info> %s migrations executed", count($migrationsToExecute)));
		$this->output->writeln(sprintf("  <info>++</info> %s sql queries", count($sql, true) - count($sql)));

		return $sql;
	}
}