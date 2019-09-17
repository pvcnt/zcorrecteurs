<?php

namespace Zco\Bundle\UserBundle\Admin;

use Zco\Bundle\ContentBundle\Admin\PendingTask;

/**
 * Counts the number of username change requests.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
final class NewUsernamePendingTask implements PendingTask
{
    public function count(): int
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare('SELECT COUNT(1) 
            FROM zcov2_changements_pseudos 
            WHERE changement_etat = ' . CH_PSEUDO_ATTENTE);
        $stmt->execute();
        $res = $stmt->fetchColumn();
        $stmt->closeCursor();

        return (int)$res;
    }

    public function getCredentials(): array
    {
        return ['membres_valider_ch_pseudos'];
    }
}