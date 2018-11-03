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

namespace Zco\Bundle\StatsBundle\Service;

/**
 * Stats Alexa
 *
 * @author mwsaz@zcorrecteurs.fr
 */
class AlexaStatsService
{
    private $conn;

    /**
     * Constructor.
     *
     * @param \Doctrine_Connection $conn
     */
    public function __construct(\Doctrine_Connection $conn)
    {
        $this->conn = $conn;
    }

    public function fetch($domain = null)
    {
        if ($domain === null) {
            $domain = URL_SITE;
        }

        $domain = parse_url($domain);
        $domain = $domain['host'];
        if (substr($domain, 0, 4) == 'www.') {
            $domain = substr($domain, 4);
        }

        $url = 'http://www.alexa.com/siteinfo/' . $domain;
        $co = file_get_contents($url);

        $ranks = array();

        // RANG MONDIAL
        $pattern = array('&#39;s three-month global Alexa traffic rank is ', '.  Search engines refer about');

        $pos = array(
            strpos($co, $pattern[0]),
            strpos($co, $pattern[1])
        );
        $start = $pos[0] + strlen($pattern[0]);
        $end = $pos[1] - $start;

        $rank = substr($co, $start, $end);
        $rank = str_replace(',', '', $rank);
        $ranks['global'] = (int)$rank;

        // RANG EN FRANCE
        $pattern = array('alt="France Flag"/>' . "\n", '              </div>');

        $pos = array(strpos($co, $pattern[0]), strpos($co, $pattern[1]));
        $start = $pos[0] + strlen($pattern[0]);
        $end = $pos[1] - $start;

        $rank = substr($co, $start, $end);
        $rank = str_replace(',', '', $rank);
        $ranks['france'] = (int)$rank;

        return $ranks;
    }

    public function fetchAndSave($domain = null)
    {
        $ranks = $this->fetch($domain);
        $stmt = $this->conn->prepare(
            'INSERT INTO zcov2_statistiques(creation, rang_global, rang_france) VALUES (CURRENT_TIMESTAMP, ?, ?)');
        $stmt->execute(array($ranks['global'], $ranks['france']));
        $stmt->closeCursor();
    }

    public function find($year, $month = null)
    {
        if ($month === null) {
            // Toute l'année
            $q = 'SELECT'
                . ' CAST(AVG(rang_global) AS UNSIGNED INTEGER) AS rang_global,'
                . ' CAST(AVG(rang_france) AS UNSIGNED INTEGER) AS rang_france,'
                . ' MONTH(creation) AS mois'
                . ' FROM zcov2_statistiques'
                . ' WHERE YEAR(creation) = ' . (int)$year
                . ' GROUP BY mois'
                . ' ORDER BY mois ASC';
        } else {
            // Un mois en particulier
            $q = 'SELECT'
                . ' CAST(AVG(rang_global) AS UNSIGNED INTEGER) AS rang_global,'
                . ' CAST(AVG(rang_france) AS UNSIGNED INTEGER) AS rang_france,'
                . ' DAY(creation) AS jour'
                . ' FROM zcov2_statistiques'
                . ' WHERE YEAR(creation) = ' . (int)$year
                . ' AND MONTH(creation) = ' . (int)$month
                . ' GROUP BY jour'
                . ' ORDER BY jour ASC';
        }

        return $this->conn->fetchAll($q);
    }
}