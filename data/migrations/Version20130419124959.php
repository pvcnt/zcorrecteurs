<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Classe de migration auto-générée. Modifiez-la selon vos besoins !
 */
class Version20130419124959 extends AbstractMigration
{
	public function up(OutputInterface $output)
	{
            $this->addSql('ALTER TABLE zcov2_publicites ADD aff_accueil TINYINT(1)');
            //$this->addSql('DROP TABLE zcov2_publicites_categories');
	}

	public function down(OutputInterface $output)
	{
            $this->addSql('ALTER TABLE zcov2_publicites DROP aff_accueil');
	}
}