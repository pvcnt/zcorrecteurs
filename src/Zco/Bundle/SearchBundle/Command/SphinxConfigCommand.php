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

/**
 * Mise en place de Sphinx.
 *
 * @author mwsaz@zcorrecteurs.fr
 */

namespace Zco\Bundle\SearchBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Yaml\Yaml;

class SphinxConfigCommand extends Command
{
	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
			->setName('sphinx:config')
			->setDescription('Bootstraps Sphinx search');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// Création des dossiers
		$sphinxPath = BASEPATH.'/data/store/sphinx';
		foreach (array('config', 'index', 'run') as $dir)
		{
			if (!is_dir($d = $sphinxPath.'/'.$dir))
			{
				mkdir($d, 0777, true);
			}
		}

		// Création du fichier de configuration
		$dbConfig = Yaml::parse(APP_PATH.'/config/parameters.yml');
		$dbConfig = $dbConfig['parameters'];
		$sphinxConfig = file_get_contents($sphinxPath.'/config/sphinx.conf.template');

		// Remplacement des constantes %x%
		$recherche = array('%sphinxpath%' => $sphinxPath);
		foreach (array('host', 'username', 'password', 'base', 'prefix') as $key)
		{
			$recherche['%database.'.$key.'%'] = $dbConfig['database.'.$key];
		}

		$sphinxConfig = str_replace(array_keys($recherche), $recherche, $sphinxConfig);
		file_put_contents($sphinxPath.'/config/sphinx.conf', $sphinxConfig);
		
		return 0;
	}
}
