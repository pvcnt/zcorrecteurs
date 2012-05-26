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

namespace Zco\Bundle\Doctrine1Bundle\EventListener;

/**
 * Garde une trace des méta-données de toutes les requêtes Doctrine 
 * exécutées. Non utilisé pour l'instant car l'adaptateur PDO le fait déjà.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class DoctrineListener extends \Doctrine_EventListener
{
	private $queries = array();
	private $times = array();
	private $stack = array();

	public function preQuery(\Doctrine_Event $event)
	{
		$this->startQuery($event);
	}

	public function postQuery(\Doctrine_Event $event)
	{
		$this->stopQuery();
	}

	public function preExecute(\Doctrine_Event $event)
	{
		$this->startQuery($event);
	}

	public function postExecute(\Doctrine_Event $event)
	{
		$this->stopQuery();
	}

	public function preStmtExecute(\Doctrine_Event $event)
	{
		$this->startQuery($event);
	}

	public function postStmtExecute(\Doctrine_Event $event)
	{
		$this->stopQuery();
	}

	public function getQueries()
	{
		return $this->queries;
	}

	public function getTimes()
	{
		return $this->times;
	}
	
	private function startQuery(\Doctrine_Event $event)
	{
	    $this->queries[] = array('sql' => $event->getQuery(), 'params' => $event->getParams());
		$this->stack[] = microtime(true);
	}
	
	private function stopQuery()
	{
	    $this->times[] = microtime(true) - array_pop($this->stack);
	}
}
