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
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Tâche permettant de générer automatiquement les fichiers de migration 
 * en comparant l'état de la base aux modèles actuels.
 * Attention, la tâche ne sait actuellement que gérer les ajouts de table.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class MigrationsDiffCommand extends MigrationsGenerateCommand
{
	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		parent::configure();
		
		$this
			->setName('doctrine:migrations:diff')
			->setDescription('Generate a migration by comparing your current database to your mapping information.')
			->setHelp(<<<EOT
The <info>%command.name%</info> command generates a migration by comparing your current database to your mapping information:

    <info>%command.full_name%</info>

You can optionally specify a <comment>--editor</comment> option to open the generated file in your favorite editor:

    <info>%command.full_name% --editor=mate</info>
EOT
			);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$configuration = $this->getContainer()->get('zco_doctrine1.migrations.configuration');
		$cacheDir      = $this->getContainer()->getParameter('kernel.cache_dir').'/zco_doctrine1';
		$dbh	       = \Doctrine_Manager::connection()->getDbh();
		
		$stmt = $dbh->prepare("SHOW TABLES");
		$stmt->execute();
		$rows   = $stmt->fetchAll(\PDO::FETCH_BOTH);
		$tables = array();
		foreach ($rows as $row)
		{
			$tables[] = $row[0];
		}
				
		$upSql        = array();
		$downSql      = array();
		$conn	      = \Doctrine_Manager::connection();
		$iterator     = new \DirectoryIterator($cacheDir.'/generated');
		
		foreach ($iterator as $file)
		{
			if (!$file->isDot())
			{
				$model = preg_replace('/\.class\.php$/', '', $file->getFilename());
				$model = substr($model, 4);
				$table = \Doctrine_Core::getTable($model);
				
				if (!in_array($table->getTableName(), $tables))
				{
					$upSql = array_merge($upSql, $conn->export->createTableSql($table->getTableName(), $table->getColumns()));
					$downSql[] = $conn->export->dropTableSql($table->getTableName());
				}
			}
		}
		
		if (!empty($upSql) || !empty($downSql))
		{
			$version = date('YmdHis');
			$path    = $this->generateMigration($version, $input, $upSql, $downSql);
			$output->writeln(sprintf('Generated new migration class to "<info>%s</info>" from schema differences', $path));
		}
		else
		{
			$output->writeln('No changes detected in your mapping information');
		}
				
		return 0;
	}
}
