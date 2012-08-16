<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Mise à jour de la table des quiz pour la nouvelle gestion des quiz aléatoires.
 *
 * @author DJ Fox <djfox@zcorrecteurs.fr>
 */
class Version20120607234636 extends AbstractMigration
{
	public function up(OutputInterface $output)
	{
            $this->addSql('ALTER TABLE zcov2_quiz CHANGE aleatoire aleatoire int(11) NOT NULL');
            $this->addSql('UPDATE zcov2_quiz SET aleatoire=10 WHERE aleatoire = 1');
        }

	public function down(OutputInterface $output)
	{
            $this->addSql('ALTER TABLE zcov2_quiz CHANGE aleatoire aleatoire tinyint(1) NOT NULL');
	}
}
