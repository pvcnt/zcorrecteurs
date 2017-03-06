<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Add a code to each group to reference it in the code without using its identifier.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Version20170306115057 extends AbstractMigration
{
    public function up(OutputInterface $output)
    {
        $this->addSql('ALTER TABLE zcov2_groupes ADD groupe_code VARCHAR(50)');
        $this->addSql('UPDATE zcov2_groupes SET groupe_code = \'' . Groupe::ADMIN . '\' where groupe_nom = \'Administrateurs\' and groupe_secondaire = 0');
        $this->addSql('UPDATE zcov2_groupes SET groupe_code = \'' . Groupe::DEVELOPER . '\' where groupe_nom = \'Développeurs\' and groupe_secondaire = 0');
        $this->addSql('UPDATE zcov2_groupes SET groupe_code = \'' . Groupe::ANONYMOUS . '\' where groupe_nom = \'Visiteur\' and groupe_secondaire = 0');
        $this->addSql('UPDATE zcov2_groupes SET groupe_code = \'' . Groupe::SENIOR . '\' where groupe_nom = \'zAnciens\' and groupe_secondaire = 0');
        $this->addSql('UPDATE zcov2_groupes SET groupe_code = \'' . Groupe::DEFAULT . '\' where groupe_nom = \'Membres\' and groupe_secondaire = 0');

    }

    public function down(OutputInterface $output)
    {
        $this->addSql('ALTER TABLE zcov2_groupes DROP groupe_code');
    }
}