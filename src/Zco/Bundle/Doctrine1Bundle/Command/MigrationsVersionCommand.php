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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Tâche permettant d'ajouter ou de supprimer manuellement des versions de la 
 * table des migrations.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class MigrationsVersionCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('doctrine:migrations:version')
			->setDescription('Manually add and delete migration versions from the version table.')
			->addArgument('version', InputArgument::REQUIRED, 'The version to add or delete', null)
			->addOption('add', null, InputOption::VALUE_NONE, 'Add the specified version')
			->addOption('delete', null, InputOption::VALUE_NONE, 'Delete the specified version')
			->setHelp(<<<EOT
The <info>%command.name%</info> command allows you to manually add and delete migration versions from the version table:

	<info>%command.full_name% YYYYMMDDHHMMSS --add</info>

If you want to delete a version you can use the <comment>--delete</comment> option:

	<info>%command.full_name% YYYYMMDDHHMMSS --delete</info>
EOT
			);
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$configuration = $this->getContainer()->get('zco_doctrine1.migrations.configuration');
		$configuration->registerMigrations();

		if ($input->getOption('add') === false && $input->getOption('delete') === false)
		{
			throw new \InvalidArgumentException('You must specify whether you want to --add or --delete the specified version.');
		}

		$version = $input->getArgument('version');
		$markMigrated = (bool) $input->getOption('add');

		if (!$configuration->hasVersion($version))
		{
			throw new \InvalidArgumentException(sprintf('Could not find migration version %s', $version));
		}

		$version = $configuration->getVersion($version);
		if ($markMigrated && $configuration->hasVersionMigrated($version))
		{
			throw new \InvalidArgumentException(sprintf('The version "%s" already exists in the version table.', $version));
		}

		if (!$markMigrated && !$configuration->hasVersionMigrated($version))
		{
			throw new \InvalidArgumentException(sprintf('The version "%s" does not exists in the version table.', $version));
		}

		if ($markMigrated)
		{
			$version->markMigrated();
			$output->writeln(sprintf('Version <info>%s</info> has been marked as migrated', $version->getVersion()));
		}
		else
		{
			$version->markNotMigrated();
			$output->writeln(sprintf('Version <info>%s</info> has been marked as not migrated', $version->getVersion()));
		}
	}
}