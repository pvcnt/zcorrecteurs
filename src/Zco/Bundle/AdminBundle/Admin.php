<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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

namespace Zco\Bundle\AdminBundle;

use Doctrine\Common\Cache\Cache;

/**
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
final class Admin
{
    private $cache;
    /** @var PendingTask[] */
    private $tasks = [];
    private $values = [];

    /**
     * Constructor.
     *
     * @param Cache $cache
     * @param iterable $tasks
     */
    public function __construct(Cache $cache, iterable $tasks)
    {
        $this->cache = $cache;
        foreach ($tasks as $task) {
            $this->tasks[get_class($task)] = $task;
        }
    }

    public function get(string $id)
    {
        if (!isset($this->tasks[$id])) {
            return 0;
        }

        return $this->getValue($this->tasks[$id]);
    }

    public function refresh(string $id)
    {
        $cacheKey = $this->getCacheKey($id);
        $this->cache->delete($cacheKey);
        unset($this->values[$cacheKey]);
    }

    public function refreshAll()
    {
        foreach ($this->tasks as $id => $task) {
            $this->refresh($id);
        }
    }

    public function count()
    {
        $count = 0;
        foreach ($this->tasks as $task) {
            $allowed = true;
            foreach ($task->getCredentials() as $d) {
                if (!verifier($d)) {
                    $allowed = false;
                    break;
                }
            }
            if ($allowed) {
                $count += $this->getValue($task);
            }
        }

        return $count;
    }

    private function getValue(PendingTask $task)
    {
        $cacheKey = $this->getCacheKey($task);

        if (isset($this->values[$cacheKey])) {
            return $this->values[$cacheKey];
        }

        if (($value = $this->cache->fetch($cacheKey)) === false) {
            $value = (int)$task->count();
            $this->cache->save($cacheKey, $value, 3600);
        }

        $this->values[$cacheKey] = $value;

        return $value;
    }

    private function getCacheKey($id)
    {
        if ($id instanceof PendingTask) {
            $id = get_class($id);
        }

        return 'zco.admin.' . str_replace('\\', ':', $id);
    }
}
