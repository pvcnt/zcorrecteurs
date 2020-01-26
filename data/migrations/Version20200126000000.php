<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\OutputInterface;
use Zco\Bundle\Doctrine1Bundle\Migrations\Exception\IrreversibleMigrationException;

/**
 * Mise à jour de la table des utilisateurs pour enlever les absences.
 *
 * @author vincent <vincent@zcorrecteurs.fr>
 */
class Version20200126000000 extends AbstractMigration
{
    public function up(OutputInterface $output)
    {
        $this->addSql('ALTER TABLE zcov2_utilisateurs 
                            DROP utilisateur_absent, 
                            DROP utilisateur_debut_absence, 
                            DROP utilisateur_fin_absence, 
                            DROP utilisateur_motif_absence');
    }

    public function down(OutputInterface $output)
    {
        throw new IrreversibleMigrationException();
    }
}