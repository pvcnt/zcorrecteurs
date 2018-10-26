<?php

namespace Zco\Bundle\MpBundle\Admin;

use Zco\Bundle\AdminBundle\PendingTask;

/**
 * Counts the number of non-resolved PM alerts.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
final class PmAlertsPendingTask implements PendingTask
{
    public function count(): int
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare('SELECT COUNT(1) FROM zcov2_mp_alertes WHERE mp_alerte_resolu = 0');
        $stmt->execute();
        $res = $stmt->fetchColumn();
        $stmt->closeCursor();

        return (int)$res;
    }

    public function getCredentials(): array
    {
        return ['mp_alertes'];
    }
}