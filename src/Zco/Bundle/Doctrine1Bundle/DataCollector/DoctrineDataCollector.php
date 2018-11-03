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

namespace Zco\Bundle\Doctrine1Bundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Zco\Bundle\Doctrine1Bundle\EventListener\QueryAuditListener;

/**
 * Collecteur de données pour Doctrine1.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DoctrineDataCollector extends DataCollector
{
    private $auditListener;

    /**
     * Constructor.
     *
     * @param QueryAuditListener $auditListener Query audit listener.
     */
    public function __construct(QueryAuditListener $auditListener)
    {
        $this->auditListener = $auditListener;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $queries = $this->auditListener->getQueries();

        $manager = \Doctrine_Manager::getInstance();
        $connections = array();
        foreach ($manager->getConnections() as $conn) {
            $connections[$conn->getName()] = $conn->getDriverName();
        }

        $this->data = array(
            'queries' => $queries,
            'connections' => $connections,
        );
    }

    /**
     * Retourne le nombre total de requêtes.
     *
     * @return integer
     */
    public function getQueryCount()
    {
        return count($this->data['queries']);
    }

    /**
     * Retourne les méta-données sur les requêtes.
     *
     * @return array
     */
    public function getQueries()
    {
        return $this->data['queries'];
    }

    /**
     * Retourne le temps total d'exécution des requêtes.
     *
     * @return float
     */
    public function getTime()
    {
        $time = 0;
        foreach ($this->data['queries'] as $query) {
            $time += $query['time'];
        }

        return $time;
    }

    /**
     * Retourne les connexions actives.
     *
     * @return array
     */
    public function getConnections()
    {
        return $this->data['connections'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'doctrine1';
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->data = ['queries' => [], 'connections' => []];
    }
}