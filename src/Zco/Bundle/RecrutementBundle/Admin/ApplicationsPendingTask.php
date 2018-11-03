<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Zco\Bundle\RecrutementBundle\Admin;

use Zco\Bundle\AdminBundle\PendingTask;

/**
 * Counts the number of pending applicants.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
final class ApplicationsPendingTask implements PendingTask
{
    public function count(): int
    {
        $dbh = \Doctrine_Manager::connection()->getDbh();
        $stmt = $dbh->prepare('SELECT COUNT(*) 
            FROM zcov2_recrutements_candidatures 
            LEFT JOIN zcov2_recrutements ON candidature_id_recrutement = recrutement_id 
            WHERE recrutement_etat = ' . RECRUTEMENT_OUVERT . ' AND 
            (candidature_etat = ' . CANDIDATURE_ENVOYE . ' OR 
            candidature_etat = ' . CANDIDATURE_TESTE . ' OR  
            candidature_etat = ' . CANDIDATURE_ATTENTE_TEST . ')');
        $stmt->execute();
        $res = $stmt->fetchColumn();
        $stmt->closeCursor();

        return (int)$res;
    }

    public function getCredentials(): array
    {
        return ['recrutements_repondre'];
    }
}