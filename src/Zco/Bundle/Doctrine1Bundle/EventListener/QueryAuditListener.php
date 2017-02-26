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

class QueryAuditListener extends \Doctrine_EventListener
{
    protected $queries = [];

    public function preStmtExecute(\Doctrine_Event $event)
    {
        $event->start();
    }

    public function postStmtExecute(\Doctrine_Event $event)
    {
        $this->recordQuery($event);
    }

    public function preQuery(\Doctrine_Event $event)
    {
        $event->start();
    }

    public function postQuery(\Doctrine_Event $event)
    {
        $this->recordQuery($event);
    }

    public function preExec(\Doctrine_Event $event)
    {
        $event->start();
    }

    public function postExec(\Doctrine_Event $event)
    {
        $this->recordQuery($event);
    }

    public function getQueries()
    {
        return $this->queries;
    }

    private function recordQuery(\Doctrine_Event $event)
    {
        $event->end();
        $this->queries[] = [
            'sql' => $event->getQuery(),
            'time' => $event->getElapsedSecs(),
            'params' => $event->getParams(),
        ];
    }
}