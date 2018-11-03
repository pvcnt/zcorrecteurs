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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Tâche permettant de générer automatiquement les fichiers de migration 
 * en comparant l'état de la base aux modèles actuels.
 * Attention, la tâche ne sait actuellement que gérer les ajouts de table.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class MigrationsStatusCommand extends ContainerAwareCommand
{
	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
			->setName('doctrine:migrations:status')
			->setDescription('View the status of a set of migrations.')
			->addOption('show-versions', null, InputOption::VALUE_NONE, 'This will display a list of all available migrations and their status')
			->setHelp(
				'The <info>%command.name%</info> command outputs the status of a set of migrations:'.
				"\n\n".
				'<info>%command.full_name%</info>'.
				"\n\n".
				'You can output a list of all available migrations and their status with '.
				'<comment>--show-versions</comment>:'.
				"\n\n".
				'<info>%command.full_name% --show-versions</info>'
			);
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$container	 = $this->getContainer();
		$configuration = $container->get('zco_doctrine1.migrations.configuration');
		$configuration->registerMigrations();
		
		$currentVersion	  = $configuration->getCurrentVersion();
		$latestVersion	   = $configuration->getLatestVersion();
		$executedMigrations = $configuration->getMigratedVersions();
		$availableMigrations = $configuration->getAvailableVersions();
		$executedUnavailableMigrations = array_diff($executedMigrations, $availableMigrations);
		$numExecutedUnavailableMigrations = count($executedUnavailableMigrations);
		$newMigrations = (count($availableMigrations) + $numExecutedUnavailableMigrations) - count($executedMigrations);
		
		$output->writeln("<info>==</info> Configuration");
		$info = array(
			'Database driver'		=> \Doctrine_Manager::connection()->getDriverName(),
			'Database name'		 	=> $container->getParameter('database.base'),
			'Version table name'	=> $configuration->getMigrationsTableName(),
			'Migrations directory'  => $configuration->getMigrationsDirectory(),
			'Current version'		=> $currentVersion ? sprintf('%s (%s)', $configuration->formatVersion($currentVersion), $currentVersion) : $currentVersion,
			'Latest version'		=> $latestVersion ? sprintf('%s (%s)', $configuration->formatVersion($latestVersion), $latestVersion) : $latestVersion,
			'Executed migrations'   => count($executedMigrations),
			'Available migrations'  => count($availableMigrations),
			'New migrations'		=> $newMigrations > 0 ? '<question>'.$newMigrations.'</question>' : $newMigrations,
		);
		foreach ($info as $name => $value)
		{
			$output->writeln('	<comment>>></comment> '.$name.': '.str_repeat(' ', 40 - strlen($name)) . $value);
		}
		
		$showVersions = $input->getOption('show-versions') ? true : false;
		if ($showVersions === true)
		{
			if ($migrations = $configuration->getMigrations())
			{
				$output->writeln("\n<info>==</info> Available migrations");
				$migratedVersions = $configuration->getMigratedVersions();
				foreach ($migrations as $version)
				{
					$isMigrated = in_array($version->getVersion(), $migratedVersions);
					$status = $isMigrated ? '<info>migrated</info>' : '<error>not migrated</error>';
					$output->writeln('	<comment>>></comment> ' . $configuration->formatVersion($version->getVersion()) . ' (<comment>' . $version->getVersion() . '</comment>)' . str_repeat(' ', 30 - strlen($name)) . $status);
				}
			}

			if ($executedUnavailableMigrations)
			{
				$output->writeln("\n<info>==</info> Previously executed unavailable migrations");
				foreach ($executedUnavailableMigrations as $executedUnavailableMigration)
				{
					$output->writeln('	<comment>>></comment> ' . $configuration->formatVersion($executedUnavailableMigration) . ' (<comment>' . $executedUnavailableMigration . '</comment>)');
				}
			}
		}
	}
}
