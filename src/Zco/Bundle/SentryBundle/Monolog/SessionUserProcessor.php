<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
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

namespace Zco\Bundle\SentryBundle\Monolog;

/**
 * Ajoute les données concernant l'utilisateur visitant actuellement le site 
 * aux enregistrement de Monolog.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class SessionUserProcessor
{
    /**
     * Effectue la modification de l'enregistrement Monolog.
     *
     * @param  array $record
     * @return array
     */
    public function processRecord(array $record)
    {
        if (!verifier('connecte'))
        {
            $record['extra']['user.is_authenticated'] = false;
            $record['extra']['user.id'] = isset($_SESSION['id']) ? $_SESSION['id'] : -1;
        }
        else
        {
            $record['extra']['user.is_authenticated'] = true;
            $record['extra']['user.id'] = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
            if (isset($_SESSION['pseudo']))
            {
                $record['extra']['user.username'] = $_SESSION['pseudo'];
            }
        }

        return $record;
    }
}