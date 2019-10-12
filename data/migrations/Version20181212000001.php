<?php

use Symfony\Component\Console\Output\OutputInterface;
use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;

/**
 * Delete table for form backups.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Version20181212000001 extends AbstractMigration
{
    public function up(OutputInterface $output)
    {
        $this->addSql('drop table zcov2_sauvegardes_zform');
    }

    public function down(OutputInterface $output)
    {
        $this->throwIrreversibleMigrationException();
    }
}