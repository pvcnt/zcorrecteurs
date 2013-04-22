<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Classe de migration auto-générée. Modifiez-la selon vos besoins !
 */
class Version20130417152829 extends AbstractMigration
{
    public function up(OutputInterface $output)
    {
        $this->addSql('ALTER TABLE zcov2_categories ADD cat_archive TINYINT(1)');
    }

    public function down(OutputInterface $output)
    {
        $this->addSql('ALTER TABLE zcov2_categories DROP cat_archive');
    }
}