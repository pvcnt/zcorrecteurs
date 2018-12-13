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

namespace Zco\Bundle\DicteesBundle\Domain;

class DictationScoreDAO
{
    public static function MesStatistiques()
    {
        return \Doctrine_Query::create()
            ->select('AVG(note) AS moyenne, COUNT(*) AS participations')
            ->from('Dictee_Participation dp')
            ->leftJoin('dp.Dictee d')
            ->addWhere('d.etat = ?', DICTEE_VALIDEE)
            ->addWhere('dp.utilisateur_id = ?', $_SESSION['id'])
            ->execute()
            ->offsetGet(0);
    }

    public static function DernieresNotes($nombre = 10, $offset = 0)
    {
        return \Doctrine_Query::create()
            ->select('d.id, d.titre, d.difficulte, dp.note, dp.date')
            ->from('Dictee_Participation dp')
            ->leftJoin('dp.Dictee d')
            ->addWhere('d.etat = ?', DICTEE_VALIDEE)
            ->addWhere('dp.utilisateur_id = ?', $_SESSION['id'])
            ->orderBy('dp.date DESC')
            ->limit($nombre)
            ->offset($offset)
            ->execute();
    }

    public static function FrequenceNotes()
    {
        return \Doctrine_Query::create()
            ->select('dp.note, COUNT(dp.id) AS nombre')
            ->from('Dictee_Participation dp')
            ->innerJoin('dp.Dictee d')
            ->addWhere('d.etat = ?', DICTEE_VALIDEE)
            ->addWhere('dp.utilisateur_id = ?', $_SESSION['id'])
            ->groupBy('dp.note')
            ->execute();
    }
}