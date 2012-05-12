<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Chargement de la structure de base du site et des données indispensables  
 * pour avoir un site local fonctionnel.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Version20120301000000 extends AbstractMigration
{
	public function up(OutputInterface $output)
	{
		$lines = array_filter(array_merge(
			explode("\n", (file_get_contents(__DIR__.'/sql/20120301000000_structure.sql'))),
			explode("\n", (file_get_contents(__DIR__.'/sql/20120301000000_data.sql')))
		));
		
		$query = '';
		foreach ($lines as $line)
		{
			$line = trim($line);
			$query .= $line;
			if (substr($line, -1) === ';')
			{
				$this->addSql(substr($query, 0, -1));
				$query = '';
			}
		}
	}
	
	public function down(OutputInterface $output)
	{
	}
}