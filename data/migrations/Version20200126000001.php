<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\OutputInterface;
use Zco\Bundle\Doctrine1Bundle\Migrations\Exception\IrreversibleMigrationException;

/**
 * Supprime la fonctionnalité de chiffrement des MPs.
 *
 * @author vincent <vincent@zcorrecteurs.fr>
 */
class Version20200126000001 extends AbstractMigration
{
    public function up(OutputInterface $output)
    {
        $this->addSql('ALTER TABLE zcov2_mp_mp DROP mp_crypte');
        $this->addSql('ALTER TABLE zcov2_utilisateurs DROP utilisateur_cle_pgp');
    }

    public function down(OutputInterface $output)
    {
        throw new IrreversibleMigrationException();
    }
}