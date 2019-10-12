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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Zco\Bundle\Doctrine1Bundle\Migrations\MigrationGenerator;

class MigrationsGenerateCommand extends Command
{
    private $generator;

    /**
     * Constructor.
     *
     * @param MigrationGenerator $generator Migrations generator.
     */
    public function __construct(MigrationGenerator $generator)
    {
        parent::__construct('doctrine:migrations:generate');
        $this->generator = $generator;
    }
	
	protected function configure()
	{
		$this
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
        $path = $this->generator->generate($version, $input->getOption('editor'));
		$output->writeln(sprintf('Generated new migration class to "<info>%s</info>"', $path));
	}
}