<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\OutputInterface;
use Zco\Bundle\Doctrine1Bundle\Migrations\Exception\IrreversibleMigrationException;

/**
 * Drop unused fields in the tags table.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Version20181026000001 extends AbstractMigration
{
    public function up(OutputInterface $output)
    {
        $this->addSql('ALTER TABLE zcov2_tags DROP utilisateur_id');
        $this->addSql('ALTER TABLE zcov2_tags DROP couleur');
        $this->addSql('ALTER TABLE zcov2_tags DROP moderation');
    }

    public function down(OutputInterface $output)
    {
        throw new IrreversibleMigrationException();
    }
}