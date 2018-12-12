<?php

use Symfony\Component\Console\Output\OutputInterface;
use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;

/**
 * Delete obsolete credentials.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Version20181212000000 extends AbstractMigration
{
    public function up(OutputInterface $output)
    {
        $this->addSql('alter table zcov2_file drop license_id');
        $this->addSql('drop table zcov2_file_license');
        $this->addSql('drop table zcov2_license');
    }

    public function down(OutputInterface $output)
    {
        $this->throwIrreversibleMigrationException();
    }
}