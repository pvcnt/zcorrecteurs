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
 */

namespace Zco\Bundle\Doctrine1Bundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Yaml\Yaml;

/**
 * Tâche permettant de recréer les modèles de base de Doctrine à partir
 * d'un fichier de schéma YAML.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ModelsCommand extends ContainerAwareCommand
{
	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
			->setName('doctrine:models')
			->setDescription('Builds the models from the schema.yml')
			->addOption('only-base', null, InputOption::VALUE_OPTIONAL, 'Generates only base models.', false);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
	    $cacheDir = $this->getContainer()->getParameter('kernel.cache_dir');
	    $this->getContainer()->get('zco_doctrine1.models_builder')
			->buildBaseModels($cacheDir, $input->getOption('only-base'), $output);
	    		
		return 0;
	}
}
