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
 */

/**
 * Etalage temporel d'une action lourde pour le serveur
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */

namespace Zco\Component\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

define('REQUETES_SECONDE', 2);
define('TEMPS_REQUETE', 1000000 / REQUETES_SECONDE);

class MigrateCommand extends Command
{
	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
			->setName('migrate')
			->setDescription('Runs an important script without killing the server')
			->setDefinition(array(
				new InputArgument('command', InputArgument::REQUIRED, 'The command name'),
            	new InputArgument('arguments', InputArgument::OPTIONAL|InputArgument::IS_ARRAY, 'The arguments'),
        	));
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		/*$command = $input->getArgument('command');
		$class = ucfirst($command).'Command';
		
		if (!class_exists($class))
		{
			$output->writeln('Error: Migration task '.$command.' not found');
			
			return -1;
		}

		set_time_limit(0);
		ignore_user_abort(true);
		ob_implicit_flush(true);

		$dbh = Doctrine_Manager::connection()->getDbh();
		$dbh->log(false);
		$dbh->query("SET NAMES 'utf8'");

		$task = new $class($args, $dbh);

		$finished = false; $i = 0;
		while(!$finished)
		{
			$start = (int)(microtime(true) * 1000000);
			$finished = !$task->runOne($i++);

			$ellapsed = (int)(microtime(true) * 1000000) - $start;
			$sleep = TEMPS_REQUETE - $ellapsed;

			if($sleep > 0) usleep($sleep);

			if($i % 2 == 0)
				echo '#'.$i.' ';
			if($i % 20 == 0)
				echo "\n";
		}

		return "\n";*/
		
		return 0;
	}
}
