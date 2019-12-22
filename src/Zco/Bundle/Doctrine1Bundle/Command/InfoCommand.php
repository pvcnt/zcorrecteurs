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

namespace Zco\Bundle\Doctrine1Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Affiche la liste de toutes les entités reconnues par Doctrine.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class InfoCommand extends ContainerAwareCommand
{
	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
			->setName('doctrine:info')
			->setDescription('Shows basic information about all entities')
			->setHelp(
				'The <info>doctrine:info</info> shows the list of all entities '.
				'that where found and the bundle where they are defined.'
		);
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$count = false;
		foreach ($this->getContainer()->get('kernel')->getBundles() as $bundle)
		{
			$entityDir = $bundle->getPath().'/Entity';
			if (!is_dir($entityDir))
			{
				continue;
			}
			
			$files = glob($entityDir.'/*.class.php');
			$output->write(sprintf(
				'Found <info>%s</info> entities for bundle "<info>%s</info>": ', 
				ceil(count($files) / 2),
				$bundle->getName()
			));
			
			$c = 0;
			foreach ($files as $file)
			{
				if (!preg_match('/Table\.class\.php$/', $file))
				{
					if ($c++)
					{
						$output->write(', ');
					}
					$output->write(substr(preg_replace('/\.class\.php$/', '', $file), strrpos($file, '/') + 1));
					$found = true;
				}
			}
			$output->writeln('');
		}

		if (!$found)
		{
			throw new \LogicException('You do not have any entity for any of your bundles.');
		}
		
		return 0;
	}
}