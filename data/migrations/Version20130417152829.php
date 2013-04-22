<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Ajoute un nouveau champ permettant de marquer l'archivage des catégories et 
 * définit toutes les catégories comme non archivées.
 * 
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Version20130417152829 extends AbstractMigration
{
    public function up(OutputInterface $output)
    {
        $this->addSql('ALTER TABLE zcov2_categories ADD cat_archive TINYINT(1)');
        $this->addSql('UPDATE zcov2_categories SET cat_archive = 0');
    }

    public function down(OutputInterface $output)
    {
        $this->addSql('ALTER TABLE zcov2_categories DROP cat_archive');
    }
}