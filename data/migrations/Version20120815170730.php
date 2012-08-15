<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Zco\Bundle\Doctrine1Bundle\Migrations\Exception\IrreversibleMigrationException;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Classe de migration auto-générée. Modifiez-la selon vos besoins !
 */
class Version20120815170730 extends AbstractMigration
{
	public function up(OutputInterface $output)
	{
		$this->addSql('ALTER TABLE zcov2_utilisateurs_preferences DROP preference_activer_rep_rapide');
		$this->addSql('ALTER TABLE zcov2_utilisateurs_preferences DROP preference_afficher_signatures');
		$this->addSql('ALTER TABLE zcov2_utilisateurs_preferences DROP preference_temps_redirection');
		$this->addSql('ALTER TABLE zcov2_utilisateurs_preferences DROP preference_debug');

		$stmt = $this->dbh->prepare('SELECT droit_id FROM zcov2_droits WHERE droit_nom IN(
			"options_editer_absence", "options_editer_mails", 
			"options_editer_avatars", "options_editer_pass", 
			"options_editer_navigation")');
		$stmt->execute();
		$rows = $stmt->fetchAll();
		foreach ($rows as $i => $row)
		{
			$rows[$i] = $row['droit_id'];
		}

		$this->addSql('DELETE FROM zcov2_groupes_droits WHERE gd_id_droit IN('.implode(',', $rows).')');
		$this->addSql('DELETE FROM zcov2_droits WHERE droit_id IN('.implode(',', $rows).')');
	}

	public function down(OutputInterface $output)
	{
		$this->throwIrreversibleMigrationException();
	}
}