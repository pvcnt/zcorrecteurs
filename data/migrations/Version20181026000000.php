<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Fill the `auteur_nom` and `auteur_prenom` fields in the dictations table.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Version20181026000000 extends AbstractMigration
{
    public function up(OutputInterface $output)
    {
        $this->addSql('ALTER TABLE zcov2_dictees ADD auteur_nom VARCHAR(100)');
        $this->addSql('ALTER TABLE zcov2_dictees ADD auteur_prenom VARCHAR(100)');
        $this->addSql('UPDATE zcov2_dictees d 
            SET auteur_nom = (
              SELECT a.nom 
              FROM zcov2_auteurs a
              WHERE d.auteur_id = a.id),
            auteur_prenom = (
              SELECT a.prenom 
              FROM zcov2_auteurs a 
              WHERE d.auteur_id = a.id)');
    }

    public function down(OutputInterface $output)
    {
        $this->addSql('ALTER TABLE zcov2_dictees DROP auteur_nom');
        $this->addSql('ALTER TABLE zcov2_dictees DROP auteur_prenom');
    }
}