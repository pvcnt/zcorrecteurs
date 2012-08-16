<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Mise à jour de la table des utilisateurs pour le nouveau profil.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Version20120807210107 extends AbstractMigration
{
	public function up(OutputInterface $output)
	{
		$this->addSql('ALTER TABLE zcov2_utilisateurs ADD utilisateur_twitter VARCHAR(128) NOT NULL');
		$this->addSql('ALTER TABLE zcov2_utilisateurs ADD utilisateur_display_signature TINYINT(1) NOT NULL DEFAULT 1');
		$this->addSql('ALTER TABLE zcov2_utilisateurs CHANGE utilisateur_date_naissance utilisateur_date_naissance DATE NULL');
		$this->addSql('UPDATE zcov2_utilisateurs SET utilisateur_date_naissance = NULL WHERE utilisateur_date_naissance = "0000-00-00"');
	}

	public function down(OutputInterface $output)
	{
		$this->addSql('ALTER TABLE zcov2_utilisateurs DROP utilisateur_twitter');
		$this->addSql('ALTER TABLE zcov2_utilisateurs DROP utilisateur_display_signature');
		$this->addSql('UPDATE zcov2_utilisateurs SET utilisateur_date_naissance = "0000-00-00" WHERE utilisateur_date_naissance IS NULL');
		$this->addSql('ALTER TABLE zcov2_utilisateurs CHANGE utilisateur_date_naissance utilisateur_date_naissance DATE NOT NULL');
	}
}