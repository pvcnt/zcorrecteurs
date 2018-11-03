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
 *
 * Le code de ce fichier a été fortement inspiré par celui de Jonathan H. Wage 
 * <jonwage@gmail.com> développé pour Doctrine 2 et publié sous licence LGPL.
 */

namespace Zco\Bundle\Doctrine1Bundle\Command;

use Zco\Bundle\Doctrine1Bundle\Migrations\Migration;
use Zco\Bundle\Doctrine1Bundle\Migrations\Configuration\Configuration;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Tâche appliquant une série de migrations sur la base de données afin de la 
 * maintenir à jour sur chaque installation locale du code.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class MigrationsExecuteCommand extends ContainerAwareCommand
{
	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
			->setName('doctrine:migrations:execute')
			->setDescription('Execute a migration to a specified version or the latest available version.')
			->addArgument('version', InputArgument::OPTIONAL, 'The version to migrate to', null)
			->addOption('write-sql', null, InputOption::VALUE_NONE, 'The path to output the migration SQL file instead of executing it')
			->addOption('dry-run', null, InputOption::VALUE_NONE, 'Execute the migration as a dry run')
			->addOption('force', null, InputOption::VALUE_NONE, 'Force all actions without any confirmation')
			->setHelp(<<<EOT
The <info>%command.name%</info> command executes a migration to a specified version or the latest available version:

    <info>%command.full_name%</info>

You can optionally manually specify the version you wish to migrate to:

    <info>%command.full_name% YYYYMMDDHHMMSS</info>

You can also execute the migration as a <comment>--dry-run</comment>:

    <info>%command.full_name% YYYYMMDDHHMMSS --dry-run</info>

You can output the would be executed SQL statements to a file with <comment>--write-sql</comment>:

    <info>%command.full_name% YYYYMMDDHHMMSS --write-sql</info>

Or you can also execute the migration without a warning message which you need to interact with:

    <info>%command.full_name% --force</info>
EOT
		);	
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$configuration = $this->getContainer()->get('zco_doctrine1.migrations.configuration');
		$configuration->registerMigrations();
		
		$version	= $input->getArgument('version');
		$force		= (bool) $input->getOption('force');
		$migration	= new Migration($configuration, $output);

		$executedMigrations  = $configuration->getMigratedVersions();
		$availableMigrations = $configuration->getAvailableVersions();
		$executedUnavailableMigrations = array_diff($executedMigrations, $availableMigrations);
		
		//Si on a des migrations qui ont déjà été exécutées mais sont maintenant 
		//indisponibles (c'est-à-dire que leur définition n'est plus présente), 
		//c'est suspect et on affiche un message d'avertissement.
		if ($executedUnavailableMigrations)
		{
			$output->writeln(sprintf('<error>WARNING! You have %s previously executed migrations in the database that are not registered migrations.</error>', count($executedUnavailableMigrations)));
			foreach ($executedUnavailableMigrations as $executedUnavailableMigration)
			{
				$output->writeln('	<comment>>></comment> ' . $configuration->formatVersion($executedUnavailableMigration) . ' (<comment>' . $executedUnavailableMigration . '</comment>)');
			}
			if (!$force && !$this->getHelper('dialog')->askConfirmation($output, '<question>Are you sure you wish to continue? (y/n)</question>', false))
			{
				return 0;
			}
		}

		//On peut vouloir écrire la migration dans un fichier SQL pouvant être 
		//exécuté plus tard.
		if ($path = $input->getOption('write-sql'))
		{
			$path = is_bool($path) ? getcwd() : $path;
			$migration->writeSqlFile($path, $version);
		}
		//Sinon c'est qu'on veut exécuter les migrations directement sur la base 
		//courante (ou bien effectuer une simulation sur cette base).
		else
		{
			if ((bool) $input->getOption('dry-run'))
			{
				$sql = $migration->migrate($version, true);
			}
			else
			{
				if (!$force && !$this->getHelper('dialog')->askConfirmation($output, '<question>WARNING! You are about to execute a database migration that could result in schema changes and data lost. Are you sure you wish to continue? (y/n)</question>', false))
				{
					return 0;
				}
				$sql = $migration->migrate($version, false);
			}
			
			if (!isset($sql) || empty($sql))
			{
				$output->writeln('<comment>No migration to execute.</comment>');
			}
		}
		
		return 0;
	}
}