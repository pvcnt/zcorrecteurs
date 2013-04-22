<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Ajoute un nouveau champ permettant de modifier le ciblage des publicités en 
 * ne les affichant que sur la page d'accueil du site.
 * 
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Version20130419124959 extends AbstractMigration
{
    public function up(OutputInterface $output)
    {
        $this->addSql('ALTER TABLE zcov2_publicites ADD aff_accueil TINYINT(1)');
        //$this->addSql('DROP TABLE zcov2_publicites_categories');
    }

    public function down(OutputInterface $output)
    {
        $this->addSql('ALTER TABLE zcov2_publicites DROP aff_accueil');
    }
}