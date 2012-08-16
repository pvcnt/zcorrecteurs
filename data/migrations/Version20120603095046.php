<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Changement sur le schéma de la table des recrutements non reflété dans 
 * le schéma de base.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Version20120603095046 extends AbstractMigration
{
	public function up(OutputInterface $output)
	{
		$this->addSql('ALTER TABLE zcov2_recrutements CHANGE recrutement_id_groupe recrutement_id_groupe int(11) NULL');
	}

	public function down(OutputInterface $output)
	{
		$this->addSql('ALTER TABLE zcov2_recrutements CHANGE recrutement_id_groupe recrutement_id_groupe int(11) NOT NULL');
	}
}