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

namespace Zco\Bundle\Doctrine1Bundle\Command;

use Zco\Bundle\Doctrine1Bundle\Migrations\Configuration\Configuration;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MigrationsGenerateCommand extends ContainerAwareCommand
{
	private static $template =
'<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Classe de migration auto-générée. Modifiez-la selon vos besoins !
 */
class Version<version> extends AbstractMigration
{
	public function up(OutputInterface $output)
	{<up>
	}

	public function down(OutputInterface $output)
	{<down>
	}
}';
	
	protected function configure()
	{
		$this
			->setName('doctrine:migrations:generate')
			->setDescription('Generate a blank migration file.')
			->addOption('editor', null, InputOption::VALUE_OPTIONAL, 'Open file with this command upon creation')
			->setHelp(
				'The <info>%command.name%</info> command generates a blank migration file:'.
				"\n\n".
				'<info>%command.full_name%</info>'.
				"\n\n".
				'You can optionally specify a <comment>--editor</comment> option to open '.
				'the generated file in your favorite editor:'.
				"\n\n".
				'<info>%command.full_name% --editor=mate</info>'
			);
	}
	
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$version = date('YmdHis');
		$path    = $this->generateMigration($version, $input);
		$output->writeln(sprintf('Generated new migration class to "<info>%s</info>"', $path));
	}

	protected function generateMigration($version, InputInterface $input, array $upSql = array(), array $downSql = array())
	{
		$configuration = $this->getContainer()->get('zco_doctrine1.migrations.configuration');
		$directory     = rtrim($configuration->getMigrationsDirectory(), '/');
		$path	       = $directory.'/Version'.$version.'.php';
		
		$up = array();
		foreach ($upSql as $query)
		{
			$up [] = "\n\t\t".'$this->addSql("'.str_replace('"', '\\"', $query).'");';
		}
		$up = implode('', $up);
		
		$down = array();
		foreach ($downSql as $query)
		{
			$down [] = "\n\t\t".'$this->addSql("'.str_replace('"', '\\"', $query).'");';
		}
		$down = implode('', $down);
		
		$code = str_replace(array('<version>', '<up>', '<down>'), array($version, $up, $down), self::$template);
		
		if (!file_exists($directory))
		{
			throw new \InvalidArgumentException(sprintf('Migrations directory "%s" does not exist.', $directory));
		}

		file_put_contents($path, $code);
		
		if ($editor = $input->getOption('editor'))
		{
			shell_exec($editor.' '.escapeshellarg($path));
		}

		return $path;
	}
}