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

namespace Zco\Bundle\Doctrine1Bundle\DataCollector;

use Zco\Bundle\Doctrine1Bundle\Adapter\PDOAdapter;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Collecteur de données pour Doctrine1 et la classe d'abstraction \Db.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DoctrineDataCollector extends DataCollector
{
	/**
	 * {@inheritdoc}
	 */
	public function collect(Request $request, Response $response, \Exception $exception = null)
	{
		$dbh     = \Doctrine_Manager::connection()->getDbh();
		$queries = $dbh->getQueries();
				
		$manager     = \Doctrine_Manager::getInstance();
		$connections = array();
		foreach ($manager->getConnections() as $conn)
		{
			$connections[$conn->getName()] = $conn->getDriverName();
		}
		
		$this->data = array(
			'connexion_time' => $dbh->getConnexionTime(),
			'queries'        => $queries,
			'connections'    => $connections,
		);
	}
	
	/**
	 * Retourne le nombre total de requête.
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
		$time = $this->data['connexion_time'];
		foreach ($this->data['queries'] as $query)
		{
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
}